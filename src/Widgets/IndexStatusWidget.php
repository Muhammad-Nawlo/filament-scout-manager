<?php

namespace MuhammadNawlo\FilamentScoutManager\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Laravel\Scout\Engines\AlgoliaEngine;
use Laravel\Scout\Engines\DatabaseEngine;
use Laravel\Scout\Engines\MeilisearchEngine;
use Laravel\Scout\Engines\TypesenseEngine;
use Laravel\Scout\Searchable;
use MuhammadNawlo\FilamentScoutManager\Services\IndexedCountResolver;

class IndexStatusWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $data = $this->getData();

        return [
            Stat::make('Searchable models', (string) $data['total_models'])
                ->description(sprintf('%d indexed', $data['indexed_models']))
                ->color('primary'),
            Stat::make('Database records', (string) $data['total_records'])
                ->description(sprintf('%d indexed records', $data['indexed_records']))
                ->color('success'),
            Stat::make('Search engines', (string) count($data['engines']))
                ->description(implode(', ', array_keys($data['engines'])) ?: 'None detected')
                ->color('warning'),
        ];
    }

    public function getData(): array
    {
        return Cache::remember('filament-scout-manager.index-status', 60, function () {
            $models = $this->getSearchableModels();
            $resolver = app(IndexedCountResolver::class);

            $stats = [
                'total_models' => count($models),
                'indexed_models' => 0,
                'total_records' => 0,
                'indexed_records' => 0,
                'engines' => [],
            ];

            foreach ($models as $modelClass) {
                try {
                    $model = new $modelClass;
                    $engine = $model->searchableUsing();
                } catch (\Throwable) {
                    continue;
                }

                $engineLabel = $this->engineLabel($engine);
                $stats['engines'][$engineLabel] = ($stats['engines'][$engineLabel] ?? 0) + 1;

                try {
                    $totalRecords = $modelClass::count();
                    $stats['total_records'] += $totalRecords;
                } catch (\Throwable) {
                    continue;
                }

                $indexedCount = $resolver->resolve($model);
                if ($indexedCount !== null) {
                    $stats['indexed_records'] += $indexedCount;
                    if ($indexedCount > 0) {
                        $stats['indexed_models']++;
                    }
                }
            }

            return $stats;
        });
    }

    private function engineLabel(object $engine): string
    {
        if ($engine instanceof TypesenseEngine) {
            return 'Typesense';
        }
        if ($engine instanceof AlgoliaEngine) {
            return 'Algolia';
        }
        if ($engine instanceof MeilisearchEngine) {
            return 'Meilisearch';
        }
        if ($engine instanceof DatabaseEngine) {
            return 'Database';
        }
        if ($engine instanceof \Laravel\Scout\Engines\CollectionEngine) {
            return 'Collection';
        }

        return class_basename($engine);
    }

    protected function getSearchableModels(): array
    {
        $models = [];
        $path = app_path('Models');

        if (File::exists($path)) {
            foreach (File::allFiles($path) as $file) {
                $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();
                if (class_exists($class) && in_array(Searchable::class, class_uses_recursive($class))) {
                    $models[] = $class;
                }
            }
        }

        return $models;
    }
}
