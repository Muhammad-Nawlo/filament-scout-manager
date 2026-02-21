<?php

namespace MuhammadNawlo\FilamentScoutManager\Settings;

use Spatie\LaravelSettings\Settings;

class FilamentScoutManagerSettings extends Settings
{
    public array $models = [];

    public static function defaults(): array
    {
        return [
            'models' => [],
        ];
    }

    public static function group(): string
    {
        return 'filament-scout-manager';
    }

    public function getModelConfig(string $modelClass): ?array
    {
        return $this->models[$modelClass] ?? null;
    }

    public function setModelConfig(string $modelClass, array $config): void
    {
        $models = $this->models;
        $models[$modelClass] = $config;
        $this->models = $models;

        $this->save();
    }
}
