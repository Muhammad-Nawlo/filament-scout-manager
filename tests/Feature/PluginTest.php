<?php

use MuhammadNawlo\FilamentScoutManager\FilamentScoutManagerPlugin;

test('plugin returns correct id', function () {
    $plugin = FilamentScoutManagerPlugin::make();
    expect($plugin->getId())->toBe('filament-scout-manager');
});

test('plugin can be instantiated', function () {
    $plugin = FilamentScoutManagerPlugin::make();
    expect($plugin)->toBeInstanceOf(FilamentScoutManagerPlugin::class);
});
