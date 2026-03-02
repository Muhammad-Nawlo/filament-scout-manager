<?php

use Illuminate\Support\Facades\Artisan;
use MuhammadNawlo\FilamentScoutManager\Commands\InstallFilamentScoutManager;

test('install command has correct signature', function () {
    $command = app(InstallFilamentScoutManager::class);

    expect($command->getName())->toBe('filament-scout-manager:install');
});

test('install command runs via Artisan without throwing', function () {
    $exitCode = Artisan::call('filament-scout-manager:install');

    expect($exitCode)->toBe(0);
});
