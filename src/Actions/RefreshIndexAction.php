<?php

namespace MuhammadNawlo\FilamentScoutManager\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class RefreshIndexAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'refresh';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-scout-manager::filament-scout-manager.actions.refresh.label'))
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->action(function ($record) {
                $modelClass = $record->class ?? $record;

                try {
                    $modelClass::removeAllFromSearch();
                    $modelClass::makeAllSearchable();
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.refresh.success', ['model' => $modelClass]))
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.refresh.failed', ['message' => $e->getMessage()]))
                        ->danger()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading(__('filament-scout-manager::filament-scout-manager.actions.refresh.modal_heading'))
            ->modalDescription(__('filament-scout-manager::filament-scout-manager.actions.refresh.modal_description'))
            ->modalSubmitActionLabel(__('filament-scout-manager::filament-scout-manager.actions.refresh.modal_submit'));
    }
}
