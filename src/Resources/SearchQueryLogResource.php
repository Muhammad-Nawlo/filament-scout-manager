<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;

class SearchQueryLogResource extends Resource
{
    protected static ?string $model = SearchQueryLog::class;

    protected static string | null | \BackedEnum $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static string | null | \UnitEnum $navigationGroup = 'Search';

    protected static ?string $navigationLabel = 'Search Logs';

    protected static ?string $slug = 'search-logs';

    protected static ?string $modelLabel = 'Search Log';

    protected static ?string $pluralModelLabel = 'Search Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('query')
                    ->label('Query')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('result_count')
                    ->label('Results')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('execution_time')
                    ->label('Time (s)')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->default('Guest')
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('successful')
                    ->label('Success')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('successful')
                    ->label('Successful only')
                    ->query(fn (Builder $query) => $query->where('successful', true))
                    ->toggle(),

                Filter::make('failed')
                    ->label('Failed only')
                    ->query(fn (Builder $query) => $query->where('successful', false))
                    ->toggle(),

                SelectFilter::make('model_type')
                    ->label('Model')
                    ->options(function () {
                        return SearchQueryLog::query()
                            ->distinct()
                            ->pluck('model_type', 'model_type')
                            ->mapWithKeys(fn ($item) => [$item => class_basename($item)])
                            ->toArray();
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Search Log Details')
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->fillForm(fn (SearchQueryLog $record): array => [
                        'query' => $record->query,
                        'model' => class_basename($record->model_type),
                        'results' => $record->result_count,
                        'time' => $record->execution_time . ' seconds',
                        'user' => $record->user?->name ?? 'Guest',
                        'ip' => $record->ip_address,
                        'user_agent' => $record->user_agent,
                        'success' => $record->successful ? 'Yes' : 'No',
                        'created' => $record->created_at->format('Y-m-d H:i:s'),
                    ])
                    ->form([
                        Forms\Components\TextInput::make('query')->disabled(),
                        Forms\Components\TextInput::make('model')->disabled(),
                        Forms\Components\TextInput::make('results')->disabled(),
                        Forms\Components\TextInput::make('time')->disabled(),
                        Forms\Components\TextInput::make('user')->disabled(),
                        Forms\Components\TextInput::make('ip')->disabled(),
                        Forms\Components\Textarea::make('user_agent')->disabled()->columnSpanFull(),
                        Forms\Components\TextInput::make('success')->disabled(),
                        Forms\Components\TextInput::make('created')->disabled(),
                    ]),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('prune_old')
                        ->label('Prune Older Than...')
                        ->icon('heroicon-o-trash')
                        ->color('warning')
                        ->form([
                            Forms\Components\DateTimePicker::make('before')
                                ->label('Delete logs before')
                                ->required()
                                ->default(now()->subDays(config('filament-scout-manager.log_retention_days', 30))),
                        ])
                        ->action(function (array $data) {
                            $deleted = SearchQueryLog::where('created_at', '<', $data['before'])->delete();
                            Notification::make()
                                ->title("Deleted {$deleted} old search logs.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \MuhammadNawlo\FilamentScoutManager\Resources\SearchQueryLogResource\Pages\ListSearchQueryLogs::route('/'),
        ];
    }
}
