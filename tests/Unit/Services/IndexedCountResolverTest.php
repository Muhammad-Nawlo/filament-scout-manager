<?php

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use MuhammadNawlo\FilamentScoutManager\Services\IndexedCountResolver;

function invokeResolverMethod(IndexedCountResolver $resolver, string $method, array $raw): ?int
{
    $ref = new ReflectionMethod(IndexedCountResolver::class, $method);
    $ref->setAccessible(true);

    return $ref->invoke($resolver, $raw);
}

test('resolveAlgolia returns nbHits when present and numeric', function () {
    $resolver = app(IndexedCountResolver::class);

    expect(invokeResolverMethod($resolver, 'resolveAlgolia', ['nbHits' => 42]))->toBe(42)
        ->and(invokeResolverMethod($resolver, 'resolveAlgolia', ['nbHits' => '100']))->toBe(100);
});

test('resolveAlgolia returns null when nbHits missing or non-numeric', function () {
    $resolver = app(IndexedCountResolver::class);

    expect(invokeResolverMethod($resolver, 'resolveAlgolia', []))->toBeNull()
        ->and(invokeResolverMethod($resolver, 'resolveAlgolia', ['nbHits' => 'nope']))->toBeNull();
});

test('resolveMeilisearch returns estimatedTotalHits or totalHits', function () {
    $resolver = app(IndexedCountResolver::class);

    expect(invokeResolverMethod($resolver, 'resolveMeilisearch', ['estimatedTotalHits' => 10]))->toBe(10)
        ->and(invokeResolverMethod($resolver, 'resolveMeilisearch', ['totalHits' => 5]))->toBe(5)
        ->and(invokeResolverMethod($resolver, 'resolveMeilisearch', ['estimatedTotalHits' => 3, 'totalHits' => 7]))->toBe(3);
});

test('resolveMeilisearch returns null when keys missing or non-numeric', function () {
    $resolver = app(IndexedCountResolver::class);

    expect(invokeResolverMethod($resolver, 'resolveMeilisearch', []))->toBeNull()
        ->and(invokeResolverMethod($resolver, 'resolveMeilisearch', ['estimatedTotalHits' => 'x']))->toBeNull();
});

test('resolveTypesense returns found when present and numeric', function () {
    $resolver = app(IndexedCountResolver::class);

    expect(invokeResolverMethod($resolver, 'resolveTypesense', ['found' => 99]))->toBe(99);
});

test('resolveTypesense returns null when found missing or non-numeric', function () {
    $resolver = app(IndexedCountResolver::class);

    expect(invokeResolverMethod($resolver, 'resolveTypesense', []))->toBeNull()
        ->and(invokeResolverMethod($resolver, 'resolveTypesense', ['found' => 'many']))->toBeNull();
});

test('resolve returns null for unknown engine', function () {
    $modelClass = get_class(new class extends Model
    {
        use Searchable;

        public function searchableUsing()
        {
            return new class
            {
            };
        }
    });

    $model = new $modelClass;
    $resolver = app(IndexedCountResolver::class);

    $result = $resolver->resolve($model);

    expect($result)->toBeNull();
});

test('resolve returns null when searchableUsing throws', function () {
    $modelClass = get_class(new class extends Model
    {
        use Searchable;

        public function searchableUsing()
        {
            throw new \RuntimeException('Custom engine error');
        }
    });

    $model = new $modelClass;
    $resolver = app(IndexedCountResolver::class);

    $result = $resolver->resolve($model);

    expect($result)->toBeNull();
});
