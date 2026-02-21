@php($data = $this->getData())

<x-filament-widgets::widget>
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-lg font-medium">Search Index Status</h2>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="rounded-lg bg-primary-50 p-4 dark:bg-primary-900/20">
                    <div class="text-sm text-primary-600 dark:text-primary-300">Total Models</div>
                    <div class="text-2xl font-bold">{{ $data['total_models'] }}</div>
                </div>
                <div class="rounded-lg bg-success-50 p-4 dark:bg-success-900/20">
                    <div class="text-sm text-success-600 dark:text-success-300">Indexed Models</div>
                    <div class="text-2xl font-bold">{{ $data['indexed_models'] }}</div>
                </div>
                <div class="rounded-lg bg-warning-50 p-4 dark:bg-warning-900/20">
                    <div class="text-sm text-warning-600 dark:text-warning-300">Total Records</div>
                    <div class="text-2xl font-bold">{{ number_format($data['total_records']) }}</div>
                </div>
                <div class="rounded-lg bg-info-50 p-4 dark:bg-info-900/20">
                    <div class="text-sm text-info-600 dark:text-info-300">Indexed Records</div>
                    <div class="text-2xl font-bold">{{ number_format($data['indexed_records']) }}</div>
                </div>
            </div>

            @if (! empty($data['engines']))
                <div class="mt-4">
                    <h3 class="mb-2 text-sm font-medium">Engines in Use</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($data['engines'] as $engine => $count)
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm dark:bg-gray-800">
                                {{ str_replace('Engine', '', $engine) }} ({{ $count }})
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
