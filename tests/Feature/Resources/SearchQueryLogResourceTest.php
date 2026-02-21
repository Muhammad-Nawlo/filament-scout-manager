<?php

use MuhammadNawlo\FilamentScoutManager\Resources\SearchQueryLogResource;
use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;
use Filament\Tables\Table;

test('search query log resource has correct navigation properties', function () {
    expect(SearchQueryLogResource::getModel())->toBe(SearchQueryLog::class);
    expect(SearchQueryLogResource::getNavigationIcon())->toBe('heroicon-o-magnifying-glass-circle');
    expect(SearchQueryLogResource::getNavigationGroup())->toBe(__('filament-scout-manager::navigation.group'));
    expect(SearchQueryLogResource::getNavigationLabel())->toBe(__('filament-scout-manager::navigation.logs'));
    expect(SearchQueryLogResource::getSlug())->toBe('search-logs');
    expect(SearchQueryLogResource::getModelLabel())->toBe(__('filament-scout-manager::logs.title'));
    expect(SearchQueryLogResource::getPluralModelLabel())->toBe(__('filament-scout-manager::logs.title'));
});

test('search query log resource table has expected columns', function () {
    $table = SearchQueryLogResource::table(Table::make());
    $columns = $table->getColumns();

    $columnNames = collect($columns)->map(fn ($col) => $col->getName())->toArray();

    expect($columnNames)->toContain('query');
    expect($columnNames)->toContain('model_type');
    expect($columnNames)->toContain('result_count');
    expect($columnNames)->toContain('execution_time');
    expect($columnNames)->toContain('user.name');
    expect($columnNames)->toContain('ip_address');
    expect($columnNames)->toContain('successful');
    expect($columnNames)->toContain('created_at');
});

test('search query log resource has expected filters', function () {
    $table = SearchQueryLogResource::table(Table::make());
    $filters = $table->getFilters();

    $filterNames = collect($filters)->map(fn ($filter) => $filter->getName())->toArray();

    expect($filterNames)->toContain('successful');
    expect($filterNames)->toContain('failed');
    expect($filterNames)->toContain('model_type');
    expect($filterNames)->toContain('created_at');
});

test('search query log resource has expected actions', function () {
    $table = SearchQueryLogResource::table(Table::make());
    $actions = $table->getActions();

    $actionNames = collect($actions)->map(fn ($action) => $action->getName())->toArray();

    expect($actionNames)->toContain('view');
    expect($actionNames)->toContain('delete');
});

test('search query log resource has bulk actions', function () {
    $table = SearchQueryLogResource::table(Table::make());
    $bulkActions = $table->getBulkActions();

    $bulkActionNames = collect($bulkActions)->map(fn ($action) => $action->getName())->toArray();

    expect($bulkActionNames)->toContain('delete');
    expect($bulkActionNames)->toContain('prune_old');
});

test('search query log resource pages are registered', function () {
    $pages = SearchQueryLogResource::getPages();
    expect($pages)->toHaveKey('index');
    expect($pages['index']->getRoute())->toBe('/');
});
