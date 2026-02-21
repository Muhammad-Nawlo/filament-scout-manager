<?php

namespace MuhammadNawlo\FilamentScoutManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MuhammadNawlo\FilamentScoutManager\FilamentScoutManager
 */
class FilamentScoutManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MuhammadNawlo\FilamentScoutManager\FilamentScoutManager::class;
    }
}
