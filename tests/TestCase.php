<?php

namespace MuhammadNawlo\FilamentScoutManager\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use MuhammadNawlo\FilamentScoutManager\FilamentScoutManagerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MuhammadNawlo\\FilamentScoutManager\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentScoutManagerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('settings.default_repository', 'database');
        $app['config']->set('settings.repositories', [
            'database' => [
                'type' => \Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,
                'model' => \Spatie\LaravelSettings\Models\SettingsProperty::class,
                'table' => 'settings',
                'connection' => null,
            ],
        ]);

        $app->booted(function () use ($app) {
            $app->make(\Spatie\LaravelSettings\SettingsContainer::class);
        });
    }
}
