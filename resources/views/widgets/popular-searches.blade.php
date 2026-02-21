<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-lg font-medium">Search Index Status</h2>

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
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
