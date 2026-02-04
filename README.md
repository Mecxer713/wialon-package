# Wialon SDK for Laravel

[![Latest Stable Version](https://poser.pugx.org/mecxer713/wialon-package/v/stable)](https://packagist.org/packages/mecxer713/wialon-package)
[![License](https://poser.pugx.org/mecxer713/wialon-package/license)](https://packagist.org/packages/mecxer713/wialon-package)
[![Tests](https://github.com/mecxer713/wialon-package/actions/workflows/tests.yml/badge.svg)](https://github.com/mecxer713/wialon-package/actions/workflows/tests.yml)

Un SDK Laravel et Symfony pour intégrer l'API Wialon (Gurtam). Il gère l'authentification automatique (Session ID), simplifie les appels API, et propose une gestion pratique des certificats SSL en local (Windows/Laragon).

Compatible avec **Laravel 10, 11 et 12**.

## Fonctionnalités

- **Authentification auto** : gestion transparente du `sid` (Session ID).
- **Façade Laravel** : syntaxe élégante `Wialon::call()` / `Wialon::getUnits()`.
- **Gestion SSL** : téléchargement et configuration automatique des certificats CA via une commande Artisan.
- **Helpers** : méthodes pratiques (unités, login, etc.).
- **Support GET / POST** : appels API configurables par méthode.

## Installation

Installez le package via Composer :

```bash
composer require mecxer713/wialon-package
```

Publiez la configuration (optionnel) :

```bash
php artisan vendor:publish --tag=wialon-config
```

## Configuration

Dans votre `.env` :

```env
WIALON_TOKEN=your_token_here
WIALON_BASE_URL=https://hst-api.wialon.com/wialon/ajax.html
WIALON_DEFAULT_METHOD=POST
```

Le fichier de config publié est `config/wialon.php`.

## Utilisation rapide

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

## Appels GET / POST

Par défaut, la méthode est `POST` (configurable via `WIALON_DEFAULT_METHOD`).

```php
// POST (recommandé)
Wialon::call('core/search_items', [...], 'POST');
Wialon::callPost('core/search_items', [...]);

// GET (si besoin)
Wialon::call('core/get_statistics', [...], 'GET');
Wialon::callGet('core/get_statistics', [...]);
```

## Options Guzzle

Vous pouvez surcharger les options HTTP via `config/wialon.php` :

```php
'guzzle' => [
    'timeout' => 30,
    'verify' => storage_path('certif/cacert.pem'),
    // 'proxy' => 'http://user:pass@proxy:8080',
],
```

## Injection du client (non-statique)

```php
use Mecxer\WialonPackage\WialonClient;

public function __construct(private WialonClient $wialon)
{
}
```

## Gestion SSL

### Commande de diagnostic

```bash
php artisan wialon:check --download
```

Cette commande télécharge le certificat officiel (si nécessaire) et teste la connexion à l'API.  
Le certificat est stocké dans `storage/certif/cacert.pem`.

### Configuration manuelle

```php
Wialon::setVerifyPath(storage_path('certif/cacert.pem'));
```

## Gestion des erreurs

```php
try {
    $data = Wialon::call('core/search_items', [...], 'POST');
} catch (\RuntimeException $e) {
    // Gérer l'erreur API ou HTTP
}
```

## Tests

```bash
vendor/bin/phpunit
```

## FAQ

- **Erreur SSL (cURL error 77 / certificate)**  
  Lance `php artisan wialon:check --download`, puis vérifie que `storage/certif/cacert.pem` existe.

- **Erreur d’accès / permissions Wialon**  
  Vérifie que ton token a les droits nécessaires pour le service `svc` appelé.

- **GET vs POST**  
  Par défaut, la méthode est `POST`. Tu peux forcer `GET` via `Wialon::call(..., 'GET')`.

## Publication

1. Nettoyez le repo (ne pas versionner `vendor/`, `.testbench/`, `.phpunit.result.cache`).
2. Poussez sur GitHub.
3. Créez un tag `vX.Y.Z`.
4. Soumettez l'URL GitHub sur Packagist.

## Licence

MIT
