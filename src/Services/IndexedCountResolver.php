<?php

namespace MuhammadNawlo\FilamentScoutManager\Services;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Engines\AlgoliaEngine;
use Laravel\Scout\Engines\DatabaseEngine;
use Laravel\Scout\Engines\MeilisearchEngine;
use Laravel\Scout\Engines\TypesenseEngine;

class IndexedCountResolver
{
    /**
     * Resolve the number of indexed records for a model using engine-specific raw response keys.
     * Returns null when the engine is unknown or the count cannot be determined.
     */
    public function resolve(Model $model): ?int
    {
        try {
            $engine = $model->searchableUsing();
        } catch (\Throwable) {
            return null;
        }

        if (! is_object($engine)) {
            return null;
        }

        try {
            $raw = $model::search('')->raw();
        } catch (\Throwable) {
            return null;
        }

        if (! is_array($raw)) {
            return null;
        }

        return match (true) {
            $engine instanceof AlgoliaEngine => $this->resolveAlgolia($raw),
            $engine instanceof MeilisearchEngine => $this->resolveMeilisearch($raw),
            $engine instanceof TypesenseEngine => $this->resolveTypesense($raw),
            $engine instanceof DatabaseEngine => null,
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function resolveAlgolia(array $raw): ?int
    {
        $value = $raw['nbHits'] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function resolveMeilisearch(array $raw): ?int
    {
        $value = $raw['estimatedTotalHits'] ?? $raw['totalHits'] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function resolveTypesense(array $raw): ?int
    {
        $value = $raw['found'] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }
}
