<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Tests;

use Arcanedev\MissingUrlsRedirector\{DeferredServicesProvider, MissingUrlsRedirectorServiceProvider};
use Arcanedev\MissingUrlsRedirector\Middleware\RedirectsOldUrls;
use Illuminate\Contracts\Http\Kernel;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  Arcanedev\MissingUrlsRedirector\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            MissingUrlsRedirectorServiceProvider::class,
            DeferredServicesProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app->make(Kernel::class)->pushMiddleware(RedirectsOldUrls::class);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Set the redirector provider.
     *
     * @param  string  $provider
     */
    protected function setRedirectorProvider(string $provider): void
    {
        $this->app['config']->set('missing-urls.provider', $provider);
    }

    /**
     * Set the redirected urls.
     *
     * @param  array  $urls
     */
    protected function setRedirectsUrls(array $urls): void
    {
        $this->app['config']->set('missing-urls.redirects.urls', $urls);
    }

    /**
     * Set the redirected status codes.
     *
     * @param  array  $statusCodes
     */
    protected function setRedirectsStatusCodes(array $statusCodes)
    {
        $this->app['config']->set('missing-urls.redirects.status-codes', $statusCodes);
    }
}
