<?php

namespace MuhammadNawlo\FilamentScoutManager\Commands;

use Illuminate\Console\Command;

class FilamentScoutManagerCommand extends Command
{
    public $signature = 'filament-scout-manager';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
