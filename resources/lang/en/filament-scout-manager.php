<?php

return [
    'navigation' => [
        'group' => 'Search',
        'models' => 'Searchable Models',
        'logs' => 'Search Logs',
        'synonyms' => 'Synonyms',
    ],

    'models' => [
        'title' => 'Searchable Models',
        'fields' => [
            'name' => 'Model',
            'index_name' => 'Index Name',
            'total_records' => 'Total Records',
            'indexed_records' => 'Indexed Records',
            'searchable_fields' => 'Searchable Fields',
            'engine' => 'Engine',
            'last_sync' => 'Last Sync',
        ],
        'actions' => [
            'import' => 'Import to Index',
            'flush' => 'Flush Index',
            'refresh' => 'Refresh Index',
            'configure' => 'Configure',
        ],
    ],

    'logs' => [
        'title' => 'Search Logs',
        'fields' => [
            'query' => 'Search Query',
            'model' => 'Model',
            'results' => 'Results',
            'time' => 'Time',
            'user' => 'User',
            'successful' => 'Successful',
        ],
    ],

    'synonyms' => [
        'title' => 'Search Synonyms',
        'fields' => [
            'word' => 'Word',
            'synonyms' => 'Synonyms',
            'model' => 'Model',
        ],
    ],
];
