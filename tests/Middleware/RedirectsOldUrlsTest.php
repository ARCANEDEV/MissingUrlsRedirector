<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Tests\Middleware;

use Arcanedev\MissingUrlsRedirector\Events\RedirectionNotFound;
use Arcanedev\MissingUrlsRedirector\Events\RouteRedirected;
use Arcanedev\MissingUrlsRedirector\Tests\Stubs\RedirectorProviders\RedirectionsProvider;
use Arcanedev\MissingUrlsRedirector\Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;

/**
 * Class     RedirectsOldUrlsTest
 *
 * @package  Arcanedev\MissingUrlsRedirector\Tests\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RedirectsOldUrlsTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpRoutes($this->app['router']);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_should_not_interfere_with_existing_pages(): void
    {
        $this->get('existing-page')
             ->assertSuccessful()
             ->assertSee('Existing page');
    }

    /** @test */
    public function it_will_redirect_a_non_existing_page_with_a_permanent_redirect(): void
    {
        $this->setRedirectsUrls([
            '/non-existing-page' => '/existing-page',
        ]);

        $this->get('non-existing-page')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/existing-page');
    }

    /** @test */
    public function it_can_use_named_parameters(): void
    {
        $this->setRedirectsUrls([
            '/segment1/{id}/segment2/{slug}' => '/segment2/{slug}',
        ]);

        $this->get('/segment1/123/segment2/abc')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/segment2/abc');
    }

    /** @test */
    public function it_can_use_multiple_named_parameters_in_one_segment(): void
    {
        $this->setRedirectsUrls([
            '/new-segment/{id}-{slug}' => '/new-segment/{id}/',
        ]);

        $this->get('/new-segment/123-blablabla')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/new-segment/123');
    }

    /** @test */
    public function it_can_optionally_set_the_redirect_status_code(): void
    {
        $this->setRedirectsUrls([
            '/temporarily-moved' => ['/just-for-now', Response::HTTP_FOUND],
        ]);

        $this->get('/temporarily-moved')
             ->assertStatus(Response::HTTP_FOUND)
             ->assertRedirect('/just-for-now');
    }

    /** @test */
    public function it_can_use_optional_parameters(): void
    {
        $this->setRedirectsUrls([
            '/old-segment/{parameter1?}/{parameter2?}' => '/new-segment/{parameter1}/{parameter2}',
        ]);

        $this->get('/old-segment')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/new-segment');

        $this->get('/old-segment/old-segment2')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/new-segment/old-segment2');

        $this->get('/old-segment/old-segment2/old-segment3')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/new-segment/old-segment2/old-segment3');
    }

    /** @test */
    public function it_will_not_redirect_requests_that_are_not_404s_by_default(): void
    {
        $this->get('/error/500')
             ->assertStatus(500);
    }

    /** @test */
    public function it_will_fire_an_event_when_a_route_is_hit(): void
    {
        Event::fake();

        $this->setRedirectsUrls([
            '/old-segment/{parameter1?}/{parameter2?}' => '/new-segment/',
        ]);

        $this->get('/old-segment')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/new-segment');

        Event::assertDispatched(RouteRedirected::class, function (RouteRedirected $event) {
            $redirection = $event->redirection;

            return $redirection->getFrom() === '/old-segment/{parameter1?}/{parameter2?}'
                && $redirection->getTo() === '/new-segment/'
                && $redirection->getStatus() === Response::HTTP_MOVED_PERMANENTLY
                && $redirection->getResolvedUrl() === '/new-segment/';
        });
    }

    /** @test */
    public function it_will_redirect_depending_on_redirect_status_codes_defined(): void
    {
        $this->setRedirectsStatusCodes($statusCodes = [
            Response::HTTP_I_AM_A_TEAPOT,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $this->setRedirectsUrls([
            '/error/418' => '/existing-page',
            '/error/500' => '/existing-page',
        ]);

        foreach ($statusCodes as $statusCode) {
            $this->get("/error/{$statusCode}")
                 ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
                 ->assertRedirect('/existing-page');
        }
    }

    /** @test */
    public function it_will_not_redirect_if_the_status_code_is_not_specified_in_the_config_file(): void
    {
        $this->setRedirectsStatusCodes([
            Response::HTTP_I_AM_A_TEAPOT,
            Response::HTTP_FORBIDDEN,
        ]);

        $this->setRedirectsUrls([
            '/error/500' => '/existing-page',
        ]);

        $this->get('/error/500')
             ->assertStatus(500);
    }

    /** @test */
    public function it_will_redirect_on_any_status_code(): void
    {
        $this->setRedirectsStatusCodes(['*']);
        $this->setRedirectsUrls([
            '/error/418' => '/existing-page',
        ]);

        $this->get('/error/418')
             ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
             ->assertRedirect('/existing-page');
    }

    /** @test */
    public function it_will_not_redirect_on_empty_status_code(): void
    {
        $this->setRedirectsStatusCodes([]);
        $this->setRedirectsUrls([
            '/error/418' => '/existing-page',
        ]);

        $this->get('/error/418')
             ->assertStatus(Response::HTTP_I_AM_A_TEAPOT);
    }

    /** @test */
    public function it_will_fire_an_event_when_no_redirect_was_found(): void
    {
        Event::fake();

        $this->get('/error/404')
             ->assertNotFound();

        Event::assertDispatched(RedirectionNotFound::class);
    }

    /**
     * @test
     *
     * @dataProvider providesRedirectionsFromCustomProvider
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int     $statusCode
     */
    public function it_can_use_custom_redirection_entities_provider(string $from, string $to, int $statusCode)
    {
        $this->setRedirectorProvider(RedirectionsProvider::class);

        $this->get($from)
             ->assertStatus($statusCode)
             ->assertRedirect($to);
    }

    /**
     * @return array
     */
    public function providesRedirectionsFromCustomProvider(): array
    {
        return [
            ['/old-url-1', '/new-url', Response::HTTP_MOVED_PERMANENTLY],
            ['/old-url-2', '/new-url', Response::HTTP_FOUND]
        ];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the routes for tests.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    private function setUpRoutes($router): void
    {
        $router->get('/existing-page', function () {
            return 'Existing page';
        });

        $router->get('error/{responseCode}', function (int $responseCode) {
            abort($responseCode);
        });
    }
}
