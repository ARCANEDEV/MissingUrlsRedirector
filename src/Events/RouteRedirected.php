<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Events;

use Arcanedev\MissingUrlsRedirector\Entities\Redirection;

/**
 * Class     RouteRedirected
 *
 * @package  Arcanedev\MissingUrlsRedirector\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RouteRedirected
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\MissingUrlsRedirector\Entities\Redirection */
    public $redirection;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * RouteRedirected constructor.
     *
     * @param  \Arcanedev\MissingUrlsRedirector\Entities\Redirection  $redirection
     */
    public function __construct(Redirection $redirection)
    {
        $this->redirection = $redirection;
    }
}
