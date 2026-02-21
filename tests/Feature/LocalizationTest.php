<?php

use Illuminate\Support\Facades\File;

function flattenTranslationKeys(array $data, string $prefix = ''): array
{
    $keys = [];

    foreach ($data as $key => $value) {
        $fullKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

        if (is_array($value)) {
            $keys = array_merge($keys, flattenTranslationKeys($value, $fullKey));
        } else {
            $keys[] = $fullKey;
        }
    }

    return $keys;
}

test('arabic translations mirror english key structure', function () {
    $english = require __DIR__ . '/../../resources/lang/en/filament-scout-manager.php';
    $arabic = require __DIR__ . '/../../resources/lang/ar/filament-scout-manager.php';

    $englishKeys = flattenTranslationKeys($english);
    $arabicKeys = flattenTranslationKeys($arabic);

    sort($englishKeys);
    sort($arabicKeys);

    expect($arabicKeys)->toEqual($englishKeys);
});

test('all translation keys referenced in source files exist', function () {
    $files = array_merge(
        File::allFiles(__DIR__ . '/../../src'),
        File::allFiles(__DIR__ . '/../../resources/views')
    );

    $missing = [];

    foreach ($files as $file) {
        $contents = File::get($file->getRealPath());

        preg_match_all('/filament-scout-manager::filament-scout-manager\\.[A-Za-z0-9_\\.]+/', $contents, $matches);

        foreach (array_unique($matches[0]) as $key) {
            if (! trans()->has($key, 'en') || ! trans()->has($key, 'ar')) {
                $missing[] = $file->getRelativePathname() . ':' . $key;
            }
        }
    }

    expect($missing)->toBe([]);
});
