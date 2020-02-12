<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector;

use Arcanedev\Support\Providers\PackageServiceProvider;

/**
 * Class     MissingUrlsRedirectorServiceProvider
 *
 * @package  Arcanedev\MissingUrlsRedirector
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MissingUrlsRedirectorServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'missing-urls';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
        }
    }
}
