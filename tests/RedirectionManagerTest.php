<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Tests;

use Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager;
use Arcanedev\MissingUrlsRedirector\Entities\{Redirection, RedirectionCollection};
use Symfony\Component\HttpFoundation\Response;

/**
 * Class     RedirectionManagerTest
 *
 * @package  Arcanedev\MissingUrlsRedirector\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RedirectionManagerTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager */
    protected $redirector;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->redirector = $this->app->make(RedirectorManager::class);
    }

    /* -----------------------------------------------------------------
     |  Test
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated_via_contract(): void
    {
        $expectations = [
            \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorManager::class,
            \Arcanedev\MissingUrlsRedirector\MissingUrlsRedirector::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->redirector);
        }

        $redirections = $this->redirector->getRedirections();

        static::assertInstanceOf(RedirectionCollection::class, $redirections);
        static::assertCount(0, $redirections);
    }

    /** @test */
    public function it_can_register_redirections(): void
    {
        static::assertCount(0, $this->redirector->getRedirections());

        $this->redirector->register('/old-url-1', '/new-url-1', 301);

        static::assertCount(1, $this->redirector->getRedirections());

        $this->redirector->register('/old-url-2', '/new-url-2');

        static::assertCount(2, $this->redirector->getRedirections());

        static::assertRedirectionsEquals($this->redirector->getRedirections(), [
            '/new-url-1' => [
                '/old-url-1' => 301,
            ],
            '/new-url-2' => [
                '/old-url-2' => 301,
            ],
        ]);
    }

    /** @test */
    public function it_can_register_old_urls_with_same_destination_url(): void
    {
        static::assertCount(0, $this->redirector->getRedirections());

        $this->redirector->register('/old-url-1', '/new-url', Response::HTTP_MOVED_PERMANENTLY);
        $this->redirector->register('/old-url-2', '/new-url', Response::HTTP_FOUND);
        $this->redirector->register('/old-url-3', '/new-url');

        static::assertCount(1, $this->redirector->getRedirections());

        static::assertRedirectionsEquals($this->redirector->getRedirections(), [
            '/new-url' => [
                '/old-url-1' => Response::HTTP_MOVED_PERMANENTLY,
                '/old-url-2' => Response::HTTP_FOUND,
                '/old-url-3' => Response::HTTP_MOVED_PERMANENTLY,
            ],
        ]);
    }

    /** @test */
    public function it_can_register_many_old_urls(): void
    {
        $this->redirector->register([
            '/old-url-1',
            '/old-url-2',
            '/old-url-3',
        ], '/new-url');

        static::assertRedirectionsEquals($this->redirector->getRedirections(), [
            '/new-url' => [
                '/old-url-1' => Response::HTTP_MOVED_PERMANENTLY,
                '/old-url-2' => Response::HTTP_MOVED_PERMANENTLY,
                '/old-url-3' => Response::HTTP_MOVED_PERMANENTLY,
            ],
        ]);
    }

    /** @test */
    public function it_can_register_many_old_urls_with_custom_status_code(): void
    {
        $this->redirector->register([
            '/old-url-1' => Response::HTTP_MOVED_PERMANENTLY,
            '/old-url-2' => Response::HTTP_MOVED_PERMANENTLY,
            '/old-url-3',
        ], '/new-url', Response::HTTP_FOUND);

        static::assertRedirectionsEquals($this->redirector->getRedirections(), [
            '/new-url' => [
                '/old-url-1' => Response::HTTP_MOVED_PERMANENTLY,
                '/old-url-2' => Response::HTTP_MOVED_PERMANENTLY,
                '/old-url-3' => Response::HTTP_FOUND,
            ],
        ]);
    }

    /* -----------------------------------------------------------------
     |  Assertion Methods
     | -----------------------------------------------------------------
     */

    /**
     * Assert the redirections.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection  $redirections
     * @param  array                                                            $expected
     */
    protected static function assertRedirectionsEquals(RedirectionCollection $redirections, array $expected): void
    {
        $actual = $redirections->map(function (array $redirections, string $to) {
            return array_map(function (Redirection $redirection) {
                return $redirection->getStatus();
            }, $redirections);
        })->toArray();

        static::assertEquals($expected, $actual);
    }
}
