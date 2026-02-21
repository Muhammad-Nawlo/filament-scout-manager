<?php

use MuhammadNawlo\FilamentScoutManager\Widgets\IndexStatusWidget;

test('widget has full column span', function () {
    $widget = new IndexStatusWidget;
    expect($widget->getColumnSpan())->toBe('full');
});

test('widget returns array data', function () {
    $widget = new IndexStatusWidget;
    $data = $widget->getData();

    expect($data)->toBeArray()
        ->toHaveKeys(['total_models', 'indexed_models', 'total_records', 'indexed_records', 'engines']);
});

test('widget exposes stats overview cards', function () {
    $widget = new IndexStatusWidget;

    $stats = invade($widget)->getStats();

    expect($stats)->toBeArray()->toHaveCount(3);
});
