<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | File / Webserver Paths for CMS Content
    |--------------------------------------------------------------------------
    |
    | Paths to, for instance, CMS file and image uploads, relative to the
    | Laravel application root directory.
    |
    */

    'paths' => [

        // Base external path for generating URLs to CMS content
        'base_external' => config('app.url'),

        // Base internal path for generating internal paths to CMS content
        'base_internal' => 'public',

        // Relative path to images from laravel/server root
        'images' => 'cms_img',

        // Relative path to file uploads from laravel/server root
        'files'  => 'cms/uploads',
    ],


    /*
    |--------------------------------------------------------------------------
    | CMS-specific Table Names
    |--------------------------------------------------------------------------
    |
    | CMS Content database table names for commonly used CMS entities.
    |
    */

    'tables' => [

        // Prefix string for module tables
        'prefix' => 'cms_',

        // Postfix string for multilingual tables
        'translation_postfix' => '_ml',

        // Meta-data for cms, used for CMS section grouping, etc
        'meta' => [],

        // CMS data table relevant for front-end
        'languages' => 'cms_language',
    ],


    /*
    |--------------------------------------------------------------------------
    | Relationships and References
    |--------------------------------------------------------------------------
    |
    | Configuration of how the CmsModel should handle relationships and what
    | data structure it may expect to find in the CMS database.
    |
    */

    'relations' => [],

    /*
    |--------------------------------------------------------------------------
    | Translatable / Multilingual Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration of translatable models and other 'ML' CMS database content.
    |
    */

    'translatable' => [

        // Translation table key to indicate locale (ml language id)
        'locale_key' => 'language',

        // Translation foreign key to translated belongsTo parent
        'translation_foreign_key' => 'entry',

        // The column for the locale 'code' in cms_languages
        'locale_code_column' => 'code',

        // The postfix for the translation table -- what to add to a module table to get the translation table
        'translation_table_postfix' => '_ml',
    ],


    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Configuration of caching as applied to standard CMS models and relations.
    | By default, the Rememberable Eloquent trait is used for caching.
    |
    */

    // Cache configuration for standard model / cms relations -- time in minutes (Rememberable)
    // Set to 0 to disable caching
    'cache' => [

        // CMS Languages
        'languages' => 86400,

        // Resizes for Images (looked up for images by fieldId)
        'resizes' => 60,

        // Slugs table entries
        'slugs' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Configuration of (global) scopes for models (and perhaps other classes).
    |
    */

    'scopes' => [

        'only_active' => [
            'column' => 'active',
        ],

        'position_order' => [
            'column' => 'position',
        ],

        'for_organization' => [
            'column' => 'organization',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Slugs
    |--------------------------------------------------------------------------
    |
    | Configuration of model slug handling. An adapted version of Sluggable
    | is used by default, but the original CMS table setup will be used;
    | only use this is if is a compatible setup (it is not a standard part
    | of our CMS (yet)).
    |
    | This expects to find a table with slugs for all modules, so with
    | a `module_id`, `entry_id` and `language_id` column -- and a field for
    | the slug itself.
    |
    */

    'slugs' => [

        // The slug column on the slugs table
        'column' => 'slug',

        // If the slugs table can have inactive slugs, set the boolean column here
        // False if there is no such column
        'active_column' => false,
    ],


    /*
    |--------------------------------------------------------------------------
    | Automatic Code Generation
    |--------------------------------------------------------------------------
    |
    | Configuration of generator for models and other code files
    |
    */
    
    'generator' => [

        // Which namespace to prepend for generated content
        'namespace' => [
            'models'       => 'App\\Models\\Aalberts',
            'requests'     => 'App\\Http\\Requests',
            'repositories' => 'App\\Repositories',
            'presenters'   => 'Aalberts\\Models\\Presenters',
        ],

        'aesthetics' => [
            // Whether to sort use import statements by their length (false sorts them alphabetically)
            'sort_imports_by_string_length' => false,
        ],


        // The FQN's for the standard CMS models for special relationships (and CMS categories)
        'standard_models' => [],

        /*
        |--------------------------------------------------------------------------
        | Model Generation
        |--------------------------------------------------------------------------
        |
        | Settings affecting automatic generation of Eloquent-based Model files.
        |
        */

        // Model-generation-specific settings
        'models' => [

            // FQN for base adapter model for generated models to extend
            //'extend_model' => "Czim\\PxlCms\\Models\\CmsModel",
            'extend_model' => "Aalberts\\Models\\CmsModel",

            // FQN for (optional) traits to import/use (whether they are used is not determined by this)
            'traits' => [
                'listify_fqn'             => "Czim\\Listify\\Listify",
                'listify_constructor_fqn' => "Aalberts\\Models\\ListifyConstructorTrait",
                'translatable_fqn'        => "Czim\\PxlCms\\Translatable\\Translatable",
                'rememberable_fqn'        => "Watson\\Rememberable\\Rememberable",

                'scope_active_fqn'        => "Czim\\PxlCms\\Models\\Scopes\\OnlyActive",
                'scope_position_fqn'      => "Czim\\PxlCms\\Models\\Scopes\\PositionOrdered",
                'scope_cmsordered_fqn'    => "Czim\\PxlCms\\Models\\Scopes\\CmsOrdered",
                'presentable_fqn'         => "Laracasts\\Presenter\\PresentableTrait",
            ],

            // How to handle default/global scopes for models
            'scopes' => [

                // Available modes for each scope are:
                //
                //  'global'        for a global scope that may be ignored with ::withInactive() (scope trait)
                //  'method'        for adding a scope public method scopeActive() to each model
                //  null / false    do nothing, don't add scopes, all records returned by default

                // Scope for the e_active flag, only return active records.
                // If using 'method', the '.._method' defines the scope method name that will be used
                // ('scope' is prefixed automatically).
                'only_active'           => 'global',
                'only_active_method'    => 'active',

                // Scope order by e_position (the listify column)
                'position_order'        => 'global',
                'position_order_method' => 'ordered',

                // For Aalberts organization
                'for_organization'      => 'global',
            ],

            // The model name prefix settings help keep things organised for multi-menu, multi-group cmses
            // everything enabled would result in a classname like: "MenuGroupSectionModule"
            // the prefixes can be independently applied ("MenuModule", "MenuGroupModule", etc)
            //
            // Note that duplicate model names are resolved by prefixing first section, then group,
            // then menu names until this results in unique names. This behavior will occur even if
            // any of the prefixes are disabled here.
            //
            // Note that you should be careful when overriding model/module names, since duplicate name
            // checks are NOT done for forced names!
            'model_name' => [
                // Singularize the names of all models (using str_singular)
                'singularize_model_names' => true,
            ],

            // Pluralize the names of reversed relationships if they are hasMany
            'pluralize_reversed_relationship_names' => true,
            // Same, but for self-referencing relationships
            'pluralize_reversed_relationship_names_for_self_reference' => true,

            // If used, simplify namespaces of standard models through use statements
            'include_namespace_of_standard_models' => true,

            // If a (reverse) relationship's name is already taken by an attribute
            // in the model, add this to prevent duplicate names
            'relationship_fallback_postfix' => 'Reference',

            // If a (reverse) relationship is self-referencing (on the model), the
            // relationship name gets this postfixed to prevent duplicate names
            'relationship_reverse_postfix'  => 'Reverse',

            // Postfix string for translation model
            'translation_model_postfix' => 'Translation',

            // Whether to allow overriding the current locale for a translated standard model
            // relation (such as images/files) through a parameter on the relations method
            'allow_locale_override_on_translated_model_relation' => true,

            // Singularize relationship names for hasOne and belongsTo relationships that have only 1 possible match
            // This is not used, since it would break the database dependency!
            //'singularize_single_relationships' => true,

            // Whether to add foreign key attribute names to the $hidden property
            'hide_foreign_key_attributes' => true,

            // Whether to use rememberable trait on models generated
            'enable_rememberable_cache' => true,

            // Whether to enable laravel timestamps for models with created_at and update_at
            "enable_timestamps_on_models_with_suitable_attributes" => true,

            // The date property type (or FQN) to use for ide-helper tags referring to date fields
            'date_property_fqn' => '\\Carbon\\Carbon',

            // If adding hidden attributes for a model, always add these attributes to hide aswell
            'default_hidden_fields' => [
                'e_active',
                'e_position',
                'e_category_id',
                'e_user_id',
            ],

            // Whether to add default $attributes values for the model based on CMS Field defaults
            'include_defaults' => true,

            // If slugs-functionality is enabled, this describes how the models should be analyzed
            'slugs' => [

                // Whether to do any slug analyzing or handling for models
                'enable' => true,

                // Whether slug handling is always interactive (overrides artisan command -i flag)
                'interactive' => false,

                // Whether interactive mode should go by what are procedurally considered 'candidates' for
                // Sluggable. If this is false, the user gets to decide without the Generator's suggestions.
                'candidate_based' => false,

                // Which attributes/columns, if present, to consider candidates for sluggable source
                // the order determines which one to pick first (if not using interactive command)
                'slug_source_columns' => [
                    'name',  'naam',
                    'title', 'titel',
                ],

                // Which attributes/columns, if present, to consider a slug stored directly on the model
                'slug_columns' => [ 'slug' ],

                // what to do if we find a 'slug' attribute on the model itself (might be great)

                // whether to allow updates to the slug when saving the model from without the CMS

                // Default locale value to fill in for slugs that are not translated
                // Use locales, not language IDs: 'nl' for language_id 116.
                'untranslated_locale' => null,

                // Whether the Sluggable 'on_update' config flag is set to true by default
                // Note that it might be better to handle this through the published sluggable config!
                'resluggify_on_update' => false,

                // The FQN to the interface to implement on sluggable models
                'sluggable_interface' => 'Cviebrock\EloquentSluggable\SluggableInterface',

                // The FQN to the trait to use for sluggable models
                'sluggable_trait' => 'Czim\PxlCms\Sluggable\SluggableTrait',

                // The FQN to the trait to use for parent models of translation models that are sluggable
                'sluggable_translated_trait' => 'Czim\PxlCms\Sluggable\SluggableTranslatedTrait',
            ],


            // Settings for ide-helper content to add to models
            'ide_helper' => [

                // Whether to add (id-helper data to) a docblock for the model
                'add_docblock' => true,

                // Whether to add @property tags for the magic attribute properties of the model
                'tag_attribute_properties' => true,

                // Whether to add @property-read tags for the model's relationships
                'tag_relationship_magic_properties' => true,

                // Whether to add @method static tags for whereProperty($value) type methods
                // this can get quite spammy for models with many attributes
                'tag_magic_where_methods_for_attributes' => false,
            ],
        ],


        /*
        |--------------------------------------------------------------------------
        | Repository Generation
        |--------------------------------------------------------------------------
        |
        | Settings affecting automatic generation of Repository files.
        |
        */

        'repositories' => [

            // FQN for base repository class for generated repositories to extend
            'extend_class' => "Czim\\Repository\\BaseRepository",

            // Postfix model name with this to make repository name
            'name_postfix' => 'Repository',

            // Whether to skip repository analysis/writing altogether (will not ask any questions either)
            'skip' => true,

            // Whether to create a repository for each model generated
            'create_for_all_models' => true,

            // If not creating for all models, insert module IDs in this array to
            // select which models/modules to create repositories for
            // If none are set and interactive mode is disabled, no repositories will be written.
            'create_for_models' => [],
        ],


        /*
        |--------------------------------------------------------------------------
        | Ignoring CMS content while analyzing generating
        |--------------------------------------------------------------------------
        |
        | ** This is not currently supported **
        | Just a placeholder here as a reminder that this might be a good idea.
        |
        */

        'ignore' => [

            // Indicate modules by their number: cms_m##_<some_name>
            'modules' => [
            ],

        ],

        /*
        |--------------------------------------------------------------------------
        | Overriding Automatically Generated Content
        |--------------------------------------------------------------------------
        |
        | If you want to override specific properties or output generated by
        | this package for a given model, you can define the specifics here.
        |
        */

        'overrides' => [],
    ],

    // special section for aalberts pxlcms generator
    // list of pivot tables
    //
    // extra indicates pivot table records .. use as withPivot...
    'aalberts_pivots' => [

        // cmp / item
        'cmp_item_applications' => [
            'item'         => 'cmp_item',
            'applications' => 'cmp_applications',
        ],
        'cmp_item_approvals' => [
            'item'      => 'cmp_item',
            'approvals' => 'cmp_approvals',
        ],
        'cmp_item_colors' => [
            'item'   => 'cmp_item',
            'colors' => 'cmp_colors',
        ],
        'cmp_item_connectiontype' => [
            'item'           => 'cmp_item',
            'connectiontype' => 'cmp_connectiontype',
            'extra' => [
                'connection' => 'integer',
            ],
        ],
        'cmp_item_finishings' => [
            'item'       => 'cmp_item',
            'finishings' => 'cmp_finishings',
        ],
        'cmp_item_item' => [
            'item_from'  => 'cmp_item',
            'item_to'    => 'cmp_item',
            'timestamps' => true,
        ],
        'cmp_item_materials' => [
            'item'      => 'cmp_item',
            'materials' => 'cmp_materials',
        ],
        'cmp_item_productline' => [
            'item'        => 'cmp_item',
            'productline' => 'cmp_productline',
        ],
        'cmp_item_producttype' => [
            'item'        => 'cmp_item',
            'producttype' => 'cmp_producttype',
        ],
        'cmp_item_sealing' => [
            'item'    => 'cmp_item',
            'sealing' => 'cmp_sealing',
        ],
        'cmp_item_shape' => [
            'item'  => 'cmp_item',
            'shape' => 'cmp_shape',
        ],
        'cmp_item_solutions' => [
            'item'      => 'cmp_item',
            'solutions' => 'cmp_solutions',
        ],

        // cmp / product
        'cmp_product_applications'   => [
            'product'      => 'cmp_product',
            'applications' => 'cmp_applications',
        ],
        'cmp_product_approvals'      => [
            'product'   => 'cmp_product',
            'approvals' => 'cmp_approvals',
        ],
        'cmp_product_colors'         => [
            'product' => 'cmp_product',
            'colors'  => 'cmp_colors',
        ],
        'cmp_product_connectiontype' => [
            'product'        => 'cmp_product',
            'connectiontype' => 'cmp_connectiontype',
            'extra'          => [
                'connection' => 'integer',
            ],
        ],
        'cmp_product_contourcode'  => [
            'product'     => 'cmp_product',
            'contourcode' => 'cmp_contourcode',
            'extra'       => [
                'connection' => 'integer',
            ],
        ],
        'cmp_product_finishings'  => [
            'product'    => 'cmp_product',
            'finishings' => 'cmp_finishings',
        ],
        'cmp_product_materials'   => [
            'product'   => 'cmp_product',
            'materials' => 'cmp_materials',
        ],
        'cmp_product_productline' => [
            'product'     => 'cmp_product',
            'productline' => 'cmp_productline',
        ],
        'cmp_product_producttype' => [
            'product'     => 'cmp_product',
            'producttype' => 'cmp_producttype',
        ],
        'cmp_product_sealing'     => [
            'product' => 'cmp_product',
            'sealing' => 'cmp_sealing',
        ],
        'cmp_product_shape'       => [
            'product' => 'cmp_product',
            'shape'   => 'cmp_shape',
        ],
        'cmp_product_solutions'   => [
            'product'   => 'cmp_product',
            'solutions' => 'cmp_solutions',
        ],

        // cms
        'cms_application_content' => [
            'application'  => 'cmp_applications',
            'content'      => 'cms_content',
            'organization' => true,
        ],
        'cms_content_customblock' => [
            'content'      => 'cms_content',
            'customblock'  => 'cms_customblock',
            'organization' => true,
            'position'     => true,
        ],
        'cms_content_download' => [
            'content'      => 'cms_content',
            'download'     => 'cms_download',
            'organization' => true,
            'position'     => true,
        ],
        'cms_content_function' => [
            'content'      => 'cms_content',
            'function'     => 'cms_function',
        ],
        'cms_content_highlightedproduct' => [
            'content'        => 'cms_content',
            'relatedproduct' => 'cms_relatedproduct',  // ?
            'position'       => true,
            'organization'   => false,    // not used
        ],
        'cms_content_news' => [
            'content'      => 'cms_content',
            'news'         => 'cms_news',
            'position'     => true,
            'organization' => false,    // not used
        ],
        'cms_content_project' => [
            'content'      => 'cms_content',
            'project'      => 'cms_project',
            'position'     => true,
            'organization' => false,    // not used
        ],
        'cms_content_relatedproduct' => [
            'content'        => 'cms_content',
            'relatedproduct' => 'cms_relatedproduct',
            'position'       => true,
            'organization'   => false,    // not used
        ],

        'cms_country_filter' => [
            'country'  => 'cms_country',
            'filter'   => 'cms_filter',
            'extra' => [
                'value' => 'integer',
            ],
            'organization' => true,
        ],
        'cms_country_language' => [
            'country'  => 'cms_country',
            'language' => 'cms_language',
            'extra' => [
                'default' => 'boolean',
            ],
            'organization' => true,
        ],
        'cms_country_supplier' => [
            'country'  => 'cms_country',
            'supplier' => 'cmp_supplier',
            'organization' => true,
        ],

        // cms / download
        'cms_download_application' => [
            'download'    => 'cms_download',
            'application' => 'cmp_applications',
        ],
        'cms_download_language' => [
            'download' => 'cms_download',
            'language' => 'cms_language',
        ],
        'cms_download_productline' => [
            'download'    => 'cms_download',
            'productline' => 'cmp_productline',
        ],
        'cms_download_site' => [
            'download' => 'cms_download',
            'site'     => 'cms_site',
        ],
        'cms_download_solution' => [
            'download' => 'cms_download',
            'solution' => 'cmp_solutions',
        ],
        'cms_download_supplier' => [
            'download' => 'cms_download',
            'supplier' => 'cmp_supplier',
        ],

        'cms_news_relatedproduct' => [
            'news'           => 'cms_news',
            'relatedproduct' => 'cms_relatedproduct',
            'position'       => true,
            'organization'   => true,
        ],
        'cms_organization_country' => [
            'organization' => 'cms_organization',
            'country'      => 'cms_country',
            'extra' => [
                'default' => 'boolean',
            ],
        ],
        'cms_organization_language' => [
            'organization' => 'cms_organization',
            'language'     => 'cms_language',
            'extra' => [
                'default' => 'boolean',
            ],
        ],
        'cms_organization_productgroup' => [
            'organization' => 'cms_organization',
            'productgroup' => 'cmp_productgroup',
            'extra' => [
                'default' => 'boolean',
            ],
        ],

        'cms_project_application' => [
            'project'     => 'cms_project',
            'application' => 'cmp_applications',
        ],
        'cms_project_productline' => [
            'project'     => 'cms_project',
            'productline' => 'cmp_productline',
        ],
        'cms_project_solution' => [
            'project'  => 'cms_project',
            'solution' => 'cmp_solutions',
        ],

        'cms_solution_content' => [
            'content'  => 'cms_content',
            'solution' => 'cmp_solutions',
            'organization' => true,
        ],

        // press
        'press_dimension_productline' => [
            'dimension'   => 'press_dimension',
            'productline' => 'press_productline',
        ],
    ],

];
