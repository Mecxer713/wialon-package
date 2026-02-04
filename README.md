# Wialon SDK for Laravel and Symfony

[![Latest Stable Version](https://poser.pugx.org/mecxer713/wialon-package/v/stable)](https://packagist.org/packages/mecxer713/wialon-package)
[![License](https://poser.pugx.org/mecxer713/wialon-package/license)](https://packagist.org/packages/mecxer713/wialon-package)
[![Tests](https://github.com/mecxer713/wialon-package/actions/workflows/tests.yml/badge.svg)](https://github.com/mecxer713/wialon-package/actions/workflows/tests.yml)

A Laravel & Symfony SDK to integrate the Wialon (Gurtam) API. It handles automatic authentication (Session ID), simplifies API calls, and provides practical SSL handling for local environments.

Compatible with **Laravel 10, 11 and 12**.

## Features

- **Auto authentication**: transparent `sid` (Session ID) handling.
- **Laravel Facade**: clean syntax `Wialon::call()` / `Wialon::getUnits()`.
- **SSL handling**: automatic CA certificate download via an Artisan command.
- **Helpers**: convenience methods (units, login, etc.).
- **GET / POST support**: configurable API method.

## Installation

Install via Composer:

```bash
composer require mecxer713/wialon-package
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=wialon-config
```

## Configuration

In your `.env`:

```env
WIALON_TOKEN=your_token_here
WIALON_BASE_URL=https://hst-api.wialon.com/wialon/ajax.html
WIALON_DEFAULT_METHOD=POST
```

The published config file is `config/wialon.php`.

## Quick Usage

```php
use Mecxer\WialonPackage\Facades\Wialon;

// Auto login if needed
Wialon::login();

// Generic call
$result = Wialon::call('core/search_items', [
    'spec' => [
        'itemsType' => 'avl_unit',
        'propName'  => 'sys_name',
        'propValueMask' => '*',
        'sortType'  => 'sys_name'
    ],
    'force' => 1,
    'flags' => 1,
    'from'  => 0,
    'to'    => 0
]);

// Helper
$units = Wialon::getUnits();
```

## GET / POST Calls

Default method is `POST` (configurable via `WIALON_DEFAULT_METHOD`).

```php
// POST (recommended)
Wialon::call('core/search_items', [...], 'POST');
Wialon::callPost('core/search_items', [...]);

// GET (if needed)
Wialon::call('core/get_statistics', [...], 'GET');
Wialon::callGet('core/get_statistics', [...]);
```

## Guzzle Options

You can override HTTP options in `config/wialon.php`:

```php
'guzzle' => [
    'timeout' => 30,
    'verify' => storage_path('certif/cacert.pem'),
    // 'proxy' => 'http://user:pass@proxy:8080',
],
```

## Client Injection (non-static)

```php
use Mecxer\WialonPackage\WialonClient;

public function __construct(private WialonClient $wialon)
{
}
```

## SSL Handling

### Diagnostic Command

```bash
php artisan wialon:check --download
```

This command downloads the official certificate (if needed) and tests the API connection.  
The certificate is stored in `storage/certif/cacert.pem`.

### Manual Configuration

```php
Wialon::setVerifyPath(storage_path('certif/cacert.pem'));
```

## Error Handling

```php
try {
    $data = Wialon::call('core/search_items', [...], 'POST');
} catch (\RuntimeException $e) {
    // Handle API or HTTP errors
}
```

## Tests

```bash
vendor/bin/phpunit
```

## FAQ

- **SSL error (cURL error 77 / certificate)**  
  Run `php artisan wialon:check --download`, then confirm `storage/certif/cacert.pem` exists.

- **Access / permission errors**  
  Make sure your token has the required rights for the called `svc`.

- **GET vs POST**  
  Default method is `POST`. You can force `GET` via `Wialon::call(..., 'GET')`.

## Publishing

1. Clean the repo (do not commit `vendor/`, `.testbench/`, `.phpunit.result.cache`).
2. Push to GitHub.
3. Create a `vX.Y.Z` tag.
4. Submit the GitHub URL on Packagist.

## License

MIT
