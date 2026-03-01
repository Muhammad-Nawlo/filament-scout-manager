<?php

return [
    'navigation' => [
        'group' => 'البحث',
        'models' => 'النماذج القابلة للبحث',
        'logs' => 'سجلات البحث',
        'synonyms' => 'المرادفات',
    ],

    'common' => [
        'not_available' => 'غير متاح',
        'guest' => 'زائر',
        'yes' => 'نعم',
        'no' => 'لا',
    ],

    'models' => [
        'single' => 'نموذج قابل للبحث',
        'plural' => 'النماذج القابلة للبحث',
        'fields' => [
            'name' => 'النموذج',
            'index_name' => 'اسم الفهرس',
            'total_records' => 'إجمالي السجلات',
            'indexed_records' => 'السجلات المفهرسة',
            'searchable_fields' => 'الحقول القابلة للبحث',
            'engine' => 'محرك البحث',
            'searchable' => 'قابل للبحث',
            'custom' => 'مخصص',
            'last_sync' => 'آخر مزامنة',
            'model_class' => 'صنف النموذج',
            'engine_override' => 'تجاوز المحرك',
            'engine_settings' => 'إعدادات خاصة بالمحرك',
            'index_name_override' => 'اسم فهرس مخصص',
            'should_be_searchable' => 'تفعيل البحث',
            'queue_connection' => 'اتصال الطابور (استشاري)',
            'no_fields_configured' => 'لا توجد حقول مهيأة',
            'more_count' => '+:count المزيد',
        ],
        'sections' => [
            'configuration' => 'إعدادات النموذج',
            'index_settings' => 'إعدادات الفهرس',
            'batch_indexing_tips' => 'نصائح لفهرسة الدفعات',
            'database_engine_tips' => 'نصائح محرك قاعدة البيانات',
            'import_optimization_tips' => 'نصائح تحسين الاستيراد',
        ],
        'helpers' => [
            'searchable_fields' => 'اختر الحقول التي يجب أن تكون قابلة للبحث (يتطلب تنفيذًا مخصصًا للدالة toSearchableArray).',
            'engine_override' => 'تجاوز محرك البحث الافتراضي لهذا النموذج.',
            'engine_settings' => 'قم بتكوين إعدادات الفهرس أدناه باختيار محرك. ثم شغّل "مزامنة إعدادات الفهرس" لدفعها إلى محرك البحث.',
            'index_name_override' => 'اتركه فارغًا لاستخدام searchableAs الافتراضي.',
            'not_indexed' => 'غير مفهرس',
            'should_be_searchable_help' => 'للتحكم في السجلات التي تُفهرس، نفّذ shouldBeSearchable() في نموذجك. هذا الحقل للمعلومات فقط؛ الإضافة لا تتجاوز سلوك Scout.',
            'queue_connection_help' => 'تكوين طابور Scout عام (config/scout.php). هذه القيمة للمعلومات فقط ما لم تنفّذ توجيه طابور مخصصًا في تطبيقك.',
            'database_engine_help' => 'مع محرك قاعدة البيانات، لا يُطبَّق shouldBeSearchable(). استخدم شرط where في استعلامات البحث وفهارس النص الكامل في migrations. راجع توثيق محرك قاعدة بيانات Laravel Scout.',
            'batch_indexing_tips_content' => 'استخدم Model::withoutSyncingToSearch(fn () => { ... }) في الكود للتحديثات الجماعية دون مزامنة كل تغيير. بعد الدفعة، شغّل: php artisan scout:import "App\\Models\\ModelName". هذا تحكم على مستوى التطبيق؛ الإضافة لا تضيف مفاتيح إيقاف.',
            'database_engine_tips_content' => 'مع محرك قاعدة البيانات يمكنك استخدام السمات SearchUsingFullText و SearchUsingPrefix على toSearchableArray() للتحكم في طريقة بحث الأعمدة. أضف فهرس full-text في migration للأعمدة ذات النص الكامل، مثل $table->fullText([\'title\', \'description\']). راجع توثيق محرك قاعدة بيانات Laravel Scout. الإضافة لا تُكوّن هذه السمات.',
            'import_optimization_tips_content' => 'نفّذ makeAllSearchableUsing(Builder $query) لتحميل العلاقات مسبقًا في استعلام الاستيراد (مثل return $query->with([\'category\'])). نفّذ makeSearchableUsing(Collection $models) لتحميل العلاقات على كل جزء (مثل return $models->load(\'author\')). هذا يتجنب N+1 أثناء الفهرسة. الإضافة لا تتجاوز هذه الدوال.',
        ],
        'actions' => [
            'configure' => 'إعداد',
            'bulk_import' => 'استيراد المحدد',
            'bulk_flush' => 'تفريغ المحدد',
        ],
        'notifications' => [
            'import_completed' => 'اكتمل الاستيراد: نجح :success وفشل :fail.',
            'flush_completed' => 'اكتمل التفريغ: نجح :success وفشل :fail.',
        ],
        'engine_options' => [
            'default' => 'استخدام الافتراضي',
            'algolia' => 'Algolia',
            'meilisearch' => 'Meilisearch',
            'typesense' => 'Typesense',
            'database' => 'محرك قاعدة البيانات',
            'collection' => 'محرك المجموعات',
        ],
        'engine_badges' => [
            'typesense' => 'Typesense',
            'database' => 'قاعدة البيانات',
            'collection' => 'المجموعات',
        ],
        'queue_options' => [
            'sync' => 'متزامن (بدون طابور)',
            'database' => 'قاعدة البيانات',
            'redis' => 'Redis',
            'sqs' => 'SQS',
        ],
    ],

    'logs' => [
        'single' => 'سجل بحث',
        'plural' => 'سجلات البحث',
        'fields' => [
            'query' => 'الاستعلام',
            'model' => 'النموذج',
            'results' => 'النتائج',
            'time' => 'الوقت (ث)',
            'user' => 'المستخدم',
            'ip_address' => 'عنوان IP',
            'success' => 'نجاح',
            'date' => 'التاريخ',
            'from' => 'من',
            'until' => 'إلى',
            'user_agent' => 'وكيل المستخدم',
            'created' => 'تاريخ الإنشاء',
            'before' => 'حذف السجلات قبل',
        ],
        'filters' => [
            'successful_only' => 'الناجحة فقط',
            'failed_only' => 'الفاشلة فقط',
        ],
        'actions' => [
            'view' => 'عرض التفاصيل',
            'prune_old' => 'حذف الأقدم من...',
            'close' => 'إغلاق',
        ],
        'modal' => [
            'details_heading' => 'تفاصيل سجل البحث',
        ],
        'values' => [
            'seconds' => ':seconds ثانية',
        ],
        'notifications' => [
            'pruned' => 'تم حذف :count من سجلات البحث القديمة.',
        ],
    ],

    'synonyms' => [
        'single' => 'مجموعة مرادفات',
        'plural' => 'المرادفات',
        'sections' => [
            'group' => 'مجموعة مرادفات',
        ],
        'fields' => [
            'word' => 'الكلمة',
            'synonyms' => 'المرادفات',
            'model' => 'النموذج',
            'created' => 'أُنشئ في',
            'updated' => 'آخر تحديث',
            'engine_settings' => 'إعدادات خاصة بالمحرك',
            'setting' => 'الإعداد',
            'value' => 'القيمة',
            'synonym' => 'مرادف',
        ],
        'helpers' => [
            'model' => 'اختر النموذج الذي تطبق عليه مجموعة المرادفات هذه.',
            'word' => 'الكلمة الأساسية التي ستُضاف لها مرادفات.',
            'synonyms' => 'أدخل الكلمات التي يجب اعتبارها مرادفات للكلمة الأساسية.',
            'engine_settings' => 'إعدادات إضافية للمحرك المحدد (اختياري).',
        ],
        'actions' => [
            'add_synonym' => 'إضافة مرادف',
        ],
    ],

    'widgets' => [
        'popular_searches' => [
            'heading' => 'أكثر عمليات البحث شيوعًا (آخر 30 يومًا)',
            'empty' => 'لا توجد بيانات بحث متاحة بعد.',
            'searches_count' => ':count عملية بحث',
        ],
        'index_status' => [
            'heading' => 'حالة فهرس البحث',
            'total_models' => 'إجمالي النماذج',
            'indexed_models' => 'النماذج المفهرسة',
            'total_records' => 'إجمالي السجلات',
            'indexed_records' => 'السجلات المفهرسة',
            'engines_in_use' => 'المحركات المستخدمة',
        ],
    ],

    'actions' => [
        'import' => [
            'label' => 'استيراد إلى الفهرس',
            'modal_heading' => 'استيراد إلى فهرس البحث',
            'modal_description' => 'سيؤدي هذا إلى استيراد جميع السجلات من هذا النموذج إلى فهرس البحث. قد يستغرق ذلك وقتًا مع البيانات الكبيرة.',
            'modal_submit' => 'نعم، قم بالاستيراد',
            'success' => 'تم استيراد جميع سجلات :model بنجاح.',
            'failed' => 'فشل الاستيراد: :message',
        ],
        'refresh' => [
            'label' => 'تحديث الفهرس',
            'modal_heading' => 'تحديث فهرس البحث',
            'modal_description' => 'سيؤدي هذا إلى إزالة جميع السجلات ثم إعادة استيرادها. قد يستغرق ذلك وقتًا مع البيانات الكبيرة.',
            'modal_submit' => 'نعم، تحديث',
            'success' => 'تم تحديث الفهرس لـ :model بنجاح.',
            'failed' => 'فشل التحديث: :message',
        ],
        'flush' => [
            'label' => 'تفريغ الفهرس',
            'modal_heading' => 'تفريغ فهرس البحث',
            'modal_description' => 'سيؤدي هذا إلى إزالة جميع السجلات من فهرس البحث. لا يمكن التراجع عن هذا الإجراء.',
            'modal_submit' => 'نعم، قم بالتفريغ',
            'success' => 'تم تفريغ جميع سجلات :model من فهرس البحث بنجاح.',
            'failed' => 'فشل التفريغ: :message',
        ],
        'sync_index_settings' => [
            'label' => 'مزامنة إعدادات الفهرس',
            'modal_heading' => 'مزامنة إعدادات الفهرس',
            'modal_description' => 'سيؤدي هذا إلى دفع إعدادات الفهرس المخزنة (السمات القابلة للتصفية والترتيب والبحث) إلى محرك البحث. قم بتشغيل هذا بعد تغيير إعدادات المحرك.',
            'modal_submit' => 'نعم، مزامنة',
            'success' => 'تم مزامنة إعدادات الفهرس بنجاح.',
            'failed' => 'فشل مزامنة إعدادات الفهرس: :message',
        ],
    ],

    'engine_settings' => [
        'meilisearch' => [
            'section' => 'إعدادات فهرس Meilisearch',
            'filterable_attributes' => 'السمات القابلة للتصفية',
            'sortable_attributes' => 'السمات القابلة للترتيب',
            'searchable_attributes' => 'السمات القابلة للبحث',
            'help' => 'حدد السمات المستخدمة للتصفية والترتيب والبحث. راجع [إعدادات Meilisearch](https://docs.meilisearch.com/reference/api/settings.html).',
        ],
        'algolia' => [
            'section' => 'إعدادات فهرس Algolia',
            'searchable_attributes' => 'السمات القابلة للبحث',
            'attributes_for_faceting' => 'السمات للتقسيم',
            'ranking' => 'الترتيب (مصفوفة JSON)',
            'custom_ranking' => 'ترتيب مخصص',
            'help' => 'تكوين إعدادات الفهرس لـ Algolia. راجع [إعدادات فهرس Algolia](https://www.algolia.com/doc/rest-api/search/#tag/Indices/operation/setSettings).',
        ],
        'typesense' => [
            'help' => 'بالنسبة إلى Typesense: تأكد من أن toSearchableArray() في النموذج يلقي id كنص وcreated_at كطابع زمني لـ UNIX إن وُجد. يتم تعريف مخطط المجموعة في config/scout.php. راجع [توثيق مخطط Typesense](https://typesense.org/docs/latest/api/collections.html#schema-parameters).',
        ],
    ],
];
