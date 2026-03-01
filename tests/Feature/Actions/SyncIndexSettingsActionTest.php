<?php

use MuhammadNawlo\FilamentScoutManager\Actions\SyncIndexSettingsAction;

test('sync index settings action has correct default name', function () {
    expect(SyncIndexSettingsAction::getDefaultName())->toBe('sync_index_settings');
});

test('sync index settings action sets up correctly', function () {
    $action = SyncIndexSettingsAction::make('sync_index_settings');

    expect($action->getLabel())->toBe('Sync Index Settings')
        ->and($action->getIcon())->toBe('heroicon-o-arrow-path')
        ->and($action->getColor())->toBe('primary')
        ->and($action->isConfirmationRequired())->toBeTrue();
});

test('sync index settings service apply runs without throwing', function () {
    app(\MuhammadNawlo\FilamentScoutManager\Services\ScoutIndexSettingsService::class)->apply();

    expect(true)->toBeTrue();
});
