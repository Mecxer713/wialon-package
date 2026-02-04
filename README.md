# Wialon SDK for Laravel

[![Latest Stable Version](https://poser.pugx.org/mecxer713/wialon-package/v/stable)](https://packagist.org/packages/mecxer713/wialon-package)
[![License](https://poser.pugx.org/mecxer713/wialon-package/license)](https://packagist.org/packages/mecxer713/wialon-package)

Un package Laravel pour intégrer l'API Wialon (Gurtam). Il gère l'authentification automatique (Session ID), des appels API simples et une gestion pragmatique des certificats SSL en local (Windows/Laragon).

Compatible avec **Laravel 10, 11 et 12**.

## Fonctionnalités

- **Authentification auto** : gestion transparente du `sid` (Session ID).
- **Façade Laravel** : syntaxe élégante `Wialon::getUnits()`.
- **Gestion SSL** : téléchargement et configuration automatique des certificats CA via une commande Artisan.
- **Helpers** : méthodes courantes pour les unités, le login, etc.

## Installation

Installez le package via Composer :

```bash
composer require mecxer713/wialon-package
```

Publiez la configuration (optionnel) :

```bash
php artisan vendor:publish --tag=wialon-config
```

Configurez votre `.env` :

```env
WIALON_TOKEN=your_token_here
WIALON_BASE_URL=https://hst-api.wialon.com/wialon/ajax.html
```

## Utilisation

```php
use Mecxer\WialonPackage\Facades\Wialon;

// Login automatique si nécessaire
Wialon::login();

// Appel générique
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

## Usage avancée

### Options Guzzle

Vous pouvez surcharger les options HTTP via `config/wialon.php` :

```php
'guzzle' => [
    'timeout' => 30,
    'verify' => storage_path('certif/cacert.pem'),
    // 'proxy' => 'http://user:pass@proxy:8080',
],
```

### Injection du client

Vous pouvez injecter le client directement pour un usage non-statique :

```php
use Mecxer\WialonPackage\WialonClient;

public function __construct(private WialonClient $wialon)
{
}
```

### Gestion SSL manuelle

```php
Wialon::setVerifyPath(storage_path('certif/cacert.pem'));
```

### Gestion des erreurs

```php
try {
    $data = Wialon::call('core/search_items', [...]);
} catch (\RuntimeException $e) {
    // Gérer l'erreur API ou HTTP
}
```

## Commande de diagnostic SSL

```bash
php artisan wialon:check --download
```

Cette commande télécharge le certificat officiel (si nécessaire) et teste la connexion à l'API.
Le certificat est stocké dans `storage/certif/cacert.pem`.

## Tests

```bash
vendor/bin/phpunit
```
