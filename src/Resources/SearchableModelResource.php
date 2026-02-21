<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use MuhammadNawlo\FilamentScoutManager\Actions\FlushIndexAction;
use MuhammadNawlo\FilamentScoutManager\Actions\ImportToScoutAction;
use MuhammadNawlo\FilamentScoutManager\Actions\RefreshIndexAction;
use MuhammadNawlo\FilamentScoutManager\Models\SearchableModel;
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
                        $model = new $record->class;

                        return $model->searchableAs();
                    }),

                TextColumn::make('total_records')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.total_records'))
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        try {
                            return $record->class::count();
                        } catch (\Exception $e) {
                            return __('filament-scout-manager::filament-scout-manager.common.not_available');
                        }
                    }),

                TextColumn::make('indexed_records')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.indexed_records'))
                    ->getStateUsing(function ($record) {
                        try {
                            $raw = $record->class::search('')->raw();

                            return $raw['nbHits'] ?? __('filament-scout-manager::filament-scout-manager.common.not_available');
                        } catch (\Exception $e) {
                            return __('filament-scout-manager::filament-scout-manager.models.helpers.not_indexed');
                        }
                    }),

                SearchableFieldsColumn::make('searchable_fields')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.searchable_fields')),

                TextColumn::make('engine')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.engine'))
                    ->getStateUsing(function ($record) {
                        $model = new $record->class;
                        $engine = $model->searchableUsing();

                        return class_basename($engine);
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AlgoliaEngine' => 'danger',
                        'MeilisearchEngine' => 'warning',
                        'DatabaseEngine' => 'success',
                        'CollectionEngine' => 'gray',
                        default => 'gray',
                    }),

                IconColumn::make('is_searchable')
                    ->label(__('filament-scout-manager::filament-scout-manager.models.fields.searchable'))
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        return in_array(
                            \Laravel\Scout\Searchable::class,
                            class_uses_recursive($record->class)
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

                        return $settings->getModelConfig($record->class) !== null;
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
                        ->visible(fn ($record) => static::isSearchable($record->class)),

                    FlushIndexAction::make('flush')
                        ->visible(fn ($record) => static::isSearchable($record->class)),

                    RefreshIndexAction::make('refresh')
                        ->visible(fn ($record) => static::isSearchable($record->class)),

                    EditAction::make('configure')
                        ->label(__('filament-scout-manager::filament-scout-manager.models.actions.configure'))
                        ->icon('heroicon-o-cog')
                        ->url(fn ($record) => static::getUrl('edit', ['record' => $record->id]))
                        ->visible(fn ($record) => static::isSearchable($record->class)),
                ]),
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
                                    if (static::isSearchable($record->class)) {
                                        $record->class::makeAllSearchable();
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
                                    if (static::isSearchable($record->class)) {
                                        $record->class::removeAllFromSearch();
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
                        Forms\Components\Placeholder::make('model_class')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.model_class'))
                            ->content(fn ($record) => $record->class ?? ''),

                        Forms\Components\Select::make('searchable_fields')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.searchable_fields'))
                            ->multiple()
                            ->options(function ($record) {
                                return static::getModelFields($record->class ?? $record);
                            })
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.searchable_fields')),

                        Forms\Components\Select::make('engine_override')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.engine_override'))
                            ->options([
                                '' => __('filament-scout-manager::filament-scout-manager.models.engine_options.default'),
                                'algolia' => __('filament-scout-manager::filament-scout-manager.models.engine_options.algolia'),
                                'meilisearch' => __('filament-scout-manager::filament-scout-manager.models.engine_options.meilisearch'),
                                'database' => __('filament-scout-manager::filament-scout-manager.models.engine_options.database'),
                                'collection' => __('filament-scout-manager::filament-scout-manager.models.engine_options.collection'),
                            ])
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.engine_override')),

                        Forms\Components\KeyValue::make('engine_settings')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.engine_settings'))
                            ->keyLabel(__('filament-scout-manager::filament-scout-manager.synonyms.fields.setting'))
                            ->valueLabel(__('filament-scout-manager::filament-scout-manager.synonyms.fields.value'))
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.engine_settings')),
                    ]),

                Section::make(__('filament-scout-manager::filament-scout-manager.models.sections.index_settings'))
                    ->schema([
                        Forms\Components\TextInput::make('index_name_override')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.index_name_override'))
                            ->helperText(__('filament-scout-manager::filament-scout-manager.models.helpers.index_name_override')),

                        Forms\Components\Toggle::make('should_be_searchable')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.should_be_searchable'))
                            ->default(true)
                            ->disabled(),

                        Forms\Components\Select::make('queue_connection')
                            ->label(__('filament-scout-manager::filament-scout-manager.models.fields.queue_connection'))
                            ->options([
                                'sync' => __('filament-scout-manager::filament-scout-manager.models.queue_options.sync'),
                                'database' => __('filament-scout-manager::filament-scout-manager.models.queue_options.database'),
                                'redis' => __('filament-scout-manager::filament-scout-manager.models.queue_options.redis'),
                                'sqs' => __('filament-scout-manager::filament-scout-manager.models.queue_options.sqs'),
                            ])
                            ->default(config('scout.queue')),
                    ]),
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

    protected static function isSearchable(string $class): bool
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
