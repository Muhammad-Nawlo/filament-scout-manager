<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use MuhammadNawlo\FilamentScoutManager\Models\Synonym;

class SynonymResource extends Resource
{
    protected static ?string $model = Synonym::class;

    protected static string | null | \BackedEnum $navigationIcon = 'heroicon-o-link';

    protected static ?string $slug = 'synonyms';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-scout-manager::filament-scout-manager.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.navigation.synonyms');
    }

    public static function getModelLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.synonyms.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.synonyms.plural');
    }

    public static function form($form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament-scout-manager::filament-scout-manager.synonyms.sections.group'))
                    ->schema([
                        Forms\Components\Select::make('model_type')
                            ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.model'))
                            ->options(function () {
                                $classes = SearchableModelResource::getSearchableModelClasses();

                                return array_combine($classes, array_map(fn ($class) => class_basename($class), $classes));
                            })
                            ->required()
                            ->searchable()
                            ->helperText(__('filament-scout-manager::filament-scout-manager.synonyms.helpers.model')),

                        Forms\Components\TextInput::make('word')
                            ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.word'))
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('filament-scout-manager::filament-scout-manager.synonyms.helpers.word')),

                        Forms\Components\Repeater::make('synonyms')
                            ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.synonyms'))
                            ->schema([
                                Forms\Components\TextInput::make('synonym')
                                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.synonym'))
                                    ->required()
                                    ->distinct()
                                    ->maxLength(255),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['synonym'] ?? null)
                            ->defaultItems(0)
                            ->addActionLabel(__('filament-scout-manager::filament-scout-manager.synonyms.actions.add_synonym'))
                            ->reorderable(false)
                            ->grid(2)
                            ->required()
                            ->helperText(__('filament-scout-manager::filament-scout-manager.synonyms.helpers.synonyms')),

                        Forms\Components\KeyValue::make('engine_settings')
                            ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.engine_settings'))
                            ->keyLabel(__('filament-scout-manager::filament-scout-manager.synonyms.fields.setting'))
                            ->valueLabel(__('filament-scout-manager::filament-scout-manager.synonyms.fields.value'))
                            ->helperText(__('filament-scout-manager::filament-scout-manager.synonyms.helpers.engine_settings'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('word')
                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.word'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('model_type')
                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.model'))
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable()
                    ->searchable(),

                TagsColumn::make('synonyms')
                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.synonyms'))
                    ->limit(5)
                    ->separator(', '),

                TextColumn::make('created_at')
                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.updated'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('model_type')
                    ->label(__('filament-scout-manager::filament-scout-manager.synonyms.fields.model'))
                    ->options(function () {
                        $classes = SearchableModelResource::getSearchableModelClasses();

                        return array_combine($classes, array_map(fn ($class) => class_basename($class), $classes));
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('word');
    }

    public static function getPages(): array
    {
        return [
            'index' => \MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource\Pages\ListSynonyms::route('/'),
            'create' => \MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource\Pages\CreateSynonym::route('/create'),
            'edit' => \MuhammadNawlo\FilamentScoutManager\Resources\SynonymResource\Pages\EditSynonym::route('/{record}/edit'),
        ];
    }
}
