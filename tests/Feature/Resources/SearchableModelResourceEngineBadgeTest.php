<?php

use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;

test('typesense badge translation key exists', function () {
    $label = __('filament-scout-manager::filament-scout-manager.models.engine_badges.typesense');

    expect($label)->toBe('Typesense');
});

test('unknown engine displays as class basename in badge', function () {
    $unknownEngine = new class
    {
    };
    $basename = class_basename($unknownEngine);

    expect($basename)->not->toBe('')
        ->and(str_contains(get_class($unknownEngine), $basename))->toBeTrue();
});

test('engine badge color is gray for unknown engine', function () {
    $unknownEngine = new class
    {
    };
    $known = [
        \Laravel\Scout\Engines\AlgoliaEngine::class,
        \Laravel\Scout\Engines\MeilisearchEngine::class,
        \Laravel\Scout\Engines\TypesenseEngine::class,
        \Laravel\Scout\Engines\DatabaseEngine::class,
        \Laravel\Scout\Engines\CollectionEngine::class,
    ];
    $isUnknown = ! in_array(get_class($unknownEngine), $known, true);

    expect($isUnknown)->toBeTrue();
});
