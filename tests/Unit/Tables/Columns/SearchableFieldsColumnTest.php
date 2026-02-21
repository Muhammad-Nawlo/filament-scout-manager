<?php

use Illuminate\Database\Eloquent\Model;
use MuhammadNawlo\FilamentScoutManager\Tables\Columns\SearchableFieldsColumn;

test('getState returns fields from toSearchableArray', function () {
    $searchableModel = new class extends Model
    {
        public function toSearchableArray(): array
        {
            return ['name' => 'test', 'description' => 'test'];
        }
    };

    $column = SearchableFieldsColumn::make('searchable_fields');
    $column->record(['class' => get_class($searchableModel)]);

    $reflection = new ReflectionClass($column);
    $method = $reflection->getMethod('getState');
    $method->setAccessible(true);
    $result = $method->invoke($column);

    expect($result)->toBe(['name', 'description']);
});

test('getState returns empty array on exception', function () {
    $column = SearchableFieldsColumn::make('searchable_fields');
    $column->record(['class' => 'NonExistentClass']);

    $reflection = new ReflectionClass($column);
    $method = $reflection->getMethod('getState');
    $method->setAccessible(true);
    $result = $method->invoke($column);

    expect($result)->toBe([]);
});

test('getFormattedState returns badges for fields', function () {
    $column = Mockery::mock(SearchableFieldsColumn::class)->makePartial();
    $column->shouldReceive('getState')->andReturn(['name', 'description', 'price', 'category']);

    $reflection = new ReflectionClass($column);
    $method = $reflection->getMethod('getFormattedState');
    $method->setAccessible(true);
    $result = $method->invoke($column);

    expect($result)
        ->toContain('name')
        ->toContain('description')
        ->toContain('price')
        ->toContain('+1 more');
});
