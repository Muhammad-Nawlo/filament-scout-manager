<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
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



    protected static ?string $slug = 'search-logs';


    public static function getNavigationGroup(): ?string
    {
        return __('filament-scout-manager::filament-scout-manager.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.navigation.logs');
    }

    public static function getModelLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.logs.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-scout-manager::filament-scout-manager.logs.plural');
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('query')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.query'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model_type')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.model'))
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('result_count')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.results'))
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('execution_time')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.time'))
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.user'))
                    ->default(__('filament-scout-manager::filament-scout-manager.common.guest'))
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.ip_address'))
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('successful')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.success'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.date'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('successful')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.filters.successful_only'))
                    ->query(fn (Builder $query) => $query->where('successful', true))
                    ->toggle(),

                Filter::make('failed')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.filters.failed_only'))
                    ->query(fn (Builder $query) => $query->where('successful', false))
                    ->toggle(),

                SelectFilter::make('model_type')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.model'))
                    ->options(function () {
                        return SearchQueryLog::query()
                            ->distinct()
                            ->pluck('model_type', 'model_type')
                            ->mapWithKeys(fn ($item) => [$item => class_basename($item)])
                            ->toArray();
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.from')),
                        Forms\Components\DatePicker::make('until')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.until')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('filament-scout-manager::filament-scout-manager.logs.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(__('filament-scout-manager::filament-scout-manager.logs.modal.details_heading'))
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament-scout-manager::filament-scout-manager.logs.actions.close'))
                    ->fillForm(fn (SearchQueryLog $record): array => [
                        'query' => $record->query,
                        'model' => class_basename($record->model_type),
                        'results' => $record->result_count,
                        'time' => __('filament-scout-manager::filament-scout-manager.logs.values.seconds', ['seconds' => $record->execution_time]),
                        'user' => $record->user?->name ?? __('filament-scout-manager::filament-scout-manager.common.guest'),
                        'ip' => $record->ip_address,
                        'user_agent' => $record->user_agent,
                        'success' => $record->successful ? __('filament-scout-manager::filament-scout-manager.common.yes') : __('filament-scout-manager::filament-scout-manager.common.no'),
                        'created' => $record->created_at->format('Y-m-d H:i:s'),
                    ])
                    ->form([
                        Forms\Components\TextInput::make('query')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.query'))->disabled(),
                        Forms\Components\TextInput::make('model')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.model'))->disabled(),
                        Forms\Components\TextInput::make('results')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.results'))->disabled(),
                        Forms\Components\TextInput::make('time')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.time'))->disabled(),
                        Forms\Components\TextInput::make('user')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.user'))->disabled(),
                        Forms\Components\TextInput::make('ip')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.ip_address'))->disabled(),
                        Forms\Components\Textarea::make('user_agent')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.user_agent'))->disabled()->columnSpanFull(),
                        Forms\Components\TextInput::make('success')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.success'))->disabled(),
                        Forms\Components\TextInput::make('created')->label(__('filament-scout-manager::filament-scout-manager.logs.fields.created'))->disabled(),
                    ]),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('prune_old')
                        ->label(__('filament-scout-manager::filament-scout-manager.logs.actions.prune_old'))
                        ->icon('heroicon-o-trash')
                        ->color('warning')
                        ->form([
                            Forms\Components\DateTimePicker::make('before')
                                ->label(__('filament-scout-manager::filament-scout-manager.logs.fields.before'))
                                ->required()
                                ->default(now()->subDays(config('filament-scout-manager.log_retention_days', 30))),
                        ])
                        ->action(function (array $data) {
                            $deleted = SearchQueryLog::where('created_at', '<', $data['before'])->delete();
                            Notification::make()
                                ->title(__('filament-scout-manager::filament-scout-manager.logs.notifications.pruned', ['count' => $deleted]))
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
