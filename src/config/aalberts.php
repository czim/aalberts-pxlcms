<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */

    // ID for the organization (2 = VSH, etc)
    'organization' => 2,

    // Code (in Compano) for the organization (VSH, Seppelfricke, etc)
    'salesorganizationcode' => 'VSH',


    /*
    |--------------------------------------------------------------------------
    | Paths / URLs
    |--------------------------------------------------------------------------
    */

    'paths' => [

        // location of downloadable files (and images) on the aalberts CMS server
        'uploads' => 'http://core.aiflowcontrol.com/upload',
    ],


    /*
    |--------------------------------------------------------------------------
    | Translation
    |--------------------------------------------------------------------------
    */

    'translator' => [

        'cache' => [
            // Cache keys
            'key'        => 'aalberts-translation:',
            'update-key' => 'aalberts-translation-update',

            // Time to live in minutes
            'ttl' => 86400,

            // Tag to apply for translation cache
            'tag' => 'translation',

            // The locales to cache empty results for (to prevent lookups where the label itself
            // should be used as the translation).
            'locales' => [
                'nl',
                'en',
                'de',
            ],
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
    ],

];
