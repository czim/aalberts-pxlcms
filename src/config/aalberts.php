<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */

    // ID for the organization (2 = VSH, etc)
    'organization' => env('AALBERTS_ORGANIZATION', 2),

    // Code (in Compano) for the organization (VSH, Seppelfricke, etc)
    'salesorganizationcode' => env('AALBERTS_ORGANIZATION_KEY', 'VSH'),

    // Slug in cmp_suppliers table to use for default supplier
    'supplier-slug' => null,

    /*
    |--------------------------------------------------------------------------
    | Paths / URLs
    |--------------------------------------------------------------------------
    */

    'paths' => [

        // location of downloadable files (and images) on the aalberts CMS server
        'uploads' => 'http://core.aiflowcontrol.com/upload',

        // location of compano resources (for product drawings/images, etc)
        'compano' => 'http://www.aalberts.compano.nl',
        
    ],


    /*
    |--------------------------------------------------------------------------
    | Translation
    |--------------------------------------------------------------------------
    */

    'translator' => [

        // Whether to automatically add phrases if they do not exist in the database.
        // This requires the event to be set up; otherwise it will have no effect.
        // This is mainly intended to allow easy dis/enabling of the functionality
        // for a given environment.
        'add-phrases' => env('AALBERTS_ADD_PHRASES'),

        'cache' => [
            // Cache keys
            'key'        => 'aalberts-translation:',
            'update-key' => 'aalberts-translation-update',

            // Time to live in minutes
            'ttl' => 1440,

            // The locales to cache empty results for (to prevent lookups where the label itself
            // should be used as the translation).
            'locales' => [
                'nl',
                'en',
                'de',
            ],
        ],

        // Common phrases that should be offered by the translator.
        // Key = internal label, value = the phrase label in the CMS.
        'phrase-mapping' => [
            'yes'    => 'common.yes',
            'no'     => 'common.no',
            'gas'    => 'common.gas',
            'liquid' => 'common.liquid',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    | The names of routes to use for (slug) links
    */
    
    'routes' => [

        'projects-detail' => 'projects-detail', // 'cases-detail'
        'news-detail'     => 'news-detail',
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    */

    'cache' => [

        // Default ttl in minutes. Compano concerns cmp_ tables (products, items, etc).
        // CMS concerns cms_ tables (news, content,  etc)
        'ttl' => [
            'cms'     => 1440,
            'compano' => 1440,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Special Formatting
    |--------------------------------------------------------------------------
    */

    // the way dates are formatted by presenters
    'date' => [

        // format to use (standard PHP date format string),
        // or 'special' to use a special formatter class
        'format' => 'special',

        // FQN for DateFormatterInterface to use if the format is 'special'
        'special' => \Aalberts\Models\DateFormatters\StandardDateFormatter::class,

        // if we should use trans() to get month translations, the translation
        // key in which the locale definitions can be found - with '01' => 'jan', etc
        'months-translate-key' => 'aalberts.months',

    ],

    /*
    |--------------------------------------------------------------------------
    | Searching
    |--------------------------------------------------------------------------
    |
    | Whether searches performed on the website should be logged to the
    | database. This requires the event listener to be set up, and is merely
    | a way to overrule the bound behavior.
    */

    'log-searches' => env('AALBERTS_LOG_SEARCHES', true),

    /*
    |--------------------------------------------------------------------------
    | Special Content
    |--------------------------------------------------------------------------
    |
    | Labels for 'hard-coded' content. Keyed by internal identifiers used in the
    | code, value is the label in the CMS. This setup will be different for
    | each organization.
    */

    'content' => [
    ],

];
