# Filament Scout Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/muhammad-nawlo/filament-scout-manager.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/filament-scout-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/filament-scout-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/muhammad-nawlo/filament-scout-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/filament-scout-manager/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/muhammad-nawlo/filament-scout-manager/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/muhammad-nawlo/filament-scout-manager.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/filament-scout-manager)

A Filament plugin to manage your Laravel Scout search setup from an admin panel.

## Features

- Discover Scout-searchable models and inspect index/engine metadata.
- Run index actions (import, flush, refresh) per model or in bulk.
- View index health and popular searches with dashboard widgets.
- Log user search queries for analysis.
- Manage search synonyms in the panel.
- Configure behavior with package config/settings.

## Requirements

- PHP 8.2+
- Laravel app with [Laravel Scout](https://laravel.com/docs/scout) configured
- Filament 5 panel

## Installation

Install the package:

```bash
composer require muhammad-nawlo/filament-scout-manager
```

Run the installer:

```bash
php artisan filament-scout-manager:install
```

Or manually publish package files:

```bash
php artisan vendor:publish --tag="filament-scout-manager-config"
php artisan vendor:publish --tag="filament-scout-manager-migrations"
php artisan migrate
```

If you use a custom Filament theme, add the package views as a Tailwind source:

```css
@source '../../../../vendor/muhammad-nawlo/filament-scout-manager/resources/**/*.blade.php';
```

## Register the plugin

In your Filament panel provider, register the plugin:

```php
use MuhammadNawlo\FilamentScoutManager\FilamentScoutManagerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentScoutManagerPlugin::make(),
        ]);
}
```

## Configuration

Published config: `config/filament-scout-manager.php`

```php
return [
    'log_searches' => true,
    'log_retention_days' => 30,
    'enable_synonyms' => true,
    'models' => [
        // 'App\\Other\\Model' => [],
    ],
];
```

## Usage notes

- Ensure each model you want indexed uses Scout's `Searchable` trait.
- Configure your Scout driver (`SCOUT_DRIVER`) and engine credentials in `.env`.
- The "Searchable Fields" options in the panel are most useful when your model defines a custom `toSearchableArray()`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent updates.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Muhammad-Nawlo](https://github.com/Muhammad-Nawlo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for details.
