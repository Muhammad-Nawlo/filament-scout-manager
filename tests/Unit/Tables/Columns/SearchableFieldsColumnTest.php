<?php

use MuhammadNawlo\FilamentScoutManager\Tables\Columns\SearchableFieldsColumn;

test('getState returns fields from toSearchableArray', function () {
    $model = new class {
        public $class;
        public function __construct() {
            $this->class = new class {
                public function toSearchableArray() {
                    return ['name' => 'test', 'description' => 'test'];
                }
                public function getTable() { return 'test'; }
            };
        }
    };

    $column = SearchableFieldsColumn::make('searchable_fields');
    $column->record($model);

    $reflection = new ReflectionClass($column);
    $method = $reflection->getMethod('getState');
    $method->setAccessible(true);
    $result = $method->invoke($column);

    expect($result)->toBe(['name', 'description']);
});

test('getState returns empty array on exception', function () {
    $model = new class {
        public $class = 'NonExistentClass';
    };

    $column = SearchableFieldsColumn::make('searchable_fields');
    $column->record($model);

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
