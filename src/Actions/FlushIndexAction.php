<?php

namespace MuhammadNawlo\FilamentScoutManager\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class FlushIndexAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'flush';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-scout-manager::filament-scout-manager.actions.flush.label'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->action(function ($record) {
                $modelClass = $record->class ?? $record;

                try {
                    $modelClass::removeAllFromSearch();
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.flush.success', ['model' => $modelClass]))
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.flush.failed', ['message' => $e->getMessage()]))
                        ->danger()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading(__('filament-scout-manager::filament-scout-manager.actions.flush.modal_heading'))
            ->modalDescription(__('filament-scout-manager::filament-scout-manager.actions.flush.modal_description'))
            ->modalSubmitActionLabel(__('filament-scout-manager::filament-scout-manager.actions.flush.modal_submit'));
    }
}
