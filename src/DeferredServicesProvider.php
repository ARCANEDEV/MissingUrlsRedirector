<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector;

use Illuminate\Contracts\Foundation\Application;
use Arcanedev\MissingUrlsRedirector\Contracts\{RedirectorManager, RedirectorProvider};
use Arcanedev\Support\Providers\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class     DeferredServicesProvider
 *
 * @package  Arcanedev\MissingUrlsRedirector
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DeferredServicesProvider extends ServiceProvider implements DeferrableProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        $this->bind(RedirectorProvider::class, function (Application $app) {
            return $app->make($app['config']->get('missing-urls.provider'));
        });
        $this->singleton(RedirectorManager::class, MissingUrlsRedirector::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            RedirectorManager::class,
            RedirectorProvider::class,
        ];
    }
}
