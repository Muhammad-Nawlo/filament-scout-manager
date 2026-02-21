<?php

use MuhammadNawlo\FilamentScoutManager\Models\Synonym;

test('can create synonym', function () {
    $synonym = Synonym::create([
        'model_type' => 'App\Models\Product',
        'word' => 'laptop',
        'synonyms' => ['notebook', 'macbook', 'dell'],
    ]);

    expect($synonym)
        ->toBeInstanceOf(Synonym::class)
        ->and($synonym->synonyms)->toBe(['notebook', 'macbook', 'dell']);
});

test('synonym_list attribute returns comma-separated string', function () {
    $synonym = Synonym::create([
        'model_type' => 'App\Models\Product',
        'word' => 'phone',
        'synonyms' => ['mobile', 'cellphone', 'smartphone'],
    ]);

    expect($synonym->synonym_list)->toBe('mobile, cellphone, smartphone');
});

test('scope forModel filters by model type', function () {
    Synonym::create(['model_type' => 'App\Models\Product', 'word' => 'laptop', 'synonyms' => []]);
    Synonym::create(['model_type' => 'App\Models\Post', 'word' => 'laravel', 'synonyms' => []]);

    $productSynonyms = Synonym::forModel('App\Models\Product')->get();

    expect($productSynonyms)->toHaveCount(1)
        ->and($productSynonyms->first()->word)->toBe('laptop');
});

test('converts to Meilisearch format', function () {
    $synonym = Synonym::create([
        'model_type' => 'App\Models\Product',
        'word' => 'laptop',
        'synonyms' => ['notebook', 'macbook'],
    ]);

    expect($synonym->toMeilisearchFormat())->toBe([
        'laptop' => ['notebook', 'macbook'],
    ]);
});

test('converts to Algolia format', function () {
    $synonym = Synonym::create([
        'model_type' => 'App\Models\Product',
        'word' => 'laptop',
        'synonyms' => ['notebook', 'macbook'],
    ]);

    expect($synonym->toAlgoliaFormat())->toBe([
        'objectID' => 'laptop',
        'type' => 'synonym',
        'synonyms' => ['laptop', 'notebook', 'macbook'],
    ]);
});
