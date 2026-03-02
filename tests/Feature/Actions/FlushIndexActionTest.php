<?php

use MuhammadNawlo\FilamentScoutManager\Actions\FlushIndexAction;

test('flush action has correct default name', function () {
    expect(FlushIndexAction::getDefaultName())->toBe('flush');
});

test('flush action sets up correctly', function () {
    $action = FlushIndexAction::make('flush');

    expect($action->getLabel())->toBe('Flush Index')
        ->and($action->getIcon())->toBe('heroicon-o-trash')
        ->and($action->getColor())->toBe('danger')
        ->and($action->isConfirmationRequired())->toBeTrue();
});
