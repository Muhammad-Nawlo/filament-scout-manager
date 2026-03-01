<?php

namespace MuhammadNawlo\FilamentScoutManager\Services;

use Illuminate\Database\Eloquent\SoftDeletes;
use MuhammadNawlo\FilamentScoutManager\Resources\SearchableModelResource;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

class ScoutIndexSettingsService
{
    public function apply(): void
    {
        if (! FilamentScoutManagerSettings::repositoryTableExists()) {
            return;
        }

        $settings = app(FilamentScoutManagerSettings::class);
        $models = $settings->models;

        if (empty($models)) {
            return;
        }

        $algoliaSettings = config('scout.algolia.index-settings', []);
        $meilisearchSettings = config('scout.meilisearch.index-settings', []);

        foreach ($models as $modelClass => $config) {
            if (! is_string($modelClass) || ! class_exists($modelClass)) {
                continue;
            }

            if (! SearchableModelResource::isSearchable($modelClass)) {
                continue;
            }

            $engineOverride = $config['engine_override'] ?? null;
            $raw = $config['engine_settings'] ?? null;

            if (! is_array($raw)) {
                continue;
            }

            $effectiveEngine = $this->resolveEngine($modelClass, $engineOverride);
            $engineSettings = $this->resolveEngineSettings($raw, $effectiveEngine);
            if (empty($engineSettings)) {
                continue;
            }

            if ($effectiveEngine === 'algolia') {
                $algoliaSettings[$modelClass] = $this->ensureSoftDeleteForAlgolia($modelClass, $engineSettings);
            } elseif ($effectiveEngine === 'meilisearch') {
                $meilisearchSettings[$modelClass] = $this->ensureSoftDeleteForMeilisearch($modelClass, $engineSettings);
            }
        }

        config([
            'scout.algolia.index-settings' => $algoliaSettings,
            'scout.meilisearch.index-settings' => $meilisearchSettings,
        ]);
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function resolveEngineSettings(array $raw, ?string $engine): array
    {
        if ($engine === 'meilisearch' && isset($raw['meilisearch']) && is_array($raw['meilisearch'])) {
            return $raw['meilisearch'];
        }
        if ($engine === 'algolia' && isset($raw['algolia']) && is_array($raw['algolia'])) {
            return $raw['algolia'];
        }

        return $raw;
    }

    private function resolveEngine(string $modelClass, ?string $engineOverride): ?string
    {
        if ($engineOverride === 'algolia' || $engineOverride === 'meilisearch') {
            return $engineOverride;
        }

        try {
            $model = new $modelClass;
            $engine = $model->searchableUsing();
            $name = class_basename($engine);

            return match (true) {
                str_contains($name, 'Algolia') => 'algolia',
                str_contains($name, 'Meilisearch') => 'meilisearch',
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    private function ensureSoftDeleteForAlgolia(string $modelClass, array $settings): array
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            return $settings;
        }

        $faceting = $settings['attributesForFaceting'] ?? [];
        if (! is_array($faceting)) {
            $faceting = [];
        }
        if (! in_array('__soft_deleted', $faceting, true)) {
            $faceting[] = '__soft_deleted';
        }
        $settings['attributesForFaceting'] = $faceting;

        return $settings;
    }

    private function ensureSoftDeleteForMeilisearch(string $modelClass, array $settings): array
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            return $settings;
        }

        $filterable = $settings['filterableAttributes'] ?? [];
        if (! is_array($filterable)) {
            $filterable = [];
        }
        if (! in_array('__soft_deleted', $filterable, true)) {
            $filterable[] = '__soft_deleted';
        }
        $settings['filterableAttributes'] = $filterable;

        return $settings;
    }
}
