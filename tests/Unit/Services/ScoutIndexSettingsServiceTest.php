<?php

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use MuhammadNawlo\FilamentScoutManager\Services\ScoutIndexSettingsService;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

beforeEach(function () {
    config()->set('scout.algolia.index-settings', []);
    config()->set('scout.meilisearch.index-settings', []);
});

test('apply does nothing when settings repository table does not exist', function () {
    $service = app(ScoutIndexSettingsService::class);

    $service->apply();

    expect(config('scout.algolia.index-settings'))->toBe([])
        ->and(config('scout.meilisearch.index-settings'))->toBe([]);
});

test('apply populates algolia index-settings from stored model config', function () {
    if (! FilamentScoutManagerSettings::repositoryTableExists()) {
        $this->markTestSkipped('Settings table not available');
    }

    $modelClass = get_class(new class extends Model
    {
        use Searchable;
    });

    $settings = app(FilamentScoutManagerSettings::class);
    $settings->setModelConfig($modelClass, [
        'engine_override' => 'algolia',
        'engine_settings' => [
            'algolia' => [
                'searchableAttributes' => ['id', 'name'],
                'attributesForFaceting' => ['filterOnly(status)'],
            ],
        ],
    ]);

    $service = app(ScoutIndexSettingsService::class);
    $service->apply();

    $algolia = config('scout.algolia.index-settings');
    expect($algolia)->toHaveKey($modelClass)
        ->and($algolia[$modelClass]['searchableAttributes'])->toBe(['id', 'name'])
        ->and($algolia[$modelClass]['attributesForFaceting'])->toContain('filterOnly(status)');
});

test('apply populates meilisearch index-settings from stored model config', function () {
    if (! FilamentScoutManagerSettings::repositoryTableExists()) {
        $this->markTestSkipped('Settings table not available');
    }

    $modelClass = get_class(new class extends Model
    {
        use Searchable;
    });

    $settings = app(FilamentScoutManagerSettings::class);
    $settings->setModelConfig($modelClass, [
        'engine_override' => 'meilisearch',
        'engine_settings' => [
            'meilisearch' => [
                'filterableAttributes' => ['id', 'status'],
                'sortableAttributes' => ['created_at'],
            ],
        ],
    ]);

    $service = app(ScoutIndexSettingsService::class);
    $service->apply();

    $meilisearch = config('scout.meilisearch.index-settings');
    expect($meilisearch)->toHaveKey($modelClass)
        ->and($meilisearch[$modelClass]['filterableAttributes'])->toBe(['id', 'status'])
        ->and($meilisearch[$modelClass]['sortableAttributes'])->toBe(['created_at']);
});
