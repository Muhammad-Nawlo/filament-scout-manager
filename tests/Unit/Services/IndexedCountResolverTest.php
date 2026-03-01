<?php

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use MuhammadNawlo\FilamentScoutManager\Services\IndexedCountResolver;

test('resolve returns null for unknown engine', function () {
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
