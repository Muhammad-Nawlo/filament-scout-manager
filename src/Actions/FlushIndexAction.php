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

        $this->label('Flush Index')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->action(function ($record) {
                $modelClass = $record->class ?? $record;

                try {
                    $modelClass::removeAllFromSearch();
                    Notification::make()
                        ->title("Successfully flushed all {$modelClass} records from search index.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed to flush: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading('Flush Search Index')
            ->modalDescription('This will remove all records from the search index. This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, flush them');
    }
}
