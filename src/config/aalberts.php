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
    | Queries / Product matching
    |--------------------------------------------------------------------------
    */

    'queries' => [
        'uses-is-webitem' => true,
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

    /*
    |--------------------------------------------------------------------------
    | Item Fields
    |--------------------------------------------------------------------------
    |
    | Item fields to be considered when displaying dimensions. Value is the
    | display value in table headers.
    | Note that some fields may have special display conditions, see ItemPresenter.
    */

    'item-fields' => [
        'dimension' => [
            'productbowrange'                             => 'r',
            'productl0'                                   => 'L0',
            'productdeltal'                               => 'Δl',
            'producth1'                                   => 'H1',
            'producth2'                                   => 'H2',
            'producth3'                                   => 'H3',
            'producta'                                    => 'a',
            'productb'                                    => 'b',
            'productc'                                    => 'c',
            'productlengthofconnection0'                  => 'l0',
            'productexternaltubediameterofconnection0'    => 'd0',
            'productnominalinternaldiameterofconnection0' => 'd0',
            'productexternaldiameterofconnection0'        => 'D0',
            'productinsertiondepth0'                      => 'es0',
            'productoperatinglengthofconnection0'         => 'z0',
            'productkeysize0'                             => 'slw0',
            'productswiffelkeysize0'                      => 'sks0',
            'productlengthofconnection1'                  => 'l1',
            'productexternaltubediameterofconnection1'    => 'd1',
            'productnominalinternaldiameterofconnection1' => 'd1',
            'productexternaldiameterofconnection1'        => 'D1',
            'productinsertiondepth1'                      => 'es1',
            'productoperatinglengthofconnection1'         => 'z1',
            'productkeysize1'                             => 'slw1',
            'productswiffelkeysize1'                      => 'sks1',
            'productlengthofconnection2'                  => 'l2',
            'productexternaltubediameterofconnection2'    => 'd2',
            'productnominalinternaldiameterofconnection2' => 'd2',
            'productexternaldiameterofconnection2'        => 'D2',
            'productinsertiondepth2'                      => 'es2',
            'productoperatinglengthofconnection2'         => 'z2',
            'productkeysize2'                             => 'slw2',
            'productswiffelkeysize2'                      => 'sks2',
            'productlengthofconnection3'                  => 'l3',
            'productexternaltubediameterofconnection3'    => 'd3',
            'productnominalinternaldiameterofconnection3' => 'd3',
            'productexternaldiameterofconnection3'        => 'D3',
            'productinsertiondepth3'                      => 'es3',
            'productoperatinglengthofconnection3'         => 'z3',
            'productkeysize3'                             => 'slw3',
            'productswiffelkeysize3'                      => 'sks3',
            'productlengthofconnection4'                  => 'l4',
            'productexternaltubediameterofconnection4'    => 'd4',
            'productnominalinternaldiameterofconnection4' => 'd4',
            'productexternaldiameterofconnection4'        => 'D4',
            'productinsertiondepth4'                      => 'es4',
            'productoperatinglengthofconnection4'         => 'z4',
            'productkeysize4'                             => 'slw4',
            'productswiffelkeysize4'                      => 'sks4',
            'productlengthofconnection5'                  => 'l5',
            'productexternaltubediameterofconnection5'    => 'd5',
            'productnominalinternaldiameterofconnection5' => 'd5',
            'productexternaldiameterofconnection5'        => 'D5',
            'productinsertiondepth5'                      => 'es5',
            'productoperatinglengthofconnection5'         => 'z5',
            'productkeysize5'                             => 'slw5',
            'productswiffelkeysize5'                      => 'sks5',
            'productlengthofconnection6'                  => 'l6',
            'productexternaltubediameterofconnection6'    => 'd6',
            'productnominalinternaldiameterofconnection6' => 'd6',
            'productexternaldiameterofconnection6'        => 'D6',
            'productinsertiondepth6'                      => 'es6',
            'productoperatinglengthofconnection6'         => 'z6',
            'productkeysize6'                             => 'slw6',
            'productswiffelkeysize6'                      => 'sks6',
            'productlengthofconnection7'                  => 'l7',
            'productexternaltubediameterofconnection7'    => 'd7',
            'productnominalinternaldiameterofconnection7' => 'd7',
            'productexternaldiameterofconnection7'        => 'D7',
            'productinsertiondepth7'                      => 'es7',
            'productoperatinglengthofconnection7'         => 'z7',
            'productkeysize7'                             => 'slw7',
            'productswiffelkeysize7'                      => 'sks7',
            'productlengthofconnection8'                  => 'l8',
            'productexternaltubediameterofconnection8'    => 'd8',
            'productnominalinternaldiameterofconnection8' => 'd8',
            'productexternaldiameterofconnection8'        => 'D8',
            'productinsertiondepth8'                      => 'es8',
            'productoperatinglengthofconnection8'         => 'z8',
            'productkeysize8'                             => 'slw8',
            'productswiffelkeysize8'                      => 'sks8',
        ],
    ],
    
];
