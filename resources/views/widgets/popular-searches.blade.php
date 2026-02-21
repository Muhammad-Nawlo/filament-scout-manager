<x-filament::widget>
    <x-filament::card>
        @php($searches = $this->getData())

        <div class="space-y-4">
            <h2 class="text-lg font-medium">Popular Searches (Last 30 Days)</h2>

            @if (empty($searches))
                <p class="text-sm text-gray-500">No search data available yet.</p>
            @else
                <div class="space-y-2">
                    @foreach ($searches as $search)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                            <span class="font-medium">{{ $search['query'] }}</span>
                            <span class="text-sm text-gray-600">{{ $search['total'] }} searches</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
