<?php

namespace MuhammadNawlo\FilamentScoutManager\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ImportToScoutAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-scout-manager::filament-scout-manager.actions.import.label'))
            ->icon('heroicon-o-arrow-up-on-square')
            ->color('success')
            ->action(function ($record) {
                $modelClass = $record->class ?? $record;

                try {
                    $modelClass::makeAllSearchable();
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.import.success', ['model' => $modelClass]))
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('filament-scout-manager::filament-scout-manager.actions.import.failed', ['message' => $e->getMessage()]))
                        ->danger()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading(__('filament-scout-manager::filament-scout-manager.actions.import.modal_heading'))
            ->modalDescription(__('filament-scout-manager::filament-scout-manager.actions.import.modal_description'))
            ->modalSubmitActionLabel(__('filament-scout-manager::filament-scout-manager.actions.import.modal_submit'));
    }
}
