<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Entities;

use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class     Redirection
 *
 * @package  Arcanedev\MissingUrlsRedirector\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Redirection
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    protected $from;

    /** @var  string */
    protected $to;

    /** @var  int */
    protected $status;

    /** @var  \Illuminate\Routing\Route|null */
    protected $route;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Redirection constructor.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int     $status
     */
    public function __construct(string $from, string $to, int $status)
    {
        $this->setFrom($from);
        $this->setTo($to);
        $this->setStatus($status);
    }

    /**
     * Make a new redirection.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int     $status
     *
     * @return static
     */
    public static function make(string $from, string $to, int $status = Response::HTTP_MOVED_PERMANENTLY)
    {
        return new static($from, $to, $status);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the old URL.
     *
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Set the old URL.
     *
     * @param  string  $from
     *
     * @return $this
     */
    public function setFrom(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get the new URL.
     *
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * Set the new URL.
     *
     * @param  string  $to
     *
     * @return $this
     */
    public function setTo(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the status.
     *
     * @param  int  $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the route.
     *
     * @return \Illuminate\Routing\Route|null
     */
    public function getRoute(): ?Route
    {
        return $this->route;
    }

    /**
     * Set the route.
     *
     * @param  \Illuminate\Routing\Route  $route
     *
     * @return $this
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get the resolved url.
     *
     * @return string
     */
    public function getResolvedUrl(): string
    {
        $url = $this->getTo();

        foreach ($this->getRoute()->parameters() as $key => $value) {
            $url = str_replace("{{$key}}", $value, $url);
        }

        return preg_replace('/\/{[\w-]+}/', '', $url);
    }
}
