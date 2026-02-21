<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-lg font-medium">Search Index Status</h2>
            @php($searches = $this->getData())

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="bg-primary-50 rounded-lg p-4">
                    <div class="text-sm text-primary-600">Total Models</div>
                    <div class="text-2xl font-bold">{{ $this->getData()['total_models'] }}</div>
                </div>
                <div class="bg-success-50 rounded-lg p-4">
                    <div class="text-sm text-success-600">Indexed Models</div>
                    <div class="text-2xl font-bold">{{ $this->getData()['indexed_models'] }}</div>
                </div>
                <div class="bg-warning-50 rounded-lg p-4">
                    <div class="text-sm text-warning-600">Total Records</div>
                    <div class="text-2xl font-bold">{{ number_format($this->getData()['total_records']) }}</div>
                </div>
                <div class="bg-info-50 rounded-lg p-4">
                    <div class="text-sm text-info-600">Indexed Records</div>
                    <div class="text-2xl font-bold">{{ number_format($this->getData()['indexed_records']) }}</div>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-lg font-medium">Popular Searches (Last 30 Days)</h2>

                @if(!empty($this->getData()['engines']))
                    <div class="mt-4">
                        <h3 class="text-sm font-medium mb-2">Engines in Use</h3>
                        <div class="flex gap-2">
                            @foreach($this->getData()['engines'] as $engine => $count)
                                <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">
                                {{ str_replace('Engine', '', $engine) }} ({{ $count }})
                            </span>
                            @endforeach
                        </div>
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
