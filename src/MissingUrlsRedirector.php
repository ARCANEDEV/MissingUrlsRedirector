<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector;

use Arcanedev\MissingUrlsRedirector\Contracts\{RedirectorManager, RedirectorProvider};
use Arcanedev\MissingUrlsRedirector\Entities\{Redirection, RedirectionCollection};
use Arcanedev\MissingUrlsRedirector\Events\RedirectionNotFound;
use Arcanedev\MissingUrlsRedirector\Helpers\RouteMaker;
use Arcanedev\MissingUrlsRedirector\RedirectProviders\ConfigProvider;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class     MissingUrlsRedirector
 *
 * @package  Arcanedev\MissingUrlsRedirector
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MissingUrlsRedirector implements RedirectorManager
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider|mixed */
    protected $redirector;

    /**
     * @var \Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection
     */
    protected $redirections;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * RedirectionManager constructor.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider  $redirector
     */
    public function __construct(RedirectorProvider $redirector)
    {
        $this->setRedirector($redirector);
        $this->setRedirections(new RedirectionCollection);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the redirections.
     *
     * @return \Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection
     */
    public function getRedirections(): RedirectionCollection
    {
        return $this->redirections;
    }

    /**
     * Set the redirections.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection  $redirections
     *
     * @return $this
     */
    public function setRedirections(RedirectionCollection $redirections): self
    {
        $this->redirections = $redirections;

        return $this;
    }

    /**
     * Get the redirected status codes.
     *
     * @return array|null
     */
    public function getRedirectedStatusCodes(): ?array
    {
        return $this->getRedirector()->statusCodes();
    }

    /**
     * Get the redirector.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider|mixed  $redirector
     *
     * @return $this
     */
    public function setRedirector(RedirectorProvider $redirector): self
    {
        $this->redirector = $redirector;

        return $this;
    }

    /**
     * Get the redirector.
     *
     * @return \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider|mixed
     */
    protected function getRedirector(): RedirectorProvider
    {
        return $this->redirector ?? new ConfigProvider;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register a redirection.
     *
     * @param  iterable|string  $from
     * @param  string           $to
     * @param  int              $status
     *
     * @return $this
     */
    public function register($from, string $to, int $status = Response::HTTP_MOVED_PERMANENTLY): self
    {
        if (is_string($from)) {
            $this->getRedirections()->addOne($from, $to, $status);
        }

        if (is_iterable($from)) {
            $this->getRedirections()->addMany($from, $to, $status);
        }

        return $this;
    }

    /**
     * Register a redirection (entity).
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\Redirection  $redirection
     *
     * @return $this
     */
    public function registerRedirection(Redirection $redirection)
    {
        $this->getRedirections()->addRedirection($redirection);

        return $this;
    }

    /**
     * Get the redirection for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response|null
     */
    public function getRedirectionFor(Request $request)
    {
        $this->loadRedirections(
            $this->getRedirector()->redirectionsFor($request)
        );

        $routes = RouteMaker::makeCollection($this->getRedirections());

        try {
            return $routes->match($request)->run();
        }
        catch (NotFoundHttpException $e) {
            event(new RedirectionNotFound($request));
        }

        return null;
    }

    /**
     * Load the redirections collection.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\Redirection[]|array  $redirections
     *
     * @return $this
     */
    private function loadRedirections(array $redirections): self
    {
        foreach ($redirections as $from => $redirection) {
            if ($redirection instanceof Redirection)
                $this->registerRedirection($redirection);
            else
                $this->register($from, ...Arr::wrap($redirection));
        }

        return $this;
    }
}
