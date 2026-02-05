# Wialon SDK for Laravel & Symfony

> 🚀 A **developer-first**, production-ready SDK to integrate the **Wialon (Gurtam) API** into Laravel & Symfony — with zero-auth pain, zero-SSL pain, and a clean architecture you’ll actually enjoy using.

---

## 📚 Table of Contents

- [Wialon SDK for Laravel \& Symfony](#wialon-sdk-for-laravel--symfony)
  - [📚 Table of Contents](#-table-of-contents)
  - [Overview](#overview)
  - [✨ Key Highlights](#-key-highlights)
  - [📦 Package Identity](#-package-identity)
    - [2️⃣ Laravel Integration Layer](#2️⃣-laravel-integration-layer)
    - [3️⃣ Symfony Integration Layer](#3️⃣-symfony-integration-layer)
  - [📥 Installation](#-installation)
    - [Laravel](#laravel)
    - [Symfony](#symfony)
  - [⚙️ Configuration](#️-configuration)
    - [Environment variables](#environment-variables)
  - [🚀 Quick Usage](#-quick-usage)
    - [Laravel (Facade)](#laravel-facade)
    - [Symfony (Dependency Injection)](#symfony-dependency-injection)
  - [🌐 Generic API Calls](#-generic-api-calls)
  - [🔁 GET vs POST](#-get-vs-post)
  - [🔐 SSL \& Certificate Handling (Killer Feature)](#-ssl--certificate-handling-killer-feature)
    - [Automatic Fix](#automatic-fix)
  - [🧪 Error Handling](#-error-handling)
  - [✅ Tests](#-tests)
  - [📌 Why This Package?](#-why-this-package)
  - [📜 License](#-license)

---

## Overview

**Wialon SDK for Laravel & Symfony** is a modern, open-source PHP SDK designed to make Wialon API integration **simple, reliable, and enjoyable**.

It abstracts away the two biggest pain points developers face when working with Wialon:

* 🔐 **Session management (SID)** — handled automatically, transparently, and safely
* 🔒 **SSL & cURL certificate errors** — fixed out of the box, even on Windows

Built around a **pure PHP core** and wrapped with **native Laravel & Symfony integrations**, this package lets you focus on business logic instead of boilerplate, edge cases, and environment issues.

Whether you are building a **fleet management system**, an **internal dashboard**, or a **production SaaS**, this SDK is designed to scale with confidence.

---

## ✨ Key Highlights

* 🔐 **Zero-config authentication**: automatic session (`sid`) lifecycle management
* 🧠 **Pure PHP core**: reusable, testable, framework-independent logic
* 🧩 **Native integrations**:

  * Laravel Facade & Artisan command
  * Symfony Bundle, DI & Console command
* 🛠 **Built-in SSL fixer**: automatic CA certificate download (cURL 60/77)
* 🚀 **Developer Experience (DX) focused**

---

## 📦 Package Identity

* **Name**: `mecxer713/wialon-package`
* **Type**: Hybrid SDK (Laravel & Symfony)
* **Core dependencies**:

  * `guzzlehttp/guzzle`
  * `illuminate/support` (Laravel)
  * `symfony/http-kernel` (Symfony)
* **Compatibility**:

  * Laravel **10, 11, 12**
  * Symfony **6, 7**

---

### 2️⃣ Laravel Integration Layer

Designed to feel 100% native to Laravel developers.

Components:

* `WialonServiceProvider` – container binding & config publishing
* `Facades/Wialon.php` – static access (`Wialon::call()`)
* `Commands/TestWialonConnection.php` – Artisan diagnostic command
* `config/wialon.php` – Laravel configuration

---

### 3️⃣ Symfony Integration Layer

Delivered as a standard Symfony Bundle.

Components:

* `WialonBundle.php` – bundle entry point
* `DependencyInjection/WialonExtension.php` – service injection
* `DependencyInjection/Configuration.php` – YAML config validation
* `Commands/WialonCheckCommand.php` – Console diagnostic command

---

## 📥 Installation

```bash
composer require mecxer713/wialon-package
```

### Laravel

```bash
php artisan vendor:publish --tag=wialon-config
```

### Symfony

Enable the bundle (if not using Flex auto-discovery):

```php
// config/bundles.php
Mecxer\WialonPackage\WialonBundle::class => ['all' => true],
```

---

## ⚙️ Configuration

### Environment variables

```env
WIALON_TOKEN=your_token_here
WIALON_BASE_URL=https://hst-api.wialon.com/wialon/ajax.html
WIALON_DEFAULT_METHOD=POST
```

* Laravel: `config/wialon.php`
* Symfony: `config/packages/wialon.yaml`

---

## 🚀 Quick Usage

### Laravel (Facade)

```php
use Mecxer\WialonPackage\Facades\Wialon;

Wialon::login();

$units = Wialon::getUnits();
```

### Symfony (Dependency Injection)

```php
use Mecxer\WialonPackage\WialonClient;

public function index(WialonClient $wialon)
{
    $units = $wialon->getUnits();
}
```

---

## 🌐 Generic API Calls

```php
Wialon::call('core/search_items', [
    'spec' => [
        'itemsType' => 'avl_unit',
        'propName' => 'sys_name',
        'propValueMask' => '*',
        'sortType' => 'sys_name'
    ],
    'force' => 1,
    'flags' => 1,
    'from' => 0,
    'to' => 0
]);
```

---

## 🔁 GET vs POST

```php
// POST (default & recommended)
Wialon::callPost('core/search_items', [...]);

// GET (optional)
Wialon::callGet('core/get_statistics', [...]);
```

---

## 🔐 SSL & Certificate Handling (Killer Feature)

### Automatic Fix

```bash
php artisan wialon:check --download
# or
php bin/console wialon:check --download
```

✔ Downloads a valid Mozilla CA bundle
✔ Fixes cURL error 60 / 77 (Windows)
✔ Tests API connectivity

Certificate location:

```
storage/certif/cacert.pem
```

---

## 🧪 Error Handling

```php
try {
    $data = Wialon::getUnits();
} catch (\RuntimeException $e) {
    // Handle HTTP or API errors
}
```

---

## ✅ Tests

```bash
vendor/bin/phpunit
```

---

## 📌 Why This Package?

✔ One SDK for **Laravel & Symfony**
✔ No SID headaches
✔ No SSL headaches
✔ Clean architecture
✔ Production-ready

---

## 📜 License

MIT
