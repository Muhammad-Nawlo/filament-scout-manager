<?php

namespace MuhammadNawlo\FilamentScoutManager\Settings;

use Spatie\LaravelSettings\Settings;

class FilamentScoutManagerSettings extends Settings
{
    public array $models = [];

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
        $this->models[$modelClass] = $config;
        $this->save();
    }
}
