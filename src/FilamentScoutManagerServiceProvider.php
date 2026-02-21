<?php

namespace MuhammadNawlo\FilamentScoutManager;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use MuhammadNawlo\FilamentScoutManager\Commands\InstallFilamentScoutManager;
use MuhammadNawlo\FilamentScoutManager\Testing\TestsFilamentScoutManager;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelSettings\SettingsContainer;

class FilamentScoutManagerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-scout-manager';

    public static string $viewNamespace = 'filament-scout-manager';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('muhammad-nawlo/filament-scout-manager');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-scout-manager/{$file->getFilename()}"),
                ], 'filament-scout-manager-stubs');
            }
        }
        $this->app->afterResolving(SettingsContainer::class, function (SettingsContainer $settingsContainer): void {
            $settingsClass = \MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings::class;

            if (method_exists($settingsContainer, 'register')) {
                $settingsContainer->register([$settingsClass]);

                return;
            }

            if (method_exists($settingsContainer, 'add')) {
                $settingsContainer->add($settingsClass);
            }
        });

        // Testing
        Testable::mixin(new TestsFilamentScoutManager);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'muhammad-nawlo/filament-scout-manager';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-scout-manager', __DIR__ . '/../resources/dist/components/filament-scout-manager.js'),
            Css::make('filament-scout-manager-styles', __DIR__ . '/../resources/dist/filament-scout-manager.css')->loadedOnRequest(),
            Js::make('filament-scout-manager-scripts', __DIR__ . '/../resources/dist/filament-scout-manager.js')->loadedOnRequest(),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            InstallFilamentScoutManager::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_scout_search_logs_table',
            'create_scout_synonyms_table',
        ];
    }
}
