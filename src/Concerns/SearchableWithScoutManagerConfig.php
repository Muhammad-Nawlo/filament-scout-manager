<?php

namespace MuhammadNawlo\FilamentScoutManager\Concerns;

use Laravel\Scout\Searchable;

/**
 * Combines Laravel Scout's Searchable with plugin runtime config (engine, index name, fields).
 *
 * Use this trait instead of "use Searchable, UsesScoutManagerConfig" to avoid
 * trait method collisions (searchableUsing, searchableAs, toSearchableArray).
 *
 * Example:
 *   use MuhammadNawlo\FilamentScoutManager\Concerns\SearchableWithScoutManagerConfig;
 *
 *   class User extends Model
 *   {
 *       use SearchableWithScoutManagerConfig;
 *   }
 */
trait SearchableWithScoutManagerConfig
{
    use Searchable, UsesScoutManagerConfig {
        UsesScoutManagerConfig::searchableUsing insteadof Searchable;
        UsesScoutManagerConfig::searchableAs insteadof Searchable;
        UsesScoutManagerConfig::toSearchableArray insteadof Searchable;
    }
}
