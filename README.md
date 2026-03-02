# Filament Scout Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/muhammad-nawlo/filament-scout-manager.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/filament-scout-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/filament-scout-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/muhammad-nawlo/filament-scout-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/filament-scout-manager/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/muhammad-nawlo/filament-scout-manager/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/muhammad-nawlo/filament-scout-manager.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/filament-scout-manager)

A Filament plugin to manage your Laravel Scout search setup from an admin panel.

## Features

- Discover Scout-searchable models and inspect index/engine metadata.
- Run index actions (import, flush, refresh) per model or in bulk.
- View index health and popular searches with dashboard widgets.
- Log user search queries for analysis.
- Manage search synonyms in the panel.
- Configure behavior with package config/settings.

## Requirements

- PHP 8.2+
- Laravel app with [Laravel Scout](https://laravel.com/docs/scout) configured
- Filament 5 panel

## Installation

Install the package:

```bash
composer require muhammad-nawlo/filament-scout-manager
```

Run the installer:

```bash
php artisan filament-scout-manager:install
```

Or manually publish package files:

```bash
php artisan vendor:publish --tag="filament-scout-manager-config"
php artisan vendor:publish --tag="filament-scout-manager-migrations"
php artisan migrate
```

If you use a custom Filament theme, add the package views as a Tailwind source:

```css
@source '../../../../vendor/muhammad-nawlo/filament-scout-manager/resources/**/*.blade.php';
```

## Register the plugin

In your Filament panel provider, register the plugin:

```php
use MuhammadNawlo\FilamentScoutManager\FilamentScoutManagerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentScoutManagerPlugin::make(),
        ]);
}
```

## Configuration

Published config: `config/filament-scout-manager.php`

```php
return [
    'log_searches' => true,
    'log_retention_days' => 30,
    'enable_synonyms' => true,
    'models' => [
        // 'App\\Other\\Model' => [],
    ],
];
```

## Usage notes

- Ensure each model you want indexed uses Scout's `Searchable` trait.
- Configure your Scout driver (`SCOUT_DRIVER`) and engine credentials in `.env`.
- The "Searchable Fields" options in the panel are most useful when your model defines a custom `toSearchableArray()`.

## Runtime overrides

Stored model configuration (index name, searchable fields, engine) can be applied at runtime so that Scout uses the values you set in the Filament panel. This is **opt-in** and **fully backward compatible**.

- **No trait** → no override; the model behaves exactly like vanilla Scout.
- **With trait** → if a config is saved for that model in the panel, `searchableAs()`, `toSearchableArray()`, and `searchableUsing()` use the stored values; otherwise defaults are unchanged.

To enable runtime overrides on a model, use the `SearchableWithScoutManagerConfig` trait (it combines Scout’s `Searchable` with plugin config and avoids trait method collisions):

```php
use Illuminate\Database\Eloquent\Model;
use MuhammadNawlo\FilamentScoutManager\Concerns\SearchableWithScoutManagerConfig;

class Product extends Model
{
    use SearchableWithScoutManagerConfig;
}
```

If you prefer to use `Searchable` and `UsesScoutManagerConfig` separately, you must resolve the trait collision for `searchableUsing`, `searchableAs`, and `toSearchableArray` with `insteadof`; see the docblock on `UsesScoutManagerConfig` for the exact syntax.

Then configure the model in Filament (Search → Searchable Models → Configure). Stored values for index name, searchable fields, and engine will take effect when this trait is used. If no config exists for the model, behavior is identical to Scout’s defaults.

## Batch operations without indexing

When you need to run many Eloquent operations (bulk updates, imports, etc.) without syncing each change to the search index, use Scout’s `withoutSyncingToSearch()` method. This avoids unnecessary index writes and improves performance.

Wrap your bulk logic in a closure:

```php
use App\Models\Product;

Product::withoutSyncingToSearch(function () {
    Product::where('status', 'draft')->update(['status' => 'published']);
    // ... other bulk operations
});
```

After the batch is done, re-import the affected model so the index is up to date:

```bash
php artisan scout:import "App\Models\Product"
```

This pattern is controlled in your application code; the plugin does not add any global pause or toggle. See [Laravel Scout: Pausing Indexing](https://laravel.com/docs/11.x/scout#pausing-indexing).

## Conditionally searchable records

To index only some records (e.g. only published posts), implement `shouldBeSearchable()` on your model. Scout calls this when syncing via `save`/`create` or when using the `searchable()` method on a query; if it returns `false`, the record is not added or updated in the index.

```php
public function shouldBeSearchable(): bool
{
    return $this->is_published;
}
```

- This logic must live in your model; the plugin does not override or control it.
- When using Scout’s **database** engine, `shouldBeSearchable()` is not applied (all searchable data is in the database). For similar behavior, use [where clauses](https://laravel.com/docs/11.x/scout#where-clauses) in your search queries instead.
- See [Laravel Scout: Conditionally Searchable Model Instances](https://laravel.com/docs/11.x/scout#conditionally-searchable-model-instances).

## Scout queue behavior

Scout’s queue configuration is **global**, not per model.

- Set `SCOUT_QUEUE=true` in `.env` (or the `queue` option in `config/scout.php`) to queue index operations instead of running them synchronously.
- The queue connection and queue name are defined in `config/scout.php` and apply to all Scout indexing jobs.
- Per-model queue routing is not supported by Scout out of the box; it would require a custom job implementation that reads your own config. The plugin does not change Scout’s global queue behavior.
- See [Laravel Scout: Queueing](https://laravel.com/docs/11.x/scout#queueing).

## Database engine search strategies

When using Scout’s **database** engine (`SCOUT_DRIVER=database`), you can control how each column is searched by adding PHP attributes to your model’s `toSearchableArray()` method:

- **`SearchUsingFullText`** – use MySQL/PostgreSQL full-text search for the listed columns (faster and more relevant for text). Requires a [full-text index](https://laravel.com/docs/11.x/migrations#available-index-types) on those columns in a migration.
- **`SearchUsingPrefix`** – use prefix-only matching (e.g. `term%`) for the listed columns instead of `%term%`. Helps performance on large tables.

Columns not covered by these attributes keep Scout’s default “where like” behavior. The plugin does not set or change these attributes; you implement them in your model.

Example:

```php
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;

#[SearchUsingFullText(['title', 'description'])]
#[SearchUsingPrefix(['sku'])]
public function toSearchableArray(): array
{
    return [
        'title' => $this->title,
        'description' => $this->description,
        'sku' => $this->sku,
    ];
}
```

In a migration, add a full-text index for the columns you use with `SearchUsingFullText`:

```php
$table->fullText(['title', 'description']);
```

These strategies apply only when the engine is database. See [Laravel Scout: Customizing Database Searching Strategies](https://laravel.com/docs/11.x/scout#customizing-database-searching-strategies).

## Customizing Scout import queries

Scout lets you customize how models are loaded during batch import and how the collection is prepared before indexing. Implement these methods on your model; the plugin does not override them.

**Modify the import query** (e.g. eager load relations to avoid N+1 during import):

```php
use Illuminate\Database\Eloquent\Builder;

protected function makeAllSearchableUsing(Builder $query): Builder
{
    return $query->with(['category', 'tags']);
}
```

**Modify the collection before indexing** (e.g. load relations on the chunk):

```php
use Illuminate\Database\Eloquent\Collection;

public function makeSearchableUsing(Collection $models): Collection
{
    return $models->load('author');
}
```

- Use these to eager load relationships so `toSearchableArray()` can use related data without extra queries.
- `makeAllSearchableUsing` is used when you run `scout:import` or `Model::makeAllSearchable()`; note that when the import is queued, relationships may not be restored in the job, so prefer `makeSearchableUsing` for per-chunk loading when using queues.
- See [Laravel Scout: Modifying the Import Query](https://laravel.com/docs/11.x/scout#modifying-the-import-query) and [Modifying Records Before Importing](https://laravel.com/docs/11.x/scout#modifying-records-before-importing).

## Algolia user identification

When using the Algolia engine, you can associate search requests with authenticated users for analytics and personalization. Set in your `.env`:

```env
SCOUT_IDENTIFY=true
```

This tells Scout to pass the authenticated user’s primary identifier and the request IP to Algolia with each search request. The plugin does **not** automatically send or capture user data; enabling identification and what is sent remain under your application and Laravel Scout. See [Laravel Scout: Identifying Users](https://laravel.com/docs/11.x/scout#identifying-users).

## Customizing the indexed model ID

By default, Scout uses the model’s primary key as the unique ID stored in the index. You can override this with `getScoutKey()` and `getScoutKeyName()` on your model (e.g. to use UUIDs, emails, or external IDs). Some engines (e.g. Typesense) expect string IDs or specific key names.

```php
public function getScoutKey()
{
    return $this->email;
}

public function getScoutKeyName()
{
    return 'email';
}
```

The plugin does not override these methods; implement them in your model when needed. See [Laravel Scout: Configuring the Model ID](https://laravel.com/docs/11.x/scout#configuring-the-model-id).

## Passing engine-specific search options

Some engines support passing extra parameters into the search request. For example, Typesense allows dynamic search parameters via `options()`:

```php
Post::search('laravel')
    ->options([
        'query_by' => 'title, description',
        'filter_by' => 'published:=true',
    ])
    ->get();
```

Algolia and other engines may support similar options depending on their API. The plugin does not restrict or modify `options()`; advanced search behavior stays in your application code. See your engine’s documentation (e.g. [Typesense search parameters](https://typesense.org/docs/latest/api/search.html#search-parameters)).

## Laravel Scout feature coverage

| Feature | Status |
|--------|--------|
| Index syncing (import, flush, refresh, sync settings) | ✅ |
| Runtime overrides (index name, fields, engine via trait) | ✅ |
| Typesense UI support & safe indexed count | ✅ |
| Custom engines (safe handling, no UI break) | ✅ |
| Indexing control docs (pause, conditional, queue) | ✅ |
| Database engine docs (search strategies, full-text) | ✅ |
| Import customization docs (makeAllSearchableUsing, makeSearchableUsing) | ✅ |
| Algolia user identification docs | ✅ |
| Scout key override docs (getScoutKey, getScoutKeyName) | ✅ |
| Dynamic search options docs (options()) | ✅ |

The plugin does not override Scout core behavior. All engine-specific logic (identify, key, options) remains application-controlled.

## Testing

The package uses [Pest](https://pestphp.com/) for PHPUnit-style tests. Run the test suite:

```bash
composer test
```

### Test coverage

- **Plugin**: ID, Filament contract, registration of resources and widgets (SearchableModelResource, SearchQueryLogResource, SynonymResource, IndexStatusWidget, PopularSearchesWidget).
- **Actions**: Import, Flush, Refresh, Sync Index Settings (labels, icons, confirmation).
- **Resources**: SearchableModelResource (navigation, `isSearchable`, Eloquent query), engine badges, SearchQueryLogResource, SynonymResource.
- **Widgets**: IndexStatusWidget (column span, stats), PopularSearchesWidget (data, logging toggle).
- **Services**: ScoutModelConfigService, ScoutIndexSettingsService, IndexedCountResolver (Algolia/Meilisearch/Typesense raw response parsing, unknown engine).
- **DTO & traits**: ScoutModelConfigDTO (properties, readonly), UsesScoutManagerConfig / SearchableWithScoutManagerConfig (searchableAs, toSearchableArray, scoutQueueConnection, searchableUsing).
- **Settings & models**: FilamentScoutManagerSettings, SearchQueryLog, Synonym.
- **Commands**: Install command (signature, handle).
- **Localization**: English and Arabic translation keys.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent updates.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Muhammad-Nawlo](https://github.com/Muhammad-Nawlo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for details.
