<?php

namespace MuhammadNawlo\FilamentScoutManager\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use MuhammadNawlo\FilamentScoutManager\Services\ScoutIndexSettingsService;

class SyncIndexSettingsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sync_index_settings';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.label'))
            ->icon('heroicon-o-arrow-path')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.modal_heading'))
            ->modalDescription(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.modal_description'))
            ->modalSubmitActionLabel(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.modal_submit'))
            ->action(function (): void {
                try {
                    app(ScoutIndexSettingsService::class)->apply();

                    Artisan::call('scout:sync-index-settings');
                    $output = trim(Artisan::output());

                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.success'))
                        ->body($output ?: null)
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.sync_index_settings.failed', ['message' => $e->getMessage()]))
                        ->danger()
                        ->send();
                }
            });
    }
}
