<?php

use Filament\Tables\Table;
use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchQueryLogResource;

test('search query log resource has correct navigation properties', function () {
    expect(SearchQueryLogResource::getModel())->toBe(SearchQueryLog::class);
    expect(SearchQueryLogResource::getNavigationIcon())->toBe('heroicon-o-magnifying-glass-circle');
    expect(SearchQueryLogResource::getNavigationGroup())->toBe('Search');
    expect(SearchQueryLogResource::getNavigationLabel())->toBe('Search Logs');
    expect(SearchQueryLogResource::getSlug())->toBe('search-logs');
    expect(SearchQueryLogResource::getModelLabel())->toBe('Search Log');
    expect(SearchQueryLogResource::getPluralModelLabel())->toBe('Search Logs');
});

test('search query log resource table config includes expected columns, filters and actions', function () {
    $columns = [];
    $filters = [];
    $actions = [];
    $bulkActions = [];

    $table = \Mockery::mock(Table::class);

    $table->shouldReceive('columns')->once()->withArgs(function (array $value) use (&$columns): bool {
        $columns = $value;

        return true;
    })->andReturnSelf();

    $table->shouldReceive('filters')->once()->withArgs(function (array $value) use (&$filters): bool {
        $filters = $value;

        return true;
    })->andReturnSelf();

    $table->shouldReceive('actions')->once()->withArgs(function (array $value) use (&$actions): bool {
        $actions = $value;

        return true;
    })->andReturnSelf();

    $table->shouldReceive('bulkActions')->once()->withArgs(function (array $value) use (&$bulkActions): bool {
        $bulkActions = $value;

        return true;
    })->andReturnSelf();

    $table->shouldReceive('defaultSort')->once()->with('created_at', 'desc')->andReturnSelf();

    SearchQueryLogResource::table($table);

    expect(collect($columns)->map(fn ($column) => $column->getName())->all())
        ->toContain('query', 'model_type', 'result_count', 'execution_time', 'user.name', 'ip_address', 'successful', 'created_at');

    expect(collect($filters)->map(fn ($filter) => $filter->getName())->all())
        ->toContain('successful', 'failed', 'model_type', 'created_at');

    expect(collect($actions)->map(fn ($action) => $action->getName())->all())
        ->toContain('view', 'delete');

    expect(collect($bulkActions)->first()->getFlatActions()->keys()->all())
        ->toContain('delete', 'prune_old');
});

test('search query log resource pages are registered', function () {
    $pages = SearchQueryLogResource::getPages();

    expect($pages)->toHaveKey('index')
        ->and($pages['index'])->toBeInstanceOf(Filament\Resources\Pages\PageRegistration::class);
});
