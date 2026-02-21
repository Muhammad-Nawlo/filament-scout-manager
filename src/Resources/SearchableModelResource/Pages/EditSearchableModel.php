<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MuhammadNawlo\FilamentScoutManager\Models\SearchableModel;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

class EditSearchableModel extends EditRecord
{
    protected static string $resource = SearchableModelResource::class;

    protected function resolveRecord(int | string $key): Model
    {
        foreach (SearchableModelResource::getSearchableModelClasses() as $class) {
            if (md5($class) === (string) $key) {
                return new SearchableModel([
                    'id' => md5($class),
                    'class' => $class,
                    'name' => $class,
                ]);
            }
        }

        throw (new ModelNotFoundException)->setModel(SearchableModel::class, [$key]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! FilamentScoutManagerSettings::repositoryTableExists()) {
            return $data;
        }

        $settings = app(FilamentScoutManagerSettings::class);
        $modelClass = $this->getRecord()->class;
        $config = $settings->getModelConfig($modelClass) ?? [];

        return array_merge($data, $config);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! FilamentScoutManagerSettings::repositoryTableExists()) {
            return $data;
        }

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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $record;
    }
}
