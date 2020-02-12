<?php

namespace Arcanedev\MissingUrlsRedirector\Tests;

use Arcanedev\MissingUrlsRedirector\DeferredServicesProvider;

/**
 * Class     DeferredServicesProviderTest
 *
 * @package  Arcanedev\MissingUrlsRedirector\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DeferredServicesProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\MissingUrlsRedirector\DeferredServicesProvider */
    protected $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(DeferredServicesProvider::class);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Illuminate\Contracts\Support\DeferrableProvider::class,
            DeferredServicesProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides(): void
    {
        $expected = [
            \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager::class,
            \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider::class,
        ];

        static::assertEquals($expected, $this->provider->provides());
    }
}
