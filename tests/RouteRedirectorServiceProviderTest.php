<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Tests;

use Arcanedev\MissingUrlsRedirector\MissingUrlsRedirectorServiceProvider;

/**
 * Class     RouteRedirectorServiceProviderTest
 *
 * @package  Arcanedev\MissingUrlsRedirector\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RouteRedirectorServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $provider = $this->app->getProvider(MissingUrlsRedirectorServiceProvider::class);

        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Arcanedev\Support\Providers\ServiceProvider::class,
            \Arcanedev\Support\Providers\PackageServiceProvider::class,
            MissingUrlsRedirectorServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $provider);
        }
    }
}
