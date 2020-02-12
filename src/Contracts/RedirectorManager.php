<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Contracts;

use Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection;
use Illuminate\Http\{Request, Response};

/**
 * Interface     RedirectorManager
 *
 * @package  Arcanedev\MissingUrlsRedirector\Contracts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface RedirectorManager
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get all the redirections.
     *
     * @return \Arcanedev\MissingUrlsRedirector\Entities\RedirectionCollection
     */
    public function getRedirections(): RedirectionCollection;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register a redirection.
     *
     * @param  string|iterable  $from
     * @param  string           $to
     * @param  int              $status
     */
    public function register($from, string $to, int $status = Response::HTTP_MOVED_PERMANENTLY);

    /**
     * Get the redirection for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response|null
     */
    public function getRedirectionFor(Request $request);

    /**
     * Get the redirected status codes.
     *
     * @return array|null
     */
    public function getRedirectedStatusCodes(): ?array;

    /**
     * Set the redirector.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider  $redirector
     *
     * @return $this
     */
    public function setRedirector(RedirectorProvider $redirector);
}
