<?php

use Illuminate\Database\Eloquent\Model;
use MuhammadNawlo\FilamentScoutManager\Concerns\SearchableWithScoutManagerConfig;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

beforeEach(function () {
    if (! FilamentScoutManagerSettings::repositoryTableExists()) {
        $this->markTestSkipped('Settings table not available');
    }
});

test('searchableAs returns stored index name when config has index_name_override', function () {
    $modelClass = get_class(new class extends Model
    {
        use SearchableWithScoutManagerConfig;
    });

    app(FilamentScoutManagerSettings::class)->setModelConfig($modelClass, [
        'index_name_override' => 'custom_products_index',
    ]);

    $model = new $modelClass;

    expect($model->searchableAs())->toBe('custom_products_index');
});

test('searchableAs returns table name when no config', function () {
    $modelClass = get_class(new class extends Model
    {
        use SearchableWithScoutManagerConfig;
    });

    $model = new $modelClass;

    expect($model->searchableAs())->toBeString()
        ->and($model->searchableAs())->not->toBe('');
});

test('toSearchableArray filters by searchable_fields when config has them', function () {
    $modelClass = get_class(new class extends Model
    {
        use SearchableWithScoutManagerConfig;
    });

    app(FilamentScoutManagerSettings::class)->setModelConfig($modelClass, [
        'searchable_fields' => ['title', 'body'],
    ]);

    $model = new $modelClass;
    $model->setRawAttributes([
        'id' => 1,
        'title' => 'Hello',
        'body' => 'World',
        'secret' => 'hidden',
    ]);

    $result = $model->toSearchableArray();

    expect($result)->toBe(['title' => 'Hello', 'body' => 'World'])
        ->and($result)->not->toHaveKey('secret');
});

test('scoutQueueConnection returns stored value when config has queue_connection', function () {
    $modelClass = get_class(new class extends Model
    {
        use SearchableWithScoutManagerConfig;
    });

    app(FilamentScoutManagerSettings::class)->setModelConfig($modelClass, [
        'queue_connection' => 'redis',
    ]);

    $model = new $modelClass;

    expect($model->scoutQueueConnection())->toBe('redis');
});

test('scoutQueueConnection returns null when no config', function () {
    $modelClass = get_class(new class extends Model
    {
        use SearchableWithScoutManagerConfig;
    });

    $model = new $modelClass;

    expect($model->scoutQueueConnection())->toBeNull();
});

test('searchableUsing returns engine instance', function () {
    $modelClass = get_class(new class extends Model
    {
        use SearchableWithScoutManagerConfig;
    });

    $model = new $modelClass;

    expect($model->searchableUsing())->toBeObject();
});
