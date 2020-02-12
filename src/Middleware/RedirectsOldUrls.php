<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Middleware;

use Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager;
use Closure;
use Illuminate\Http\Request;

/**
 * Class     RedirectRequests
 *
 * @package  Arcanedev\MissingUrlsRedirector\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RedirectsOldUrls
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager */
    protected $manager;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * RedirectsOldUrls constructor.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager  $manager
     */
    public function __construct(RedirectorManager $manager)
    {
        $this->manager = $manager;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldRedirectResponse($response)) {
            $redirectedResponse = $this->getRedirectResponse($request);
        }

        return $redirectedResponse ?? $response;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Should redirect missing response.
     *
     * @param  \Illuminate\Http\Response  $response
     *
     * @return bool
     */
    protected function shouldRedirectResponse($response): bool
    {
        $redirectedStatusCodes = $this->manager->getRedirectedStatusCodes();

        if (empty($redirectedStatusCodes)) {
            return false;
        }

        return in_array('*', $redirectedStatusCodes)
            || in_array($response->getStatusCode(), $redirectedStatusCodes);
    }

    /**
     * Get the redirect response.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response|null
     */
    protected function getRedirectResponse(Request $request)
    {
        return $this->manager->getRedirectionFor($request);
    }
}
