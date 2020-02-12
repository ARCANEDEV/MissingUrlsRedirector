# 3. Usage

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

## Usage

Creating an url redirect is easy. You just have to add an entry to the `redirects.urls` key in the `config/missing-urls.php` config file.

```php
<?php
// config/missing-urls.php

return [
    // ...

    'redirects' => [
        // ...

        'urls'   => [
            '/non-existing-page' => '/existing-page',
        ],
    ],
];
```

You may use route parameters like you're used to when using Laravel's routes:

```php
<?php
// config/missing-urls.php

return [
    // ...

    'redirects' => [
        // ...

        'urls'   => [
            '/old-blog/{url}' => '/new-blog/{url}',
            '/old-url/{url?}' => '/new-url/{url?}', // Optional parameters
        ],
    ],
];
```

By default it only redirects if the request has a `404` response code but it's possible to be redirected on any response code.

To achieve this you may change the `redirects.status-codes` option to an array of response codes or `"*"` if you wish to redirect no matter what the response code was sent to the URL.

You may override this using the following syntax to achieve this:

```php
<?php
// config/missing-urls.php

return [
    // ...

    'redirects' => [
        'status-codes'   => [
            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
        ],

        // ...
    ],
];
```

It is also possible to optionally specify which http response code is used when performing the redirect.

By default the `301 Moved Permanently` response code is set. You may override this using the following syntax:

```php
<?php
// config/missing-urls.php

return [
    // ...

    'redirects' => [
        // ...

        'urls'   => [
            'old-page' => ['/new-page', 302],
        ],
    ],
];
```

## Events

The package will fire a `Arcanedev\MissingUrlsRedirector\Events\RouteRedirected` event when it found a redirect for the route.

A `Arcanedev\MissingUrlsRedirector\Events\RedirectionNotFound` is fired when no redirect was found.

## Creating your own redirector

By default this package will use the `Arcanedev\MissingUrlsRedirector\RedirectorProviders\ConfigProvider` which will get its redirects from the config file.

If you want to use another source for your redirects (for example a database/eloquent) you can create your own redirector.

A valid redirector is any class that implements the `Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider` interface. That interface looks like this:

```php
namespace Arcanedev\MissingUrlsRedirector\Contracts;

use Symfony\Component\HttpFoundation\Request;

interface RedirectorProvider
{
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
     * @return array
     */
    public function redirectionsFor(Request $request): array;
}
```

The `statusCodes` method should return an array of allowed status codes in which the middleware will use to redirect the missing/aborted responses.

The `redirectionsFor` method should return an array in which the keys are the old URLs and the values the new URLs.

You can also use `Arcanedev\MissingUrlsRedirector\Entities\Redirection` class to create a redirection entity.

**Example:**

```php
<?php

namespace App\RedirectorProviders;

use Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider;
use Arcanedev\MissingUrlsRedirector\Entities\Redirection;
use Illuminate\Http\{Request, Response};

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
     * @return \Arcanedev\MissingUrlsRedirector\Entities\Redirection[]
     */
    public function redirectionsFor(Request $request): array
    {
        return [
            Redirection::make('/old-url-1', '/new-url'),
            Redirection::make('/old-url-2', '/new-url', Response::HTTP_FOUND),
        ];
    }
}
```
