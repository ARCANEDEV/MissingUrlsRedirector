<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Contracts;

use Illuminate\Http\Request;

/**
 * Interface     RedirectorProvider
 *
 * @package  Arcanedev\MissingUrlsRedirector\Contracts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface RedirectorProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the status codes.
     *
     * @return array
     */
    public function statusCodes(): array;

    /**
     * Get the redirections.
     *
     * @param  \Illuminate\Http\Request|mixed  $request
     *
     * @return \Arcanedev\MissingUrlsRedirector\Entities\Redirection[]|array
     */
    public function redirectionsFor(Request $request): array;
}
