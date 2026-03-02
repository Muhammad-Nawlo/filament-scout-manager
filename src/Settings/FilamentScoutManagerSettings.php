<?php

namespace MuhammadNawlo\FilamentScoutManager\Settings;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public static function repositoryTableExists(): bool
    {
        $repository = config('settings.default_repository') ?? 'database';
        $table = config("settings.repositories.{$repository}.table") ?? 'settings';
        $connection = config("settings.repositories.{$repository}.connection");

        try {
            return Schema::connection($connection)->hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Create the settings table if it does not exist (same schema as Spatie / plugin migration).
     * Call this before persisting so config can be saved without requiring a prior migrate.
     */
    public static function ensureSettingsTableExists(): bool
    {
        $repository = config('settings.default_repository') ?? 'database';
        $table = config("settings.repositories.{$repository}.table") ?? 'settings';
        $connection = config("settings.repositories.{$repository}.connection");

        try {
            if (Schema::connection($connection)->hasTable($table)) {
                return true;
            }

            Schema::connection($connection)->create($table, function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('group');
                $blueprint->string('name');
                $blueprint->boolean('locked')->default(false);
                $blueprint->json('payload');
                $blueprint->timestamps();
                $blueprint->unique(['group', 'name']);
            });

            return true;
        } catch (\Throwable) {
            return false;
        }
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

        static::ensureSettingsTableExists();

        if (static::repositoryTableExists()) {
            $this->persistModelsFallback($models);
        }

        try {
            $this->save();
        } catch (MissingSettings | QueryException) {
            if (static::repositoryTableExists()) {
                $this->persistModelsFallback($models);
            }
        }
    }

    private function persistModelsFallback(array $models): void
    {
        $repository = config('settings.default_repository') ?? 'database';
        $table = config("settings.repositories.{$repository}.table") ?? 'settings';
        $connection = config("settings.repositories.{$repository}.connection");

        if (! static::repositoryTableExists()) {
            return;
        }

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
