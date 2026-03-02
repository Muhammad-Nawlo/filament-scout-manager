<?php

use Filament\Panel;
use MuhammadNawlo\FilamentScoutManager\FilamentScoutManagerPlugin;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchQueryLogResource;
use MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource;
use MuhammadNawlo\FilamentScoutManager\Widgets\IndexStatusWidget;
use MuhammadNawlo\FilamentScoutManager\Widgets\PopularSearchesWidget;

test('plugin returns correct id', function () {
    $plugin = FilamentScoutManagerPlugin::make();
    expect($plugin->getId())->toBe('filament-scout-manager');
});

test('plugin can be instantiated', function () {
    $plugin = FilamentScoutManagerPlugin::make();
    expect($plugin)->toBeInstanceOf(FilamentScoutManagerPlugin::class);
});

test('plugin implements Filament Plugin contract', function () {
    $plugin = FilamentScoutManagerPlugin::make();
    expect($plugin)->toBeInstanceOf(\Filament\Contracts\Plugin::class);
});

test('plugin register adds expected resources and widgets', function () {
    $panel = Panel::make();
    FilamentScoutManagerPlugin::make()->register($panel);

    expect($panel->getResources())->toContain(SearchableModelResource::class)
        ->and($panel->getResources())->toContain(SearchQueryLogResource::class)
        ->and($panel->getResources())->toContain(SynonymResource::class)
        ->and($panel->getWidgets())->toContain(IndexStatusWidget::class)
        ->and($panel->getWidgets())->toContain(PopularSearchesWidget::class);
});
