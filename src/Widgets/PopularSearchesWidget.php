<?php

namespace MuhammadNawlo\FilamentScoutManager\Widgets;

use Filament\Widgets\Widget;
use MuhammadNawlo\FilamentScoutManager\Models\SearchQueryLog;

class PopularSearchesWidget extends Widget
{
    protected string $view = 'filament-scout-manager::widgets.popular-searches';

    protected int | string | array $columnSpan = 'half';

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
