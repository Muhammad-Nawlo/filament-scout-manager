<?php

use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;
use MuhammadNawlo\FilamentScoutManager\Widgets\PopularSearchesWidget;

beforeEach(function () {
    config()->set('filament-scout-manager.log_searches', true);
});

test('widget returns empty array when logging disabled', function () {
    config()->set('filament-scout-manager.log_searches', false);
    $widget = new PopularSearchesWidget;
    expect($widget->getData())->toBe([]);
});

test('widget returns fallback stat when logging disabled', function () {
    config()->set('filament-scout-manager.log_searches', false);

    $widget = new PopularSearchesWidget;
    $stats = invade($widget)->getStats();

    expect($stats)->toHaveCount(1);
});

test('widget returns popular searches', function () {
    SearchQueryLog::create(['query' => 'test1', 'created_at' => now()->subDay()]);
    SearchQueryLog::create(['query' => 'test1', 'created_at' => now()->subDay()]);
    SearchQueryLog::create(['query' => 'test2', 'created_at' => now()->subDay()]);

    $widget = new PopularSearchesWidget;
    $data = $widget->getData();

    expect($data)->toHaveCount(2)
        ->and($data[0]['query'])->toBe('test1')
        ->and($data[0]['total'])->toBe(2);
});

test('widget maps popular searches to stats overview cards', function () {
    SearchQueryLog::create(['query' => 'test1', 'created_at' => now()->subDay()]);
    SearchQueryLog::create(['query' => 'test1', 'created_at' => now()->subDay()]);
    SearchQueryLog::create(['query' => 'test2', 'created_at' => now()->subDay()]);

    $widget = new PopularSearchesWidget;
    $stats = invade($widget)->getStats();

    expect($stats)->toHaveCount(2);
});
