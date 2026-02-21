<?php

namespace MuhammadNawlo\FilamentScoutManager\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use MuhammadNawlo\FilamentScoutManager\FilamentScoutManagerServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MuhammadNawlo\\FilamentScoutManager\\Database\\Factories\\'.class_basename($modelName).'Factory'
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
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app->booted(function () use ($app) {
            $app->make(\Spatie\LaravelSettings\SettingsContainer::class);
        });
    }
}
