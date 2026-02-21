<?php

namespace MuhammadNawlo\FilamentScoutManager\Commands;

use Illuminate\Console\Command;
use MuhammadNawlo\FilamentScoutManager\Settings\FilamentScoutManagerSettings;

class InstallFilamentScoutManager extends Command
{
    protected $signature = 'filament-scout-manager:install';

    protected $description = 'Install the Filament Scout Manager plugin';

    public function handle()
    {
        $this->info('Installing Filament Scout Manager...');

        // Publish migrations
        $this->call('vendor:publish', [
            '--provider' => 'MuhammadNawlo\\FilamentScoutManager\\FilamentScoutManagerServiceProvider',
            '--tag' => 'migrations'
        ]);

        // Run migrations
        $this->call('migrate');

        // Publish config
        $this->call('vendor:publish', [
            '--provider' => 'MuhammadNawlo\\FilamentScoutManager\\FilamentScoutManagerServiceProvider',
            '--tag' => 'config'
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--provider' => 'MuhammadNawlo\\FilamentScoutManager\\FilamentScoutManagerServiceProvider',
            '--tag' => 'filament-scout-manager-assets'
        ]);

        // Initialize settings
        $settings = app(FilamentScoutManagerSettings::class);
        if (empty($settings->models)) {
            $settings->models = [];
            $settings->save();
        }

        $this->info('Filament Scout Manager installed successfully!');
        $this->warn('Don\'t forget to add FilamentScoutManagerPlugin to your Filament panel provider.');
    }
}
