<?php

namespace MuhammadNawlo\FilamentScoutManager\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Laravel\Scout\Searchable;

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
                    $engine = class_basename($model->searchableUsing());

                    $stats['engines'][$engine] = ($stats['engines'][$engine] ?? 0) + 1;

                    $totalRecords = $modelClass::count();
                    $stats['total_records'] += $totalRecords;

                    try {
                        $indexedRecords = $modelClass::search('')->raw()['nbHits'] ?? 0;
                        $stats['indexed_records'] += $indexedRecords;
                        if ($indexedRecords > 0) {
                            $stats['indexed_models']++;
                        }
                    } catch (\Exception $e) {
                        // ignore
                    }
                } catch (\Exception $e) {
                    // skip problematic models
                }
            }

            return $stats;
        });
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
