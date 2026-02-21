<?php

use MuhammadNawlo\FilamentScoutManager\Actions\ImportToScoutAction;

test('action has correct default name', function () {
    expect(ImportToScoutAction::getDefaultName())->toBe('import');
});

test('action sets up correctly', function () {
    $action = ImportToScoutAction::make('import');

    expect($action->getLabel())->toBe('Import to Index')
        ->and($action->getIcon())->toBe('heroicon-o-arrow-up-on-square')
        ->and($action->getColor())->toBe('success')
        ->and($action->requiresConfirmation())->toBeTrue();
});
