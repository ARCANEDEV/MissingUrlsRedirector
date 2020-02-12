<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\Events;

/**
 * Class     RedirectionNotFound
 *
 * @package  Arcanedev\MissingUrlsRedirector\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RedirectionNotFound
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Http\Request */
    public $request;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * RedirectionNotFound constructor.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }
}
