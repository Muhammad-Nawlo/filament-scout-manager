<?php

use MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource;
use MuhammadNawlo\FilamentScoutManager\Models\Synonym;
use Filament\Tables\Table;
use Filament\Forms\Form;

test('synonym resource has correct navigation properties', function () {
    expect(SynonymResource::getModel())->toBe(Synonym::class);
    expect(SynonymResource::getNavigationIcon())->toBe('heroicon-o-link');
    expect(SynonymResource::getNavigationGroup())->toBe(__('filament-scout-manager::navigation.group'));
    expect(SynonymResource::getNavigationLabel())->toBe(__('filament-scout-manager::navigation.synonyms'));
    expect(SynonymResource::getSlug())->toBe('synonyms');
    expect(SynonymResource::getModelLabel())->toBe(__('filament-scout-manager::synonyms.title'));
    expect(SynonymResource::getPluralModelLabel())->toBe(__('filament-scout-manager::synonyms.title'));
});

test('synonym resource table has expected columns', function () {
    $table = SynonymResource::table(Table::make());
    $columns = $table->getColumns();

    $columnNames = collect($columns)->map(fn ($col) => $col->getName())->toArray();

    expect($columnNames)->toContain('word');
    expect($columnNames)->toContain('model_type');
    expect($columnNames)->toContain('synonyms');
    expect($columnNames)->toContain('created_at');
    expect($columnNames)->toContain('updated_at');
});

test('synonym resource has expected filters', function () {
    $table = SynonymResource::table(Table::make());
    $filters = $table->getFilters();

    $filterNames = collect($filters)->map(fn ($filter) => $filter->getName())->toArray();

    expect($filterNames)->toContain('model_type');
});

test('synonym resource has expected actions', function () {
    $table = SynonymResource::table(Table::make());
    $actions = $table->getActions();

    $actionNames = collect($actions)->map(fn ($action) => $action->getName())->toArray();

    expect($actionNames)->toContain('edit');
    expect($actionNames)->toContain('delete');
});

test('synonym resource has bulk actions', function () {
    $table = SynonymResource::table(Table::make());
    $bulkActions = $table->getBulkActions();

    $bulkActionNames = collect($bulkActions)->map(fn ($action) => $action->getName())->toArray();

    expect($bulkActionNames)->toContain('delete');
});

test('synonym resource form has expected fields', function () {
    $form = SynonymResource::form(Form::make());
    $schema = $form->getSchema();

    $fieldNames = collect($schema)
        ->flatMap(fn ($section) => $section->getChildComponents())
        ->map(fn ($field) => $field->getName())
        ->filter()
        ->values()
        ->toArray();

    expect($fieldNames)->toContain('model_type');
    expect($fieldNames)->toContain('word');
    expect($fieldNames)->toContain('synonyms');
    expect($fieldNames)->toContain('engine_settings');
});

test('synonym resource pages are registered', function () {
    $pages = SynonymResource::getPages();
    expect($pages)->toHaveKey('index');
    expect($pages['index']->getRoute())->toBe('/');
    expect($pages)->toHaveKey('create');
    expect($pages['create']->getRoute())->toBe('/create');
    expect($pages)->toHaveKey('edit');
    expect($pages['edit']->getRoute())->toBe('/{record}/edit');
});
