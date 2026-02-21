<?php

namespace MuhammadNawlo\FilamentScoutManager\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;

class PopularSearchesWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'half';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        if (! config('filament-scout-manager.log_searches', true)) {
            return [
                Stat::make('Popular searches', 'Disabled')
                    ->description('Enable search logging to view this widget')
                    ->color('gray'),
            ];
        }

        return collect($this->getData())
            ->map(fn (array $search): Stat => Stat::make((string) $search['query'], (string) $search['total'])
                ->description('Searches in last 30 days')
                ->color('info'))
            ->all();
    }

    public function getData(): array
    {
        if (! config('filament-scout-manager.log_searches', true)) {
            return [];
        }

        return SearchQueryLog::popular(30)
            ->limit(10)
            ->get()
            ->toArray();
    }
}
