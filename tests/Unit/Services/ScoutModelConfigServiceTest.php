<?php

use MuhammadNawlo\FilamentScoutManager\DTO\ScoutModelConfigDTO;
use MuhammadNawlo\FilamentScoutManager\Services\ScoutModelConfigService;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

test('forModel returns null when no config is saved', function () {
    $service = app(ScoutModelConfigService::class);

    $result = $service->forModel('App\Models\UnconfiguredModel');

    expect($result)->toBeNull();
});

test('forModel returns DTO when config is saved and caches result', function () {
    if (! FilamentScoutManagerSettings::repositoryTableExists()) {
        $this->markTestSkipped('Settings table not available');
    }

    $settings = app(FilamentScoutManagerSettings::class);
    $settings->setModelConfig('App\Models\Product', [
        'index_name_override' => 'products_custom',
        'searchable_fields' => ['name', 'description'],
        'engine_override' => 'meilisearch',
        'queue_connection' => 'redis',
    ]);

    $service = app(ScoutModelConfigService::class);

    $first = $service->forModel('App\Models\Product');
    $second = $service->forModel('App\Models\Product');

    expect($first)->toBeInstanceOf(ScoutModelConfigDTO::class)
        ->and($first->indexName)->toBe('products_custom')
        ->and($first->searchableFields)->toBe(['name', 'description'])
        ->and($first->engine)->toBe('meilisearch')
        ->and($first->queueConnection)->toBe('redis')
        ->and($second)->toBe($first);
});
