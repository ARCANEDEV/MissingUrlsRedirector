<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Helpers;

use Arcanedev\MissingUrlsRedirector\Entities\{Redirection, RedirectionCollection};
use Arcanedev\MissingUrlsRedirector\Events\RouteRedirected;
use Illuminate\Routing\{Route, RouteAction, RouteCollection};

/**
 * Class     RouteMaker
 *
 * @package  Arcanedev\MissingUrlsRedirector\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RouteMaker
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make routes collection.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection  $redirections
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public static function makeCollection(RedirectionCollection $redirections): RouteCollection
    {
        $routes = new RouteCollection;

        foreach ($redirections as $group) {
            foreach ($group as $redirection) {
                /** @var  \Arcanedev\MissingUrlsRedirector\Entities\Redirection  $redirection */
                $routes->add($route = static::makeRoute($redirection));
                $redirection->setRoute($route);
            }
        }

        return $routes;
    }

    /**
     * Make a route.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\Redirection  $redirection
     *
     * @return \Illuminate\Routing\Route
     */
    public static function makeRoute(Redirection $redirection): Route
    {
        $route = new Route(['GET', 'HEAD'], $redirection->getFrom(), []);

        return $route->setAction(
            static::parseAction($route, $redirection)
        );
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Resolve the router parameters.
     *
     * @param  \Illuminate\Routing\Route                              $route
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\Redirection  $redirection
     *
     * @return array
     */
    private static function parseAction(Route $route, Redirection $redirection): array
    {
        return RouteAction::parse($route->uri(), function () use ($redirection, $route) {
            event(new RouteRedirected($redirection));

            return redirect()->to(
                $redirection->getResolvedUrl(),
                $redirection->getStatus()
            );
        });
    }
}
