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
            'queue_connection' => 'Queue Connection',
            'no_fields_configured' => 'No fields configured',
            'more_count' => '+:count more',
        ],
        'sections' => [
            'configuration' => 'Model Configuration',
            'index_settings' => 'Index Settings',
        ],
        'helpers' => [
            'searchable_fields' => 'Select which fields should be searchable (requires custom toSearchableArray implementation).',
            'engine_override' => 'Override the default search engine for this model.',
            'engine_settings' => 'Additional settings for the selected engine (e.g., searchableAttributes for Algolia).',
            'index_name_override' => 'Leave empty to use the default searchableAs.',
            'not_indexed' => 'Not indexed',
        ],
        'actions' => [
            'configure' => 'Configure',
            'bulk_import' => 'Import Selected',
            'bulk_flush' => 'Flush Selected',
        ],
        'notifications' => [
            'import_completed' => 'Import completed: :success succeeded, :fail failed.',
            'flush_completed' => 'Flush completed: :success succeeded, :fail failed.',
        ],
        'engine_options' => [
            'default' => 'Use Default',
            'algolia' => 'Algolia',
            'meilisearch' => 'Meilisearch',
            'database' => 'Database Engine',
            'collection' => 'Collection Engine',
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
    ],
];
