<?php

use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;

test('can create search log', function () {
    $log = SearchQueryLog::create([
        'query' => 'test search',
        'model_type' => 'App\Models\Post',
        'result_count' => 5,
        'execution_time' => 0.5,
        'successful' => true,
    ]);

    expect($log)
        ->toBeInstanceOf(SearchQueryLog::class)
        ->and($log->query)->toBe('test search')
        ->and($log->result_count)->toBe(5);
});

test('popular scope returns most frequent queries', function () {
    SearchQueryLog::create(['query' => 'popular term', 'created_at' => now()->subDays(5)]);
    SearchQueryLog::create(['query' => 'popular term', 'created_at' => now()->subDays(3)]);
    SearchQueryLog::create(['query' => 'rare term', 'created_at' => now()->subDays(10)]);

    $popular = SearchQueryLog::popular(30)->get();

    expect($popular)->toHaveCount(2)
        ->and($popular->first()->query)->toBe('popular term')
        ->and($popular->first()->total)->toBe(2);
});

test('failed scope returns only unsuccessful searches', function () {
    SearchQueryLog::create(['query' => 'success', 'successful' => true]);
    SearchQueryLog::create(['query' => 'fail', 'successful' => false]);

    $failed = SearchQueryLog::failed()->get();

    expect($failed)->toHaveCount(1)
        ->and($failed->first()->query)->toBe('fail');
});
