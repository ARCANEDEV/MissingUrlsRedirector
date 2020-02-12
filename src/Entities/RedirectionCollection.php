<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Entities;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

/**
 * Class     RedirectionCollection
 *
 * @package  Arcanedev\MissingUrlsRedirector\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RedirectionCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a new redirection.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int     $status
     *
     * @return \Arcanedev\MissingUrlsRedirector\Entities\Redirection
     */
    public function newRedirection(string $from, string $to, int $status): Redirection
    {
        return new Redirection($from, $to, $status);
    }

    /**
     * Add multiple redirections.
     *
     * @param  iterable  $from
     * @param  string    $to
     * @param  int       $status
     *
     * @return $this
     */
    public function addMany(iterable $from, string $to, int $status = RedirectResponse::HTTP_FOUND)
    {
        foreach ($from as $fromUrl => $fromStatus) {
            is_int($fromUrl)
                ? $this->addOne($fromStatus, $to, $status)
                : $this->addOne($fromUrl, $to, $fromStatus);
        }

        return $this;
    }

    /**
     * Add a new redirection.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int     $status
     *
     * @return $this
     */
    public function addOne(string $from, string $to, int $status = RedirectResponse::HTTP_MOVED_PERMANENTLY): self
    {
        $this->addRedirection(
            $this->newRedirection($from, $to, $status)
        );

        return $this;
    }

    /**
     * Add a redirection into the collection.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\Redirection  $redirection
     *
     * @return $this
     */
    public function addRedirection(Redirection $redirection)
    {
        $key = $redirection->getTo();

        if ($this->has($key)) {
            // Update redirection if exists
            $redirections = $this->get($key, []);
            $redirections[$redirection->getFrom()] = $redirection;
        }
        else {
            // New redirection
            $redirections = [$redirection->getFrom() => $redirection];
        }

        return $this->put($key, $redirections);
    }
}
