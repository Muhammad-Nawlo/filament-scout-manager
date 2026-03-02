<?php

use MuhammadNawlo\FilamentScoutManager\Actions\RefreshIndexAction;

test('refresh action has correct default name', function () {
    expect(RefreshIndexAction::getDefaultName())->toBe('refresh');
});

test('refresh action sets up correctly', function () {
    $action = RefreshIndexAction::make('refresh');

    expect($action->getLabel())->toBe('Refresh Index')
        ->and($action->getIcon())->toBe('heroicon-o-arrow-path')
        ->and($action->getColor())->toBe('warning')
        ->and($action->isConfirmationRequired())->toBeTrue();
});
