<?php
namespace Aalberts\Generator\Analyzer\Steps;

use Czim\PxlCms\Generator\Analyzer\Steps\AbstractProcessStep;

class ManualAdjustments extends AbstractProcessStep
{
    // from translated => normal
    protected $translatedSwaps = [
        'cmp_item' => [ 'accessedts', 'modifiedts', 'createdts' ],
        'cmp_product' => [ 'accessedts', 'modifiedts', 'createdts' ],
    ];

    protected $casts = [
        'cmp_item' => [
            'accessedts' => 'date',
        ],
        'cmp_product' => [
            'accessedts' => 'date',
        ],
        'cms_news' => [
            'date' => 'date_timestamp', // unix timestamp for a date
        ],
    ];

    protected $dates = [
        'cmp_item'    => [ 'accessedts' ],
        'cmp_product' => [ 'accessedts' ],
    ];

    protected $belongsTo = [
        'cmp_item' => [
            'product'     => [ 'table' => 'cmp_product' ],
            'successor'   => [ 'table' => 'cmp_product' ],
            'predecessor' => [ 'table' => 'cmp_product' ],
        ],
        'cmp_product' => [
            'successor'   => [ 'table' => 'cmp_product' ],
            'predecessor' => [ 'table' => 'cmp_product' ],
        ],
        'cms_application_image' => [
            'entry' => [ 'table' => 'cmp_applications', 'name' => 'application' ],
        ],
        'cms_approval_image' => [
            'entry' => [ 'table' => 'cmp_approvals', 'name' => 'approval' ],
        ],
        'cms_content' => [
            'parent'   => [ 'table' => 'cms_content' ],
            'solution' => [ 'table' => 'cmp_solutions' ],
        ],
        'cms_content_gallery' => [
            'entry' => [ 'table' => 'cms_content', 'name' => 'content' ],
        ],
        'cms_content_gallery_image' => [
            'entry' => [ 'table' => 'cms_content_gallery', 'name' => 'contentGallery' ],
        ],
        'cms_content_tile' => [
            'entry' => [ 'table' => 'cms_content', 'name' => 'content' ],
        ],
        'cms_content_tile_image' => [
            // field 1, 2 ?
            'entry' => [ 'table' => 'cms_content_tile', 'name' => 'contentTile' ],
        ],
        'cms_country' => [
            'parent' => [ 'table' => 'cms_country' ],
        ],
        'cms_customblock' => [
            'content' => [ 'table' => 'cms_content' ],
        ],
        'cms_customblock_image' => [
            'entry' => [ 'table' => 'cms_customblock', 'name' => 'customblock' ],
        ],
        'cms_download_file' => [
            'entry' => [ 'table' => 'cms_download', 'name' => 'download' ],
        ],
        'cms_download_image' => [
            'entry' => [ 'table' => 'cms_download', 'name' => 'download' ],
        ],
        'cms_log_download' => [
            'download' => [ 'table' => 'cms_download' ],
        ],
        'cms_log_email' => [
            'item' => [ 'table' => 'cmp_item' ],
        ],
        'cms_news_gallery' => [
            'entry' => [ 'table' => 'cms_news', 'name' => 'news' ],
        ],
        'cms_news_gallery_image' => [
            'entry' => [ 'table' => 'cms_news_gallery', 'name' => 'newsGallery' ],
        ],
        'cms_productgroup' => [
            'entry' => [ 'table' => 'cmp_productgroup', 'name' => 'companoProductgroup' ]
        ],
        'cms_productgroup_filter' => [
            'productgroup' => [ 'table' => 'cmp_productgroup' ],
            'filter'       => [ 'table' => 'cms_filter' ],
        ],
        'cms_productgroup_filtergroup' => [
            'productgroup' => [ 'table' => 'cmp_productgroup' ],
            'filtergroup'  => [ 'table' => 'cms_filtergroup' ],
        ],
        'cms_productgroup_image' => [
            'entry' => [ 'table' => 'cms_productgroup', 'name' => 'productgroup' ],
        ],
        'cms_productline_image' => [
            'entry' => [ 'table' => 'cmp_productline', 'name' => 'productline' ],
        ],
        'cms_project_gallery' => [
            'entry' => [ 'table' => 'cms_project', 'name' => 'project' ],
        ],
        'cms_project_gallery_image' => [
            'entry' => [ 'table' => 'cms_project_gallery', 'name' => 'projectGallery' ],
        ],
        'cms_project_image' => [
            'entry' => [ 'table' => 'cms_project', 'name' => 'project' ],
        ],
        'cms_relatedproduct_image' => [
            'entry' => [ 'table' => 'cms_relatedproduct', 'name' => 'relatedproduct' ],
        ],
        'cms_solution_image' => [
            'entry' => [ 'table' => 'cmp_solution', 'name' => 'solution' ],
        ],
        'cms_translation' => [
            'phrase' => [ 'table' => 'cms_phrase' ],
        ],

        'press_lookup' => [
            'productline' => [ 'table' => 'press_productline' ],
            'dimension'   => [ 'table' => 'press_dimension' ],
            'tool'        => [ 'table' => 'press_tool' ],
            'remark'      => [ 'table' => 'press_remark' ],
        ],
        'press_tool' => [
            'manufacturer' => [ 'table' => 'press_manufacturer' ],
        ],
    ];

