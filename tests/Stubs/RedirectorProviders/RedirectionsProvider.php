<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Tests\Stubs\RedirectorProviders;

use Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider;
use Arcanedev\MissingUrlsRedirector\Entities\Redirection;
use Illuminate\Http\{Request, Response};

/**
 * Class     RedirectionsProvider
 *
 * @package  Arcanedev\MissingUrlsRedirector\Tests\Stubs\RedirectorProviders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RedirectionsProvider implements RedirectorProvider
{
    /**
     * Get the status codes.
     *
     * @return array
     */
    public function statusCodes(): array
    {
        return [
            Response::HTTP_NOT_FOUND,
        ];
    }

    /**
     * Get the redirections.
     *
     * @param  \Illuminate\Http\Request|mixed  $request
     *
     * @return \Arcanedev\MissingUrlsRedirector\Entities\Redirection[]|array
     */
    public function redirectionsFor(Request $request): array
    {
        return [
            Redirection::make('/old-url-1', '/new-url'),
            Redirection::make('/old-url-2', '/new-url', Response::HTTP_FOUND),
        ];
    }
}
