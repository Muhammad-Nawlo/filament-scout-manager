<?php

namespace MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MuhammadNawlo\FilamentScoutManager\Models\SearchableModel;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;
use MuhammadNawlo\FilamentScoutManager\Services\ScoutModelConfigService;
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
        $record = $this->getRecord();
        $modelClass = $record->getAttribute('class');
        if (is_string($modelClass)) {
            $data['class'] = $modelClass;
        }

        if (! FilamentScoutManagerSettings::repositoryTableExists()) {
            return $data;
        }

        $settings = app(FilamentScoutManagerSettings::class);
        $config = $settings->getModelConfig($modelClass) ?? [];

        $data = array_merge($data, $config);

        $engineSettings = $config['engine_settings'] ?? [];
        if (is_array($engineSettings)) {
            $meilisearch = $engineSettings['meilisearch'] ?? $engineSettings;
            $algolia = $engineSettings['algolia'] ?? $engineSettings;
            if (! is_array($meilisearch)) {
                $meilisearch = [];
            }
            if (! is_array($algolia)) {
                $algolia = [];
            }
            $data['meilisearch_filterable_attributes'] = $meilisearch['filterableAttributes'] ?? [];
            $data['meilisearch_sortable_attributes'] = $meilisearch['sortableAttributes'] ?? [];
            $data['meilisearch_searchable_attributes'] = $meilisearch['searchableAttributes'] ?? [];
            $data['algolia_searchable_attributes'] = $algolia['searchableAttributes'] ?? [];
            $data['algolia_attributes_for_faceting'] = $algolia['attributesForFaceting'] ?? [];
            $data['algolia_ranking'] = $algolia['ranking'] ?? [];
            $data['algolia_custom_ranking'] = $algolia['customRanking'] ?? [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $modelClass = $data['class'] ?? $this->getRecord()->getAttribute('class');
        if (! is_string($modelClass) || $modelClass === '') {
            return $data;
        }

        FilamentScoutManagerSettings::ensureSettingsTableExists();

        if (! FilamentScoutManagerSettings::repositoryTableExists()) {
            Notification::make()
                ->title(__('filament-scout-manager::filament-scout-manager.models.notifications.settings_table_missing_title'))
                ->body(__('filament-scout-manager::filament-scout-manager.models.notifications.settings_table_missing_body'))
                ->danger()
                ->persistent()
                ->send();

            return $data;
        }

        $settings = app(FilamentScoutManagerSettings::class);
        $existingConfig = $settings->getModelConfig($modelClass) ?? [];
        $existingEngineSettings = $existingConfig['engine_settings'] ?? [];
        if (! is_array($existingEngineSettings)) {
            $existingEngineSettings = [];
        }

        $engineSettings = [
            'meilisearch' => [
                'filterableAttributes' => $this->normalizeArray($data['meilisearch_filterable_attributes'] ?? []),
                'sortableAttributes' => $this->normalizeArray($data['meilisearch_sortable_attributes'] ?? []),
                'searchableAttributes' => $this->normalizeArray($data['meilisearch_searchable_attributes'] ?? []),
            ],
            'algolia' => [
                'searchableAttributes' => $this->normalizeArray($data['algolia_searchable_attributes'] ?? []),
                'attributesForFaceting' => $this->normalizeArray($data['algolia_attributes_for_faceting'] ?? []),
                'ranking' => $this->normalizeArray($data['algolia_ranking'] ?? []),
                'customRanking' => $this->normalizeArray($data['algolia_custom_ranking'] ?? []),
            ],
        ];

        $config = [
            'searchable_fields' => $data['searchable_fields'] ?? null,
            'engine_override' => $data['engine_override'] ?? null,
            'engine_settings' => $engineSettings,
            'index_name_override' => $data['index_name_override'] ?? null,
            'queue_connection' => $data['queue_connection'] ?? null,
        ];

        $settings->setModelConfig($modelClass, $config);

        app(ScoutModelConfigService::class)->clearCache($modelClass);

        Notification::make()
            ->title(__('filament-scout-manager::filament-scout-manager.models.notifications.config_saved'))
            ->success()
            ->send();

        return $data;
    }

    /**
     * @param  mixed  $value
     * @return array<int, string>
     */
    private function normalizeArray($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('strval', $value)));
        }

        return [];
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
