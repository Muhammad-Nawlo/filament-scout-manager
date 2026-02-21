<?php

namespace MuhammadNawlo\FilamentScoutManager\Settings;

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\MissingSettings;
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

        try {
            $this->save();
        } catch (MissingSettings) {
            $this->persistModelsFallback($models);
        }
    }

    private function persistModelsFallback(array $models): void
    {
        $repository = config('settings.default_repository', 'database');
        $table = config("settings.repositories.{$repository}.table", 'settings');
        $connection = config("settings.repositories.{$repository}.connection");

        $query = DB::connection($connection)->table($table);
        $now = now();

        $query->updateOrInsert(
            ['group' => static::group(), 'name' => 'models'],
            [
                'payload' => json_encode($models, JSON_THROW_ON_ERROR),
                'locked' => false,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }
}
