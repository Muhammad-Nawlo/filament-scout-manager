<?php

use MuhammadNawlo\FilamentScoutManager\DTO\ScoutModelConfigDTO;

test('dto can be created with all properties', function () {
    $dto = new ScoutModelConfigDTO(
        indexName: 'my_index',
        searchableFields: ['title', 'body'],
        engine: 'meilisearch',
        engineSettings: ['filterableAttributes' => ['status']],
        queueConnection: 'redis',
    );

    expect($dto->indexName)->toBe('my_index')
        ->and($dto->searchableFields)->toBe(['title', 'body'])
        ->and($dto->engine)->toBe('meilisearch')
        ->and($dto->engineSettings)->toBe(['filterableAttributes' => ['status']])
        ->and($dto->queueConnection)->toBe('redis');
});

test('dto can be created with defaults', function () {
    $dto = new ScoutModelConfigDTO;

    expect($dto->indexName)->toBeNull()
        ->and($dto->searchableFields)->toBeNull()
        ->and($dto->engine)->toBeNull()
        ->and($dto->engineSettings)->toBeNull()
        ->and($dto->queueConnection)->toBeNull();
});

test('dto is readonly and cannot be mutated', function () {
    $dto = new ScoutModelConfigDTO(indexName: 'test');

    expect($dto->indexName)->toBe('test');

    expect(fn () => ($dto->indexName = 'other'))->toThrow(\Error::class);
});
