<?php

use Filament\Tables\Table;
use MuhammadNawlo\FilamentScoutManager\Models\Synonym;
use MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource;

test('synonym resource has correct navigation properties', function () {
    expect(SynonymResource::getModel())->toBe(Synonym::class);
    expect(SynonymResource::getNavigationIcon())->toBe('heroicon-o-link');
    expect(SynonymResource::getNavigationGroup())->toBe('Search');
    expect(SynonymResource::getNavigationLabel())->toBe('Synonyms');
    expect(SynonymResource::getSlug())->toBe('synonyms');
    expect(SynonymResource::getModelLabel())->toBe('Synonym Group');
    expect(SynonymResource::getPluralModelLabel())->toBe('Synonyms');
});

test('synonym resource table has expected columns, filters and actions', function () {
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

    $table->shouldReceive('defaultSort')->once()->with('word')->andReturnSelf();

    SynonymResource::table($table);

    expect(collect($columns)->map(fn ($column) => $column->getName())->all())
        ->toContain('word', 'model_type', 'synonyms', 'created_at', 'updated_at');

    expect(collect($filters)->map(fn ($filter) => $filter->getName())->all())
        ->toContain('model_type');

    expect(collect($actions)->map(fn ($action) => $action->getName())->all())
        ->toContain('edit', 'delete');

    expect(collect($bulkActions)->first()->getFlatActions()->keys()->all())
        ->toContain('delete');
});

test('synonym resource form has expected fields', function () {
    $resourceContents = file_get_contents(__DIR__ . '/../../../src/Resources/SynonymResource.php');

    expect($resourceContents)
        ->toContain("Select::make('model_type')")
        ->toContain("TextInput::make('word')")
        ->toContain("Repeater::make('synonyms')")
        ->toContain("KeyValue::make('engine_settings')");
});

test('synonym resource pages are registered', function () {
    $pages = SynonymResource::getPages();

    expect($pages)
        ->toHaveKeys(['index', 'create', 'edit'])
        ->and($pages['index'])->toBeInstanceOf(Filament\Resources\Pages\PageRegistration::class)
        ->and($pages['create'])->toBeInstanceOf(Filament\Resources\Pages\PageRegistration::class)
        ->and($pages['edit'])->toBeInstanceOf(Filament\Resources\Pages\PageRegistration::class);
});
