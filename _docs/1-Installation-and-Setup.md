# 1. Installation & Setup

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)
 
## Version Compatibility

| LogViewer                                                      | Laravel                      |
|:---------------------------------------------------------------|:-----------------------------|
| ![Missing Urls Redirector v4.3.x][missing_urls_redirector_1_x] | ![Laravel v6.x][laravel_6_x] |

[laravel_6_x]:  https://img.shields.io/badge/v6.x-supported-brightgreen.svg?style=flat-square "Laravel v6.x"

[missing_urls_redirector_1_x]: https://img.shields.io/badge/version-1.x-blue.svg?style=flat-square "Missing Urls Redirector v1.x"

## Composer

You can install this package via [Composer](http://getcomposer.org/) by running this command: 

```bash
composer require arcanedev/missing-urls-redirector
```

**OR**

`composer require arcanedev/missing-urls-redirector:{x.x}` where **x.x** is the version compatible with your laravel's version. 

E.g `composer require arcanedev/missing-urls-redirector:~4.6.0` for Laravel **v5.7**.

See the [Version compatibility](#version-compatibility) table above to choose the correct version.

## Laravel

### Setup

> **NOTE :** The package will automatically register itself if you're using Laravel `>= v5.5`, so you can skip this section.

Once the package is installed, you can register the service provider in `config/app.php` in the `providers` array:

```php
'providers' => [
    ...
    Arcanedev\MissingUrlsRedirector\MissingUrlsRedirectorServiceProvider::class,
],
```

### Artisan commands

To publish the config file, run this command:

```bash
php artisan vendor:publish --provider="Arcanedev\MissingUrlsRedirector\MissingUrlsRedirectorServiceProvider"
```
