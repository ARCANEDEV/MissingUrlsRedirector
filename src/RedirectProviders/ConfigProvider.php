<?php

declare(strict_types=1);

namespace Arcanedev\MissingUrlsRedirector\RedirectProviders;

use Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider;
use Illuminate\Contracts\Config\Repository;

/**
 * Class     ConfigProvider
 *
 * @package  Arcanedev\MissingUrlsRedirector\RedirectProviders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ConfigProvider implements RedirectorProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Contracts\Config\Repository */
    protected $config;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * ConfigProvider constructor.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the status codes.
     *
     * @return array
     */
    public function statusCodes(): array
    {
        return (array) $this->config->get('missing-urls.redirects.status-codes');
    }

    /**
     * Get redirections for the given request.
     *
     * @param  \Illuminate\Http\Request|mixed  $request
     *
     * @return \Arcanedev\MissingUrlsRedirector\Entities\Redirection[]|array
     */
    public function redirectionsFor($request): array
    {
        return (array) $this->config->get('missing-urls.redirects.urls');
    }
}
