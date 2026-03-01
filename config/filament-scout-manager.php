<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Log Searches
    |--------------------------------------------------------------------------
    */
    'log_searches' => true,

    /*
    |--------------------------------------------------------------------------
    | Search Log Retention (days)
    |--------------------------------------------------------------------------
    */
    'log_retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Enable Synonyms
    |--------------------------------------------------------------------------
    */
    'enable_synonyms' => true,

    /*
    |--------------------------------------------------------------------------
    | Additional Searchable Models (outside app/Models)
    |--------------------------------------------------------------------------
    */
    'models' => [
        // 'App\\Other\\Model' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Algolia User Identification (Informational)
    |--------------------------------------------------------------------------
    | When using Algolia, SCOUT_IDENTIFY=true in .env enables passing user ID
    | and IP to Algolia for analytics. This key mirrors that env for reference
    | only; the plugin does not use it programmatically. User identification
    | is controlled by Laravel Scout and your application.
    */
    'identify_users' => env('SCOUT_IDENTIFY', false),
];
