<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use MuhammadNawlo\FilamentScoutManager\Actions\FlushIndexAction;
use MuhammadNawlo\FilamentScoutManager\Actions\ImportToScoutAction;
use MuhammadNawlo\FilamentScoutManager\Actions\RefreshIndexAction;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;
use MuhammadNawlo\FilamentScoutManager\Tables\Columns\SearchableFieldsColumn;

class SearchableModelResource extends Resource
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cube';
    protected static string|null|\UnitEnum $navigationGroup = 'Search';

    protected static ?string $navigationLabel = 'Searchable Models';

    protected static ?string $slug = 'searchable-models';

    protected static ?string $modelLabel = 'Searchable Model';

    protected static ?string $pluralModelLabel = 'Searchable Models';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Model')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => class_basename($state)),

                TextColumn::make('index_name')
                    ->label('Index Name')
                    ->getStateUsing(function ($record) {
                        $model = new $record->class;

                        return $model->searchableAs();
                    }),

                TextColumn::make('total_records')
                    ->label('Total Records')
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        try {
                            return $record->class::count();
                        } catch (\Exception $e) {
                            return 'N/A';
                        }
                    }),

                TextColumn::make('indexed_records')
                    ->label('Indexed Records')
                    ->getStateUsing(function ($record) {
                        try {
                            $raw = $record->class::search('')->raw();

                            return $raw['nbHits'] ?? 'N/A';
                        } catch (\Exception $e) {
                            return 'Not indexed';
                        }
                    }),

                SearchableFieldsColumn::make('searchable_fields')
                    ->label('Searchable Fields'),

                TextColumn::make('engine')
                    ->label('Search Engine')
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
                    ->label('Searchable')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        return in_array(
                            \Laravel\Scout\Searchable::class,
                            class_uses_recursive($record->class)
                        );
                    }),

                IconColumn::make('has_custom_settings')
                    ->label('Custom')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        $settings = app(FilamentScoutManagerSettings::class);

                        return $settings->getModelConfig($record->class) !== null;
                    })
                    ->trueIcon('heroicon-o-cog')
                    ->falseIcon('heroicon-o-x-mark'),

                TextColumn::make('last_sync')
                    ->label('Last Sync')
                    ->getStateUsing(fn () => 'N/A')
                    ->since(),
            ])
            ->actions([
                ActionGroup::make([
                    ImportToScoutAction::make('import')
                        ->visible(fn ($record) => static::isSearchable($record->class)),

                    FlushIndexAction::make('flush')
                        ->visible(fn ($record) => static::isSearchable($record->class)),

                    RefreshIndexAction::make('refresh')
                        ->visible(fn ($record) => static::isSearchable($record->class)),

                    Tables\Actions\EditAction::make('configure')
                        ->label('Configure')
                        ->icon('heroicon-o-cog')
                        ->url(fn ($record) => static::getUrl('edit', ['record' => $record->id]))
                        ->visible(fn ($record) => static::isSearchable($record->class)),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_import')
                        ->label('Import Selected')
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
                                ->title("Import completed: {$successCount} succeeded, {$failCount} failed.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_flush')
                        ->label('Flush Selected')
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
                                ->title("Flush completed: {$successCount} succeeded, {$failCount} failed.")
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
                Forms\Components\Section::make('Model Configuration')
                    ->schema([
                        Forms\Components\Placeholder::make('model_class')
                            ->label('Model Class')
                            ->content(fn ($record) => $record->class ?? ''),

                        Forms\Components\Select::make('searchable_fields')
                            ->label('Searchable Fields')
                            ->multiple()
                            ->options(function ($record) {
                                return static::getModelFields($record->class ?? $record);
                            })
                            ->helperText('Select which fields should be searchable (requires custom toSearchableArray implementation).'),

                        Forms\Components\Select::make('engine_override')
                            ->label('Override Engine')
                            ->options([
                                '' => 'Use Default',
                                'algolia' => 'Algolia',
                                'meilisearch' => 'Meilisearch',
                                'database' => 'Database Engine',
                                'collection' => 'Collection Engine',
                            ])
                            ->helperText('Override the default search engine for this model.'),

                        Forms\Components\KeyValue::make('engine_settings')
                            ->label('Engine-Specific Settings')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->helperText('Additional settings for the selected engine (e.g., searchableAttributes for Algolia).'),
                    ]),

                Forms\Components\Section::make('Index Settings')
                    ->schema([
                        Forms\Components\TextInput::make('index_name_override')
                            ->label('Custom Index Name')
                            ->helperText('Leave empty to use the default searchableAs.'),

                        Forms\Components\Toggle::make('should_be_searchable')
                            ->label('Enable Searchable')
                            ->default(true)
                            ->disabled(),

                        Forms\Components\Select::make('queue_connection')
                            ->label('Queue Connection')
                            ->options([
                                'sync' => 'Sync (No Queue)',
                                'database' => 'Database',
                                'redis' => 'Redis',
                                'sqs' => 'SQS',
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $classes = static::getSearchableModelClasses();

        $items = [];
        foreach ($classes as $class) {
            $items[] = new class($class)
            {
                public $id;

                public $class;

                public function __construct($class)
                {
                    $this->id = md5($class);
                    $this->class = $class;
                }

                public function __get($key)
                {
                    if ($key === 'id') {
                        return $this->id;
                    }

                    return null;
                }
            };
        }

        return new Collection($items);
    }

    protected static function isSearchable(string $class): bool
    {
        return in_array(\Laravel\Scout\Searchable::class, class_uses_recursive($class));
    }

    protected static function getSearchableModelClasses(): array
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
