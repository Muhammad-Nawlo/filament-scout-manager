<?php

use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;

test('resource has correct navigation properties', function () {
    expect(SearchableModelResource::getNavigationIcon())->toBe('heroicon-o-cube')
        ->and(SearchableModelResource::getNavigationGroup())->toBe('Search')
        ->and(SearchableModelResource::getNavigationLabel())->toBe('Searchable Models')
        ->and(SearchableModelResource::getSlug())->toBe('searchable-models');
});

test('isSearchable detects trait correctly', function () {
    $reflection = new ReflectionClass(SearchableModelResource::class);
    $method = $reflection->getMethod('isSearchable');
    $method->setAccessible(true);

    $searchableClass = new class { use \Laravel\Scout\Searchable; };
    $nonSearchableClass = new class {};

    expect($method->invoke(null, get_class($searchableClass)))->toBeTrue()
        ->and($method->invoke(null, get_class($nonSearchableClass)))->toBeFalse();
});
