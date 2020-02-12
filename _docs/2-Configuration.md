# 2. Configuration

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

```php
<?php

return [

    /* -----------------------------------------------------------------
     |  Provider
     | -----------------------------------------------------------------
     */
    
    /*
     * This is the class responsible for providing the URLs which must be redirected.
     * The only requirement for the redirector is that it needs to implement the
     * `Arcanedev\MissingUrlsRedirector\Contracts\RedirectorProvider` interface
     */

    'provider'  => Arcanedev\MissingUrlsRedirector\RedirectProviders\ConfigProvider::class,

    /* -----------------------------------------------------------------
     |  Redirects
     | -----------------------------------------------------------------
     */
    
    'redirects' => [

        /*
         * By default the package will only redirect 404s. If you want to redirect on other
         * response codes, just add them to the array. Leave the array empty to redirect
         * always no matter what the response code.
         */

        'status-codes' => [
            Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
        ],

        /*
         * When using the `ConfigProvider` you can specify the redirects in this array.
         * You can use Laravel's route parameters here.
         */

        'urls'   => [
//            '/non-existing-page' => '/existing-page',
//            '/old-blog/{url}' => '/new-blog/{url}',
//            '/old-url' => ['/new-url', 302],
        ],

    ],

];
```
