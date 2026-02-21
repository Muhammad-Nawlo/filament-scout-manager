<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;
use MuhammadNawlo\FilamentScoutManager\Models\Synonym;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;

class SynonymResource extends Resource
{
    protected static ?string $model = Synonym::class;

    protected static string | null | \BackedEnum $navigationIcon = 'heroicon-o-link';

    protected static string | null | \UnitEnum $navigationGroup = 'Search';

    protected static ?string $navigationLabel = 'Synonyms';

    protected static ?string $slug = 'synonyms';

    protected static ?string $modelLabel = 'Synonym Group';

    protected static ?string $pluralModelLabel = 'Synonyms';

    public static function form($form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Synonym Group')
                    ->schema([
                        Forms\Components\Select::make('model_type')
                            ->label('Model')
                            ->options(function () {
                                $classes = SearchableModelResource::getSearchableModelClasses();
                                return array_combine($classes, array_map(fn($class) => class_basename($class), $classes));
                            })
                            ->required()
                            ->searchable()
                            ->helperText('Select the model this synonym group applies to.'),

                        Forms\Components\TextInput::make('word')
                            ->label('Word')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The main word that will have synonyms.'),

                        Forms\Components\Repeater::make('synonyms')
                            ->label('Synonyms')
                            ->schema([
                                Forms\Components\TextInput::make('synonym')
                                    ->label('Synonym')
                                    ->required()
                                    ->distinct()
                                    ->maxLength(255),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['synonym'] ?? null)
                            ->defaultItems(0)
                            ->addActionLabel('Add Synonym')
                            ->reorderable(false)
                            ->grid(2)
                            ->required()
                            ->helperText('Enter words that should be treated as synonyms for the main word.'),

                        Forms\Components\KeyValue::make('engine_settings')
                            ->label('Engine-Specific Settings')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->helperText('Additional settings for the selected engine (optional).')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('word')
                    ->label('Word')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable()
                    ->searchable(),

                TagsColumn::make('synonyms')
                    ->label('Synonyms')
                    ->limit(5)
                    ->separator(', '),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('model_type')
                    ->label('Model')
                    ->options(function () {
                        $classes = SearchableModelResource::getSearchableModelClasses();
                        return array_combine($classes, array_map(fn($class) => class_basename($class), $classes));
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
