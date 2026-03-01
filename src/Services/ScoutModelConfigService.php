<?php

namespace MuhammadNawlo\FilamentScoutManager\Services;

use MuhammadNawlo\FilamentScoutManager\DTO\ScoutModelConfigDTO;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

class ScoutModelConfigService
{
    /**
     * @var array<string, ScoutModelConfigDTO|null>
     */
    protected array $cache = [];

    public function forModel(string $modelClass): ?ScoutModelConfigDTO
    {
        if (array_key_exists($modelClass, $this->cache)) {
            return $this->cache[$modelClass];
        }

        $this->cache[$modelClass] = $this->resolve($modelClass);

        return $this->cache[$modelClass];
    }

    private function resolve(string $modelClass): ?ScoutModelConfigDTO
    {
        try {
            if (! FilamentScoutManagerSettings::repositoryTableExists()) {
                return null;
            }

            $settings = app(FilamentScoutManagerSettings::class);
            $config = $settings->getModelConfig($modelClass);

            if (! is_array($config) || empty($config)) {
                return null;
            }

            $indexName = isset($config['index_name_override']) && $config['index_name_override'] !== ''
                ? (string) $config['index_name_override']
                : null;

            $searchableFields = null;
            if (isset($config['searchable_fields']) && is_array($config['searchable_fields'])) {
                $searchableFields = array_values(array_filter(array_map('strval', $config['searchable_fields'])));
            }

            $engine = isset($config['engine_override']) && $config['engine_override'] !== ''
                ? (string) $config['engine_override']
                : null;

            $engineSettings = isset($config['engine_settings']) && is_array($config['engine_settings'])
                ? $config['engine_settings']
                : null;

            $queueConnection = isset($config['queue_connection']) && $config['queue_connection'] !== ''
                ? (string) $config['queue_connection']
                : null;

            return new ScoutModelConfigDTO(
                indexName: $indexName,
                searchableFields: $searchableFields !== [] ? $searchableFields : null,
                engine: $engine,
                engineSettings: $engineSettings,
                queueConnection: $queueConnection,
            );
        } catch (\Throwable) {
            return null;
        }
    }
}
