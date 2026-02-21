<?php

namespace MuhammadNawlo\FilamentScoutManager\Actions;

use Filament\Notifications\Notification;
use Filament\Actions\Action;

class RefreshIndexAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'refresh';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Refresh Index')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->action(function ($record) {
                $modelClass = $record->class ?? $record;

                try {
                    $modelClass::removeAllFromSearch();
                    $modelClass::makeAllSearchable();
                    Notification::make()
                        ->title("Successfully refreshed index for {$modelClass}.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed to refresh: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading('Refresh Search Index')
            ->modalDescription('This will remove all records and re-import them. This might take a while for large datasets.')
            ->modalSubmitActionLabel('Yes, refresh');
    }
}
