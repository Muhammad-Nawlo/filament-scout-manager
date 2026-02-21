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

        $this->label('Import to Index')
            ->icon('heroicon-o-arrow-up-on-square')
            ->color('success')
            ->action(function ($record) {
                $modelClass = $record->class ?? $record;

                try {
                    $modelClass::makeAllSearchable();
                    Notification::make()
                        ->title("Successfully imported all {$modelClass} records.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed to import: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading('Import to Search Index')
            ->modalDescription('This will import all records from this model into the search index. This might take a while for large datasets.')
            ->modalSubmitActionLabel('Yes, import them');
    }
}
