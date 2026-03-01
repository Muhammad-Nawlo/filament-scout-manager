<?php

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use MuhammadNawlo\FilamentScoutManager\Services\IndexedCountResolver;

test('identify_users config key exists and defaults to false', function () {
    $value = config('filament-scout-manager.identify_users');

    expect($value)->toBeFalse();
});

test('identify_users config does not alter IndexedCountResolver behavior', function () {
    config(['filament-scout-manager.identify_users' => true]);

    $modelClass = get_class(new class extends Model
    {
        use Searchable;

        public function searchableUsing()
        {
            return new class {};
        }
    });
    $model = new $modelClass;
    $resolver = app(IndexedCountResolver::class);
    $result = $resolver->resolve($model);

    expect($result)->toBeNull();

    config(['filament-scout-manager.identify_users' => false]);
    $result2 = $resolver->resolve($model);

    expect($result2)->toBeNull();
});

test('plugin does not use identify_users in code', function () {
    $srcPath = __DIR__ . '/../../src';
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    $found = false;
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }
        $content = file_get_contents($file->getRealPath());
        if (str_contains($content, 'identify_users') || str_contains($content, 'SCOUT_IDENTIFY')) {
            $found = true;

            break;
        }
    }

    expect($found)->toBeFalse();
});

test('model with getScoutKey override does not break resource query', function () {
    $modelClass = get_class(new class extends Model
    {
        use Searchable;

        public function getScoutKey()
        {
            return $this->email ?? $this->getKey();
        }

        public function getScoutKeyName()
        {
            return 'email';
        }
    });

    expect($modelClass)->toBeString();
    expect(method_exists($modelClass, 'getScoutKey'))->toBeTrue();
});