    protected $hasManyThrough = [
        'cmp_productgroup' => [
            'cms_filter' => [
                'through' => 'cms_productgroup_filter',
                'foreign_key' => 'productgroup',
                'other_key'   => 'filter',
                'active' => true,
                'position' => true,
                'organization' => true,
                'extra' => [
                    'main' => 'boolean',
                ],
            ],
            'cms_filtergroup' => [
                'through' => 'cms_productgroup_filtergroup',
                'foreign_key' => 'productgroup',
                'other_key'   => 'filtergroup',
                'active' => true,
                'position' => true,
                'organization' => true,
                'extra' => [
                    'main' => 'boolean',
                ],
            ],
        ],
        'cms_filter' => [
            'cmp_productgroup' => [
                'through' => 'cms_productgroup_filter',
                'foreign_key' => 'filter',
                'other_key'   => 'productgroup',
                'active' => true,
                'organization' => true,
                'extra' => [
                    'main' => 'boolean',
                ],
            ],
        ],
        'cms_filtergroup' => [
            'cmp_productgroup' => [
                'through' => 'cms_productgroup_filter',
                'foreign_key' => 'filter',
                'other_key'   => 'productgroup',
                'active' => true,
                'organization' => true,
                'extra' => [
                    'main' => 'boolean',
                ],
            ],
        ],
    ];

    // new names for things that cannot be called what they are
    protected $rename = [
        'cms_function'    => 'project_function',
        'cms_function_ml' => 'project_function_translations',   // ?
    ];


    protected function process()
    {
        $this->moveTranslatedToNormalAttributes();
        $this->setCasts();
        $this->setDates();

        $this->setRelations();

        $this->doRenames();
    }

    /**
     * Sometimes attribute duplicates in the translated table
     * should be in the normal table
     */
    protected function moveTranslatedToNormalAttributes()
    {
        foreach ($this->translatedSwaps as $table => $columns) {

            foreach ($columns as $column) {

                $this->context->output['models'][ $table ][ 'translated_fillable' ] = array_diff(
                    $this->context->output['models'][ $table ][ 'translated_fillable' ],
                    [ $column ]
                );
                $this->context->output['models'][ $table ][ 'translated_attributes' ] = array_diff(
                    $this->context->output['models'][ $table ][ 'translated_attributes' ],
                    [ $column ]
                );

                $this->context->output['models'][ $table ][ 'normal_attributes' ][] = $column;
                $this->context->output['models'][ $table ][ 'normal_fillable' ][] = $column;
            }
        }
    }

    protected function setCasts()
    {
        foreach ($this->casts as $table => $columns) {

            foreach ($columns as $column => $cast) {

                $this->context->output['models'][ $table ][ 'casts' ][ $column ] = $cast;
            }
        }
    }

    protected function setDates()
    {
        foreach ($this->dates as $table => $columns) {

            foreach ($columns as $column) {

                $this->context->output['models'][ $table ][ 'dates' ][] = $column;
            }
        }
    }

    protected function doRenames()
    {
        // todo
    }

    protected function setRelations()
    {
        // belongsTo
        // and reverse


        // hasManyThrough (reverse included)

    }
}
