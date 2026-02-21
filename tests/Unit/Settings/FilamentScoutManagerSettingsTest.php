<?php

use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

test('can set and get model config', function () {
    $settings = app(FilamentScoutManagerSettings::class);
    $modelClass = 'App\Models\Product';
    $config = [
        'searchable_fields' => ['name', 'description'],
        'engine_override' => 'algolia',
    ];

    $settings->setModelConfig($modelClass, $config);
    $retrieved = $settings->getModelConfig($modelClass);

    expect($retrieved)->toBe($config);
});

test('returns null for nonexistent model', function () {
    $settings = app(FilamentScoutManagerSettings::class);
    expect($settings->getModelConfig('App\Missing'))->toBeNull();
});

test('can store multiple model configs', function () {
    $settings = app(FilamentScoutManagerSettings::class);
    $settings->setModelConfig('App\Product', ['fields' => ['name']]);
    $settings->setModelConfig('App\Post', ['fields' => ['title']]);

    expect($settings->getModelConfig('App\Product'))->toBe(['fields' => ['name']])
        ->and($settings->getModelConfig('App\Post'))->toBe(['fields' => ['title']]);
});
