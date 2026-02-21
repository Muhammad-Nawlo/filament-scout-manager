<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource\Pages;

use Filament\Resources\Pages\EditRecord;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

class EditSearchableModel extends EditRecord
{
    protected static string $resource = SearchableModelResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $settings = app(FilamentScoutManagerSettings::class);
        $modelClass = $this->getRecord()->class;
        $config = $settings->getModelConfig($modelClass) ?? [];

        return array_merge($data, $config);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $settings = app(FilamentScoutManagerSettings::class);
        $modelClass = $this->getRecord()->class;

        $config = [
            'searchable_fields' => $data['searchable_fields'] ?? null,
            'engine_override' => $data['engine_override'] ?? null,
            'engine_settings' => $data['engine_settings'] ?? null,
            'index_name_override' => $data['index_name_override'] ?? null,
            'queue_connection' => $data['queue_connection'] ?? null,
        ];

        $settings->setModelConfig($modelClass, $config);

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
