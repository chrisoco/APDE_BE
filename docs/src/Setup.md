# Laravel Setup Documentation

## Übersicht

Diese Dokumentation beschreibt Schritt für Schritt, wie die **serverseitige Basis-Applikation** für das Hotel Grand Pilatus E-Mail Campaign Management System aufgebaut wurde. Es handelt sich um eine management-orientierte Anleitung der implementierten Schritte.

## Teil 1: Projekt-Setup und Tooling

### 1.1 Laravel 12 Projekt erstellt

Das Projekt wurde mit Laravel 12 und PHP 8.4 initialisiert:

```bash
# Laravel 12 Projekt erstellen
composer create-project laravel/laravel apde_be

# PHP 8.4 Anforderung in composer.json
"require": {
    "php": "^8.4",
    "laravel/framework": "^12.0"
}
```

**Was ist Laravel 12:**
- Modernste Version des Laravel PHP-Frameworks
- Streamlined Dateistruktur (keine Middleware-Dateien mehr in app/Http/Middleware/)
- Neue Konfiguration über `bootstrap/app.php` statt Kernel-Dateien
- Erweiterte Enum-Support und moderne PHP 8.4 Features
- **Dokumentation:** [Laravel 12 Documentation](https://laravel.com/docs/12.x)

### 1.2 MongoDB Library installiert

MongoDB-Integration für NoSQL-Datenpersistierung hinzugefügt:

```bash
# MongoDB Laravel Package installieren
composer require mongodb/laravel-mongodb:^5.4
```

**Was ist MongoDB Laravel Package:**
- Offizielle MongoDB-Integration für Laravel
- Ermöglicht Eloquent ORM mit MongoDB-Collections
- Unterstützt Relationships, Migrations und Laravel-Features
- **Dokumentation:** [MongoDB Laravel Package](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/)

### 1.3 Laravel Konfigurationen vorgenommen

#### AppServiceProvider Anpassungen

Spezifische Konfigurationen wurden im `AppServiceProvider` vorgenommen für:
- MongoDB-Verbindung als Standard-Datenbank
- Service-Bindings für Business-Services
- API-Resource-Konfigurationen

#### Database Configuration

```php
// config/database.php - MongoDB als Standard
'default' => env('DB_CONNECTION', 'mongodb'),

'connections' => [
    'mongodb' => [
        'driver' => 'mongodb',
        'dsn' => env('MONGODB_URI', 'mongodb://localhost:27017'),
        'database' => env('MONGODB_DATABASE', 'apde'),
    ],
]
```

### 1.4 Development Tools hinzugefügt

#### 1.4.1 Pest Testing Framework

```bash
composer require pestphp/pest:^3.8 --dev
composer require pestphp/pest-plugin-laravel:^3.2 --dev
```

**Was ist Pest:**
- Modernes PHP-Testing-Framework
- Alternative zu PHPUnit mit eleganterer Syntax
- Bessere Lesbarkeit und Developer Experience
- **Dokumentation:** [Pest Testing](https://pestphp.com)

**Beispiel Test-Syntax:**
```php
it('can create a prospect', function () {
    $prospect = Prospect::factory()->create();
    expect($prospect)->toBeInstanceOf(Prospect::class);
});
```

#### 1.4.2 Laravel Pint Code Formatter

```bash
composer require laravel/pint:^1.13 --dev
```

**Was ist Pint:**
- Offizieller Laravel Code-Formatter
- Basiert auf PHP-CS-Fixer
- Automatische Code-Formatierung nach Laravel-Standards
- **Dokumentation:** [Laravel Pint](https://laravel.com/docs/12.x/pint)

**Verwendung:**
```bash
vendor/bin/pint          # Code formatieren
vendor/bin/pint --test   # Nur prüfen ohne Änderungen
```

#### 1.4.3 Rector Code Refactoring

```bash
composer require rector/rector:^2.0 --dev
composer require driftingly/rector-laravel:^2.0 --dev
```

**Was ist Rector:**
- Automatisierte Code-Refactoring-Tool
- Upgrade von Laravel-Versionen
- Modernisierung von PHP-Code
- **Dokumentation:** [Rector](https://getrector.com)

#### 1.4.4 PHPStan/Larastan Static Analysis

```bash
composer require larastan/larastan:^3.4 --dev
```

**Was ist PHPStan/Larastan:**
- Statische Code-Analyse für PHP
- Larastan = Laravel-spezifische Erweiterung
- Findet Bugs ohne Code-Ausführung
- Type-Safety und Code-Qualität
- **Dokumentation:** [PHPStan](https://phpstan.org) | [Larastan](https://github.com/larastan/larastan)

### 1.5 Coding Standards definiert

#### 1.5.1 PSR-4 Autoloading Standard

PSR-4 wurde in `composer.json` konfiguriert:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    }
}
```

**Was ist PSR-4:**
- PHP Standard Recommendation für Autoloading
- Definiert Namespace-zu-Dateipfad-Mapping
- Ermöglicht automatisches Laden von Klassen
- **Dokumentation:** [PSR-4 Specification](https://www.php-fig.org/psr/psr-4/)

#### 1.5.2 PSR-12 Coding Style Standard

PSR-12 wird durch Laravel Pint automatisch durchgesetzt:

```bash
# PSR-12 Style Check
vendor/bin/pint --test

# PSR-12 Style Fix
vendor/bin/pint
```

**Was ist PSR-12:**
- Erweiterte Version von PSR-2 Coding Style Guide
- Definiert Code-Formatierung und Style-Regeln
- Einheitlicher Code-Style im gesamten Projekt
- **Dokumentation:** [PSR-12 Specification](https://www.php-fig.org/psr/psr-12/)

**PSR-12 Beispiele:**
```php
<?php

declare(strict_types=1);

namespace App\Models;

final class Prospect extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];
}
```

### 1.6 MongoDB Local Setup mit Docker

#### 1.6.1 MongoDB Docker Container

```bash
# MongoDB Community Server mit Docker
docker pull mongodb/mongodb-community-server:latest

# Container starten
docker run --name mongodb \
  -p 27017:27017 \
  -d mongodb/mongodb-community-server:latest
```

#### 1.6.2 Docker Compose Setup (Optional)

```yaml
# docker-compose.yml
version: '3.8'
services:
  mongodb:
    image: mongodb/mongodb-community-server:latest
    container_name: mongodb
    ports:
      - "27017:27017"
    environment:
      MONGODB_INITDB_DATABASE: apde
    volumes:
      - mongodb_data:/data/db

volumes:
  mongodb_data:
```

```bash
# Mit Docker Compose starten
docker-compose up -d mongodb
```

#### 1.6.3 Verbindung testen

```bash
# MongoDB Shell verbinden
mongosh mongodb://localhost:27017

# Oder mit Docker exec
docker exec -it mongodb mongosh
```

#### 1.6.4 Laravel MongoDB Konfiguration

```env
# .env Konfiguration
DB_CONNECTION=mongodb
MONGODB_URI="mongodb://localhost:27017"
MONGODB_DATABASE="apde"
```

**Was ist MongoDB:**
- NoSQL Dokumenten-Datenbank
- Flexible Schema-Struktur
- Horizontal skalierbar
- JSON-ähnliche Dokumente (BSON)
- **Dokumentation:** [MongoDB Docs](https://www.mongodb.com/docs/manual/)

### 1.7 Composer Scripts konfiguriert

Automatisierte Scripts für Code-Qualität wurden hinzugefügt:

```json
{
    "scripts": {
        "rector": "vendor/bin/rector process",
        "pint": "vendor/bin/pint --parallel",
        "phpstan": "vendor/bin/phpstan analyse",
        "test:unit": "pest --parallel --coverage",
        "test": [
            "@test:unit",
            "@pint",
            "@rector",
            "@phpstan"
        ],
        "fix": [
            "@rector",
            "@pint",
            "@phpstan"
        ]
    }
}
```

**Verwendung:**
```bash
composer test    # Alle Tests und Code-Qualitätsprüfungen
composer fix     # Automatische Code-Fixes
composer pint    # Code-Formatierung
composer phpstan # Statische Analyse
```

## Projektstruktur nach Teil 1

```
apde_be/
├── app/
│   ├── Console/
│   ├── Http/
│   ├── Models/
│   └── Providers/
├── bootstrap/
│   └── app.php           # Laravel 12 Konfiguration
├── config/
│   └── database.php      # MongoDB Konfiguration
├── database/
├── tests/                # Pest Tests
├── vendor/
├── composer.json         # Dependencies & Scripts
├── .env.example         # Environment Template
├── rector.php           # Rector Konfiguration
├── phpstan.neon         # PHPStan Konfiguration
└── README.md            # Projekt-Dokumentation
```

## Referenzen und Links

### Laravel Ecosystem
- **[Laravel 12 Documentation](https://laravel.com/docs/12.x)** - Offizielle Laravel Dokumentation
- **[Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)** - Community Best Practices
- **[Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)** - Commit-Message Standard

### Development Tools
- **[Pest Testing](https://pestphp.com/docs/installation)** - Testing Framework
- **[Laravel Pint](https://laravel.com/docs/12.x/pint)** - Code Formatter
- **[Rector](https://getrector.com)** - Code Refactoring
- **[PHPStan](https://phpstan.org)** / **[Larastan](https://github.com/larastan/larastan)** - Static Analysis

### MongoDB
- **[MongoDB Laravel Package](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/quick-start/)** - Quick Start Guide
- **[MongoDB Docker](https://www.mongodb.com/docs/manual/tutorial/install-mongodb-community-with-docker/)** - Docker Installation
- **[MongoDB Authentication](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/user-authentication/)** - User Authentication

### Standards
- **[PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)** - Autoloading Standard
- **[PSR-12 Coding Style](https://www.php-fig.org/psr/psr-12/)** - Coding Style Guide

---

**Status:** Teil 1 abgeschlossen ✅

**Nächste Schritte:** Teil 2 - Einbindung der Persistenz (Data Access Layer, Models, APIs)