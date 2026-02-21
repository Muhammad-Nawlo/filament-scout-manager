@php($searches = $this->getData())

<x-filament-widgets::widget>
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-lg font-medium">{{ __('filament-scout-manager::filament-scout-manager.widgets.popular_searches.heading') }}</h2>

            @if (empty($searches))
                <p class="text-sm text-gray-500">{{ __('filament-scout-manager::filament-scout-manager.widgets.popular_searches.empty') }}</p>
            @else
                <div class="space-y-2">
                    @foreach ($searches as $search)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800">
                            <span class="font-medium">{{ $search['query'] }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('filament-scout-manager::filament-scout-manager.widgets.popular_searches.searches_count', ['count' => $search['total']]) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
