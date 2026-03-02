<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Laravel\Scout\Engines\AlgoliaEngine;
use Laravel\Scout\Engines\DatabaseEngine;
use Laravel\Scout\Engines\MeilisearchEngine;
use Laravel\Scout\Engines\TypesenseEngine;
use MuhammadNawlo\FilamentScoutManager\Actions\FlushIndexAction;
use MuhammadNawlo\FilamentScoutManager\Actions\ImportToScoutAction;
use MuhammadNawlo\FilamentScoutManager\Actions\RefreshIndexAction;
use MuhammadNawlo\FilamentScoutManager\Actions\SyncIndexSettingsAction;
use MuhammadNawlo\FilamentScoutManager\Models\SearchableModel;
use MuhammadNawlo\FilamentScoutManager\Services\IndexedCountResolver;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;
use MuhammadNawlo\FilamentScoutManager\Tables\Columns\SearchableFieldsColumn;

class SearchableModelResource extends Resource
{
    protected static ?string $model = SearchableModel::class;

    protected static string | null | \BackedEnum $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'searchable-models';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-scout-manager::filament-scout-manager.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.navigation.models');
    }

    public static function getModelLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.models.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.models.plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => class_basename($state)),

                TextColumn::make('index_name')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.index_name'))
                    ->getStateUsing(function ($record) {
                        try {
                            $modelClass = $record->getAttribute('class');
                            $model = new $modelClass;

                            return $model->searchableAs();
                        } catch (\Throwable) {
                            return __('filament-scout-manager::filament-scout-manager.common.not_available');
                        }
                    }),

                TextColumn::make('total_records')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.total_records'))
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        try {
                            $modelClass = $record->getAttribute('class');

                            return $modelClass::count();
                        } catch (\Exception $e) {
                            return __('filament-scout-manager::filament-scout-manager.common.not_available');
                        }
                    }),

                TextColumn::make('indexed_records')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.indexed_records'))
                    ->getStateUsing(function ($record) {
                        try {
                            $modelClass = $record->getAttribute('class');
                            $model = new $modelClass;
                            $count = app(IndexedCountResolver::class)->resolve($model);

                            return $count !== null ? (string) $count : __('filament-scout-manager::filament-scout-manager.common.not_available');
                        } catch (\Throwable) {
                            return __('filament-scout-manager::filament-scout-manager.models.helpers.not_indexed');
                        }
                    }),

                SearchableFieldsColumn::make('searchable_fields')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.searchable_fields')),

                TextColumn::make('engine')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.engine'))
                    ->getStateUsing(function ($record) {
                        try {
                            $modelClass = $record->getAttribute('class');
                            $model = new $modelClass;

                            return $model->searchableUsing();
                        } catch (\Throwable) {
                            return null;
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return __('filament-scout-manager::filament-scout-manager.common.not_available');
                        }
                        if ($state instanceof TypesenseEngine) {
                            return __('filament-scout-manager::filament-scout-manager.models.engine_badges.typesense');
                        }
                        if ($state instanceof AlgoliaEngine) {
                            return 'Algolia';
                        }
                        if ($state instanceof MeilisearchEngine) {
                            return 'Meilisearch';
                        }
                        if ($state instanceof DatabaseEngine) {
                            return __('filament-scout-manager::filament-scout-manager.models.engine_badges.database');
                        }
                        if ($state instanceof \Laravel\Scout\Engines\CollectionEngine) {
                            return __('filament-scout-manager::filament-scout-manager.models.engine_badges.collection');
                        }

                        return class_basename($state);
                    })
                    ->tooltip(function ($state) {
                        if ($state === null || ! is_object($state)) {
                            return null;
                        }
                        $known = [
                            AlgoliaEngine::class,
                            MeilisearchEngine::class,
                            TypesenseEngine::class,
                            DatabaseEngine::class,
                            \Laravel\Scout\Engines\CollectionEngine::class,
                        ];
                        if (in_array(get_class($state), $known, true)) {
                            return null;
                        }

                        return get_class($state);
                    })
                    ->badge()
                    ->color(function ($state): string {
                        if ($state === null) {
                            return 'gray';
                        }
                        if ($state instanceof TypesenseEngine) {
                            return 'info';
                        }
                        if ($state instanceof AlgoliaEngine) {
                            return 'danger';
                        }
                        if ($state instanceof MeilisearchEngine) {
                            return 'warning';
                        }
                        if ($state instanceof DatabaseEngine) {
                            return 'success';
                        }
                        if ($state instanceof \Laravel\Scout\Engines\CollectionEngine) {
                            return 'gray';
                        }

                        return 'gray';
                    }),

                IconColumn::make('is_searchable')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.searchable'))
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        $modelClass = $record->getAttribute('class');

                        return $modelClass && in_array(
                            \Laravel\Scout\Searchable::class,
                            class_uses_recursive($modelClass)
                        );
                    }),

                IconColumn::make('has_custom_settings')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.custom'))
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        if (! FilamentScoutManagerSettings::repositoryTableExists()) {
                            return false;
                        }

                        $settings = app(FilamentScoutManagerSettings::class);

                        return $settings->getModelConfig($record->getAttribute('class')) !== null;
                    })
                    ->trueIcon('heroicon-o-cog')
                    ->falseIcon('heroicon-o-x-mark'),

                TextColumn::make('last_sync')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.last_sync'))
                    ->getStateUsing(fn () => __('filament-scout-manager::filament-scout-manager.common.not_available')),
            ])
            ->actions([
                ActionGroup::make([
                    ImportToScoutAction::make('import')
                        ->visible(fn ($record) => static::isSearchable($record->getAttribute('class'))),

                    FlushIndexAction::make('flush')
                        ->visible(fn ($record) => static::isSearchable($record->getAttribute('class'))),

                    RefreshIndexAction::make('refresh')
                        ->visible(fn ($record) => static::isSearchable($record->getAttribute('class'))),

                    SyncIndexSettingsAction::make('sync_index_settings')
                        ->visible(fn ($record) => static::isSearchable($record->getAttribute('class'))),

                    EditAction::make('configure')
                        ->label(__('filament-scout-manager::filament-scout-manager.models.actions.configure'))
                        ->icon('heroicon-o-cog')
                        ->url(fn ($record) => static::getUrl('edit', ['record' => $record->id]))
                        ->visible(fn ($record) => static::isSearchable($record->getAttribute('class'))),
                ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('syncAll')
                    ->label(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.label'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.modal_heading'))
                    ->modalDescription(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.modal_description'))
                    ->modalSubmitActionLabel(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.modal_submit'))
                    ->action(function (): void {
                        try {
                            app(\MuhammadNawlo\FilamentScoutManager\Services\ScoutIndexSettingsService::class)->apply();
                            \Illuminate\Support\Facades\Artisan::call('scout:sync-index-settings');
                            $output = trim(\Illuminate\Support\Facades\Artisan::output());
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.success'))
                                ->body($output ?: null)
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.failed', ['message' => $e->getMessage()]))
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_import')
                        ->label(__('filament-scout-manager::filament-scout-manager.models.actions.bulk_import'))
                        ->icon('heroicon-o-arrow-up-on-square')
                        ->action(function (Collection $records) {
                            $successCount = 0;
                            $failCount = 0;
                            foreach ($records as $record) {
                                try {
                                    $modelClass = $record->getAttribute('class');
                                    if ($modelClass && static::isSearchable($modelClass)) {
                                        $modelClass::makeAllSearchable();
                                        $successCount++;
                                    }
                                } catch (\Exception $e) {
                                    $failCount++;
                                }
                            }
                            Notification::make()
                                ->title(__('filament-scout-manager::filament-scout-manager.models.notifications.import_completed', ['success' => $successCount, 'fail' => $failCount]))
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_flush')
                        ->label(__('filament-scout-manager::filament-scout-manager.models.actions.bulk_flush'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $successCount = 0;
                            $failCount = 0;
                            foreach ($records as $record) {
                                try {
                                    $modelClass = $record->getAttribute('class');
                                    if ($modelClass && static::isSearchable($modelClass)) {
                                        $modelClass::removeAllFromSearch();
                                        $successCount++;
                                    }
                                } catch (\Exception $e) {
                                    $failCount++;
                                }
                            }
                            Notification::make()
                                ->title(__('filament-scout-manager::filament-scout-manager.models.notifications.flush_completed', ['success' => $successCount, 'fail' => $failCount]))
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function form($form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make(__('filament-scout-manager::filament-scout-manager.models.sections.configuration'))
                    ->schema([
                        Forms\Components\Hidden::make('class'),

                        Forms\Components\Placeholder::make('model_class')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.model_class'))
                            ->content(fn ($record) => $record instanceof \Illuminate\Database\Eloquent\Model ? ($record->getAttribute('class') ?? '') : ''),

                        Forms\Components\Select::make('searchable_fields')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.searchable_fields'))
                            ->multiple()
                            ->preload()
                            ->options(function (Get $get) {
                                $modelClass = $get('class');

                                return $modelClass
                                    ? static::getModelFields($modelClass)
                                    : [];
                            })
                            ->searchable()
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.searchable_fields'))
                            ->reactive(),
                        Forms\Components\Select::make('engine_override')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.engine_override'))
                            ->options([
                                '' => __('filament-scout-manager::filament-scout-manager.models.engine_options.default'),
                                'algolia' => __('filament-scout-manager::filament-scout-manager.models.engine_options.algolia'),
                                'meilisearch' => __('filament-scout-manager::filament-scout-manager.models.engine_options.meilisearch'),
                                'typesense' => __('filament-scout-manager::filament-scout-manager.models.engine_options.typesense'),
                                'database' => __('filament-scout-manager::filament-scout-manager.models.engine_options.database'),
                                'collection' => __('filament-scout-manager::filament-scout-manager.models.engine_options.collection'),
                            ])
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.engine_override'))
                            ->live(),

                        Forms\Components\Placeholder::make('engine_settings_hint')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.models.helpers.engine_settings'))
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => in_array($get('engine_override'), ['algolia', 'meilisearch'], true)),

                        Forms\Components\Placeholder::make('typesense_help')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.engine_settings.typesense.help'))
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('engine_override') === 'typesense'),
                    ]),

                Section::make(__('filament-scout-manager::filament-scout-manager.engine_settings.meilisearch.section'))
                    ->schema([
                        Forms\Components\TagsInput::make('meilisearch_filterable_attributes')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.meilisearch.filterable_attributes'))
                            ->placeholder('e.g. id, status, category'),
                        Forms\Components\TagsInput::make('meilisearch_sortable_attributes')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.meilisearch.sortable_attributes'))
                            ->placeholder('e.g. created_at, updated_at'),
                        Forms\Components\TagsInput::make('meilisearch_searchable_attributes')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.meilisearch.searchable_attributes'))
                            ->placeholder('e.g. title, description'),
                        Forms\Components\Placeholder::make('meilisearch_help')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.engine_settings.meilisearch.help')),
                    ])
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('engine_override') === 'meilisearch')
                    ->columns(1),

                Section::make(__('filament-scout-manager::filament-scout-manager.engine_settings.algolia.section'))
                    ->schema([
                        Forms\Components\TagsInput::make('algolia_searchable_attributes')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.algolia.searchable_attributes'))
                            ->placeholder('e.g. id, name, email'),
                        Forms\Components\TagsInput::make('algolia_attributes_for_faceting')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.algolia.attributes_for_faceting'))
                            ->placeholder('e.g. filterOnly(email), searchable(category)'),
                        Forms\Components\TagsInput::make('algolia_ranking')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.algolia.ranking'))
                            ->placeholder('e.g. typo, words, proximity'),
                        Forms\Components\TagsInput::make('algolia_custom_ranking')
                            ->label(__('filament-scout-manager::filament-scout-manager.engine_settings.algolia.custom_ranking'))
                            ->placeholder('e.g. asc(name), desc(created_at)'),
                        Forms\Components\Placeholder::make('algolia_help')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.engine_settings.algolia.help')),
                    ])
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('engine_override') === 'algolia')
                    ->columns(1),

                Section::make(__('filament-scout-manager::filament-scout-manager.models.sections.index_settings'))
                    ->schema([
                        Forms\Components\TextInput::make('index_name_override')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.index_name_override'))
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.index_name_override')),

                        Forms\Components\Toggle::make('should_be_searchable')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.should_be_searchable'))
                            ->default(true)
                            ->disabled()
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.should_be_searchable_help')),

                        Forms\Components\Select::make('queue_connection')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.queue_connection'))
                            ->options([
                                'sync' => __('filament-scout-manager::filament-scout-manager.models.queue_options.sync'),
                                'database' => __('filament-scout-manager::filament-scout-manager.models.queue_options.database'),
                                'redis' => __('filament-scout-manager::filament-scout-manager.models.queue_options.redis'),
                                'sqs' => __('filament-scout-manager::filament-scout-manager.models.queue_options.sqs'),
                            ])
                            ->default(config('scout.queue'))
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.queue_connection_help')),

                        Forms\Components\Placeholder::make('database_engine_help')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.models.helpers.database_engine_help'))
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('engine_override') === 'database'),
                    ]),

                Section::make(__('filament-scout-manager::filament-scout-manager.models.sections.batch_indexing_tips'))
                    ->schema([
                        Forms\Components\Placeholder::make('batch_indexing_tips_content')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.models.helpers.batch_indexing_tips_content')),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make(__('filament-scout-manager::filament-scout-manager.models.sections.database_engine_tips'))
                    ->schema([
                        Forms\Components\Placeholder::make('database_engine_tips_content')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.models.helpers.database_engine_tips_content')),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('engine_override') === 'database'),

                Section::make(__('filament-scout-manager::filament-scout-manager.models.sections.import_optimization_tips'))
                    ->schema([
                        Forms\Components\Placeholder::make('import_optimization_tips_content')
                            ->label('')
                            ->content(__('filament-scout-manager::filament-scout-manager.models.helpers.import_optimization_tips_content')),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource\Pages\ListSearchableModels::route('/'),
            'edit' => \MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource\Pages\EditSearchableModel::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $classes = static::getSearchableModelClasses();

        if ($classes === []) {
            return SearchableModel::query()
                ->selectRaw('NULL as id, NULL as class, NULL as name')
                ->whereRaw('1 = 0');
        }

        $query = null;

        foreach ($classes as $class) {
            $select = DB::query()->selectRaw('? as id, ? as class, ? as name', [
                md5($class),
                $class,
                $class,
            ]);

            $query = $query === null ? $select : $query->unionAll($select);
        }

        return SearchableModel::query()->fromSub($query, 'searchable_models');
    }

    public static function isSearchable(string $class): bool
    {
        return in_array(\Laravel\Scout\Searchable::class, class_uses_recursive($class));
    }

    public static function getSearchableModelClasses(): array
    {
        $classes = [];
        $modelPath = app_path('Models');

        if (File::exists($modelPath)) {
            foreach (File::allFiles($modelPath) as $file) {
                $relativePath = $file->getRelativePathName();
                $class = 'App\\Models\\' . str_replace('/', '\\', pathinfo($relativePath, PATHINFO_FILENAME));

                if (class_exists($class) && static::isSearchable($class)) {
                    $classes[] = $class;
                }
            }
        }

        $configured = config('filament-scout-manager.models', []);
        foreach ($configured as $modelClass => $settings) {
            if (class_exists($modelClass) && static::isSearchable($modelClass) && ! in_array($modelClass, $classes)) {
                $classes[] = $modelClass;
            }
        }

        return $classes;
    }

    protected static function getModelFields($class): array
    {
        try {
            $model = new $class;
            $table = $model->getTable();
            $columns = Schema::getColumnListing($table);

            return array_combine($columns, $columns);
        } catch (\Exception $e) {
            return [];
        }
    }
}
