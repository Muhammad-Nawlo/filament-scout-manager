<?php

namespace MuhammadNawlo\FilamentScoutManager;

use Filament\Contracts\Plugin;
use Filament\Panel;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchQueryLogResource;
use MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource;

class FilamentScoutManagerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-scout-manager';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                SearchableModelResource::class,
                SearchQueryLogResource::class,
                SynonymResource::class,
            ])
            ->widgets([
                \MuhammadNawlo\FilamentScoutManager\Widgets\IndexStatusWidget::class,
                \MuhammadNawlo\FilamentScoutManager\Widgets\PopularSearchesWidget::class,
            ])
            ->pages([
                // We'll add settings page later
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
