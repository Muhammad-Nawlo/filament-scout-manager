<?php

namespace MuhammadNawlo\FilamentScoutManager\Concerns;

use Laravel\Scout\EngineManager;
use MuhammadNawlo\FilamentScoutManager\DTO\ScoutModelConfigDTO;
use MuhammadNawlo\FilamentScoutManager\Services\ScoutModelConfigService;

/**
 * Opt-in trait to apply Filament Scout Manager stored config at runtime.
 *
 * This trait defines searchableUsing(), searchableAs(), and toSearchableArray(), which
 * collide with Laravel Scout's Searchable trait. To avoid "trait method collides" errors,
 * use SearchableWithScoutManagerConfig instead (one trait that composes both and resolves
 * the conflict). Only use this trait directly if you resolve the conflict manually, e.g.:
 *
 *   use Searchable, UsesScoutManagerConfig {
 *       UsesScoutManagerConfig::searchableUsing insteadof Searchable;
 *       UsesScoutManagerConfig::searchableAs insteadof Searchable;
 *       UsesScoutManagerConfig::toSearchableArray insteadof Searchable;
 *   }
 */
trait UsesScoutManagerConfig
{
    public function searchableAs(): string
    {
        $config = $this->scoutManagerConfig();

        if ($config?->indexName !== null && $config->indexName !== '') {
            return $config->indexName;
        }

        $parentClass = get_parent_class($this);
        if ($parentClass && method_exists($parentClass, 'searchableAs')) {
            return parent::searchableAs();
        }

        return config('scout.prefix', '') . $this->getTable();
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $parentClass = get_parent_class($this);
        $base = ($parentClass && method_exists($parentClass, 'toSearchableArray'))
            ? parent::toSearchableArray()
            : $this->toArray();

        if (! is_array($base)) {
            return [];
        }

        $config = $this->scoutManagerConfig();
        if ($config?->searchableFields === null || $config->searchableFields === []) {
            return $base;
        }

        $filtered = [];
        foreach ($config->searchableFields as $key) {
            if (array_key_exists($key, $base)) {
                $filtered[$key] = $base[$key];
            }
        }

        return $filtered;
    }

    /**
     * @return \Laravel\Scout\Engines\Engine
     */
    public function searchableUsing()
    {
        $config = $this->scoutManagerConfig();

        if ($config?->engine !== null && $config->engine !== '') {
            try {
                return app(EngineManager::class)->engine($config->engine);
            } catch (\Throwable) {
                // Fall through to default
            }
        }

        $parentClass = get_parent_class($this);
        if ($parentClass && method_exists($parentClass, 'searchableUsing')) {
            return parent::searchableUsing();
        }

        return app(EngineManager::class)->engine();
    }

    /**
     * Return the queue connection name from stored config, if any.
     *
     * Scout queue is global; this is advisory unless a custom job implementation
     * reads this value. Does not override Scout's global queue config.
     */
    public function scoutQueueConnection(): ?string
    {
        $config = $this->scoutManagerConfig();

        return $config?->queueConnection;
    }

    private function scoutManagerConfig(): ?ScoutModelConfigDTO
    {
        try {
            $service = app(ScoutModelConfigService::class);
        } catch (\Throwable) {
            return null;
        }

        return $service->forModel(get_class($this));
    }
}
