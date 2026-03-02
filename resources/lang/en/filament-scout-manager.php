<?php

return [
    'navigation' => [
        'group' => 'Search',
        'models' => 'Searchable Models',
        'logs' => 'Search Logs',
        'synonyms' => 'Synonyms',
    ],

    'common' => [
        'not_available' => 'N/A',
        'guest' => 'Guest',
        'yes' => 'Yes',
        'no' => 'No',
    ],

    'models' => [
        'single' => 'Searchable Model',
        'plural' => 'Searchable Models',
        'fields' => [
            'name' => 'Model',
            'index_name' => 'Index Name',
            'total_records' => 'Total Records',
            'indexed_records' => 'Indexed Records',
            'searchable_fields' => 'Searchable Fields',
            'engine' => 'Search Engine',
            'searchable' => 'Searchable',
            'custom' => 'Custom',
            'last_sync' => 'Last Sync',
            'model_class' => 'Model Class',
            'engine_override' => 'Override Engine',
            'engine_settings' => 'Engine-Specific Settings',
            'index_name_override' => 'Custom Index Name',
            'should_be_searchable' => 'Enable Searchable',
            'queue_connection' => 'Queue Connection (Advisory)',
            'no_fields_configured' => 'No fields configured',
            'more_count' => '+:count more',
        ],
        'sections' => [
            'configuration' => 'Model Configuration',
            'index_settings' => 'Index Settings',
            'batch_indexing_tips' => 'Batch indexing tips',
            'database_engine_tips' => 'Database engine tips',
            'import_optimization_tips' => 'Import optimization tips',
        ],
        'helpers' => [
            'searchable_fields' => 'Select which fields should be searchable (requires custom toSearchableArray implementation).',
            'engine_override' => 'Override the default search engine for this model.',
            'engine_settings' => 'Configure index settings below by selecting an engine. Then run "Sync Index Settings" to push to the search engine.',
            'index_name_override' => 'Leave empty to use the default searchableAs.',
            'not_indexed' => 'Not indexed',
            'should_be_searchable_help' => 'To control which records are indexed, implement shouldBeSearchable() in your model. This field is informational; the plugin does not override Scout behavior.',
            'queue_connection_help' => 'Scout queue configuration is global (config/scout.php). This value is informational unless you implement custom queue routing in your application.',
            'database_engine_help' => 'With the database engine, shouldBeSearchable() is not applied. Use where clauses in search queries and full-text indexes in migrations. See the Laravel Scout database engine docs.',
            'batch_indexing_tips_content' => 'Use Model::withoutSyncingToSearch(fn () => { ... }) in your code for bulk updates without syncing each change. After the batch, run: php artisan scout:import "App\\Models\\ModelName". This is app-level control; the plugin does not add pause toggles.',
            'database_engine_tips_content' => 'With the database engine you can use SearchUsingFullText and SearchUsingPrefix attributes on toSearchableArray() to control how columns are searched. Add a full-text index in a migration for full-text columns, e.g. $table->fullText([\'title\', \'description\']). See Laravel Scout database engine docs. The plugin does not configure these attributes.',
            'import_optimization_tips_content' => 'Implement makeAllSearchableUsing(Builder $query) to eager load relations on the import query (e.g. return $query->with([\'category\'])). Implement makeSearchableUsing(Collection $models) to load relations on each chunk (e.g. return $models->load(\'author\')). This avoids N+1 during indexing. The plugin does not override these methods.',
        ],
        'actions' => [
            'configure' => 'Configure',
            'bulk_import' => 'Import Selected',
            'bulk_flush' => 'Flush Selected',
        ],
        'notifications' => [
            'import_completed' => 'Import completed: :success succeeded, :fail failed.',
            'flush_completed' => 'Flush completed: :success succeeded, :fail failed.',
            'settings_table_missing_title' => 'Configuration could not be saved',
            'settings_table_missing_body' => 'The settings table does not exist. Run `php artisan migrate` (or `php artisan filament-scout-manager:install`) to create it, then save again.',
            'config_saved' => 'Searchable model configuration saved.',
        ],
        'engine_options' => [
            'default' => 'Use Default',
            'algolia' => 'Algolia',
            'meilisearch' => 'Meilisearch',
            'typesense' => 'Typesense',
            'database' => 'Database Engine',
            'collection' => 'Collection Engine',
        ],
        'engine_badges' => [
            'typesense' => 'Typesense',
            'database' => 'Database',
            'collection' => 'Collection',
        ],
        'queue_options' => [
            'sync' => 'Sync (No Queue)',
            'database' => 'Database',
            'redis' => 'Redis',
            'sqs' => 'SQS',
        ],
    ],

    'logs' => [
        'single' => 'Search Log',
        'plural' => 'Search Logs',
        'fields' => [
            'query' => 'Query',
            'model' => 'Model',
            'results' => 'Results',
            'time' => 'Time (s)',
            'user' => 'User',
            'ip_address' => 'IP Address',
            'success' => 'Success',
            'date' => 'Date',
            'from' => 'From',
            'until' => 'Until',
            'user_agent' => 'User Agent',
            'created' => 'Created',
            'before' => 'Delete logs before',
        ],
        'filters' => [
            'successful_only' => 'Successful only',
            'failed_only' => 'Failed only',
        ],
        'actions' => [
            'view' => 'View Details',
            'prune_old' => 'Prune Older Than...',
            'close' => 'Close',
        ],
        'modal' => [
            'details_heading' => 'Search Log Details',
        ],
        'values' => [
            'seconds' => ':seconds seconds',
        ],
        'notifications' => [
            'pruned' => 'Deleted :count old search logs.',
        ],
    ],

    'synonyms' => [
        'single' => 'Synonym Group',
        'plural' => 'Synonyms',
        'sections' => [
            'group' => 'Synonym Group',
        ],
        'fields' => [
            'word' => 'Word',
            'synonyms' => 'Synonyms',
            'model' => 'Model',
            'created' => 'Created',
            'updated' => 'Updated',
            'engine_settings' => 'Engine-Specific Settings',
            'setting' => 'Setting',
            'value' => 'Value',
            'synonym' => 'Synonym',
        ],
        'helpers' => [
            'model' => 'Select the model this synonym group applies to.',
            'word' => 'The main word that will have synonyms.',
            'synonyms' => 'Enter words that should be treated as synonyms for the main word.',
            'engine_settings' => 'Additional settings for the selected engine (optional).',
        ],
        'actions' => [
            'add_synonym' => 'Add Synonym',
        ],
    ],

    'widgets' => [
        'popular_searches' => [
            'heading' => 'Popular Searches (Last 30 Days)',
            'empty' => 'No search data available yet.',
            'searches_count' => ':count searches',
        ],
        'index_status' => [
            'heading' => 'Search Index Status',
            'total_models' => 'Total Models',
            'indexed_models' => 'Indexed Models',
            'total_records' => 'Total Records',
            'indexed_records' => 'Indexed Records',
            'engines_in_use' => 'Engines in Use',
        ],
    ],

    'actions' => [
        'import' => [
            'label' => 'Import to Index',
            'modal_heading' => 'Import to Search Index',
            'modal_description' => 'This will import all records from this model into the search index. This might take a while for large datasets.',
            'modal_submit' => 'Yes, import them',
            'success' => 'Successfully imported all :model records.',
            'failed' => 'Failed to import: :message',
        ],
        'refresh' => [
            'label' => 'Refresh Index',
            'modal_heading' => 'Refresh Search Index',
            'modal_description' => 'This will remove all records and re-import them. This might take a while for large datasets.',
            'modal_submit' => 'Yes, refresh',
            'success' => 'Successfully refreshed index for :model.',
            'failed' => 'Failed to refresh: :message',
        ],
        'flush' => [
            'label' => 'Flush Index',
            'modal_heading' => 'Flush Search Index',
            'modal_description' => 'This will remove all records from the search index. This action cannot be undone.',
            'modal_submit' => 'Yes, flush them',
            'success' => 'Successfully flushed all :model records from search index.',
            'failed' => 'Failed to flush: :message',
        ],
        'sync_index_settings' => [
            'label' => 'Sync Index Settings',
            'modal_heading' => 'Sync Index Settings',
            'modal_description' => 'This will push stored index settings (filterable, sortable, searchable attributes) to the search engine. Run this after changing engine settings.',
            'modal_submit' => 'Yes, sync',
            'success' => 'Index settings synced successfully.',
            'failed' => 'Failed to sync index settings: :message',
        ],
    ],

    'engine_settings' => [
        'meilisearch' => [
            'section' => 'Meilisearch Index Settings',
            'filterable_attributes' => 'Filterable attributes',
            'sortable_attributes' => 'Sortable attributes',
            'searchable_attributes' => 'Searchable attributes',
            'help' => 'Define attributes used for filtering (where), sorting (orderBy), and search. See [Meilisearch settings](https://docs.meilisearch.com/reference/api/settings.html).',
        ],
        'algolia' => [
            'section' => 'Algolia Index Settings',
            'searchable_attributes' => 'Searchable attributes',
            'attributes_for_faceting' => 'Attributes for faceting',
            'ranking' => 'Ranking (JSON array)',
            'custom_ranking' => 'Custom ranking',
            'help' => 'Configure index settings for Algolia. See [Algolia index settings](https://www.algolia.com/doc/rest-api/search/#tag/Indices/operation/setSettings).',
        ],
        'typesense' => [
            'help' => 'For Typesense: ensure your model\'s toSearchableArray() casts id to string and created_at to UNIX timestamp if used. Collection schema is defined in config/scout.php. See [Typesense schema documentation](https://typesense.org/docs/latest/api/collections.html#schema-parameters).',
        ],
    ],
];
