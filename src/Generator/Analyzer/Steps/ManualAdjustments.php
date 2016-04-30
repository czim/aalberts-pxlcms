<?php
namespace Aalberts\Generator\Analyzer\Steps;

use Czim\PxlCms\Generator\Analyzer\Steps\AbstractProcessStep;
use Czim\PxlCms\Generator\Generator;

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
            'product'     => [ 'table' => 'cmp_item' ],
            'successor'   => [ 'table' => 'cmp_item', 'reverse_name' => 'reverseSuccessors' ],
            'predecessor' => [ 'table' => 'cmp_item', 'reverse_name' => 'reversePredecessors' ],
        ],
        'cmp_product' => [
            'successor'   => [ 'table' => 'cmp_product', 'reverse_name' => 'reverseSuccessors' ],
            'predecessor' => [ 'table' => 'cmp_product', 'reverse_name' => 'reversePredecessors' ],
        ],
        'cms_application_image' => [
            'entry' => [ 'table' => 'cmp_applications', 'name' => 'application' ],
        ],
        'cms_approval_image' => [
            'entry' => [ 'table' => 'cmp_approvals', 'name' => 'approval' ],
        ],
        'cms_content' => [
            'parent'   => [ 'table' => 'cms_content', 'reverse_name' => 'children' ],
            'solution' => [ 'table' => 'cmp_solutions', 'skip_reverse' => true ],
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
            'parent' => [ 'table' => 'cms_country', 'reverse_name' => 'children' ],
        ],
        'cms_customblock' => [
            'content' => [ 'table' => 'cms_content', 'skip_reverse' => true ],
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
            'entry' => [ 'table' => 'cmp_solutions', 'name' => 'solution' ],
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


        'cmp_filter_angleofbow'                               => [ 'angleofbow' => ['table' => 'cmp_angleofbow', 'reverse_name' => 'filter', 'reverse_single' => true  ] ],
        'cmp_filter_applications'                             => [ 'applications' => ['table' => 'cmp_applications', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_approvals'                                => [ 'approvals' => ['table' => 'cmp_approvals', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_bowrange'                                 => [ 'bowrange' => ['table' => 'cmp_bowrange', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_colors'                                   => [ 'colors' => ['table' => 'cmp_colors', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_connectiontype'                           => [ 'connectiontype' => ['table' => 'cmp_connectiontype', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_contourcode'                              => [ 'contourcode' => ['table' => 'cmp_contourcode', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_externaltubediameterofconnection'         => [ 'externaltubediameterofconnection' => ['table' => 'cmp_externaltubediameterofconnection', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_finishings'                               => [ 'finishings' => ['table' => 'cmp_finishings', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_manufacturercode'                         => [ 'manufacturercode' => ['table' => 'cmp_manufacturercode', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_materialquality'                          => [ 'materialquality' => ['table' => 'cmp_materialquality', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_materials'                                => [ 'materials' => ['table' => 'cmp_materials', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_maxdischargeflow'                         => [ 'maxdischargeflow' => ['table' => 'cmp_maxdischargeflow', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_maximumoperatingpressureliquid'           => [ 'maximumoperatingpressureliquid' => ['table' => 'cmp_maximumoperatingpressureliquid', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_maxmediumtemp'                            => [ 'maxmediumtemp' => ['table' => 'cmp_maxmediumtemp', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_maxoperatingpressuregas'                  => [ 'maxoperatingpressuregas' => ['table' => 'cmp_maxoperatingpressuregas', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_minmediumtemp'                            => [ 'minmediumtemp' => ['table' => 'cmp_minmediumtemp', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_nominalinternaldiameterofconnection_dn'   => [ 'nominalinternaldiameterofconnection_dn' => ['table' => 'cmp_nominalinternaldiameterofconnection_dn', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_nominalinternaldiameterofconnection_inch' => [ 'nominalinternaldiameterofconnection_inch' => ['table' => 'cmp_nominalinternaldiameterofconnection_inch', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_numberofconnections'                      => [ 'numberofconnections' => ['table' => 'cmp_numberofconnections', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_powerconsumption'                         => [ 'powerconsumption' => ['table' => 'cmp_powerconsumption', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_productgroup'                             => [ 'productgroup' => ['table' => 'cmp_productgroup', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_productline'                              => [ 'productline' => ['table' => 'cmp_productline', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_producttype'                              => [ 'producttype' => ['table' => 'cmp_producttype', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_pumpbrand'                                => [ 'pumpbrand' => ['table' => 'cmp_pumpbrand', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_shape'                                    => [ 'shape' => ['table' => 'cmp_shape', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
        'cmp_filter_solutions'                                => [ 'solutions' => ['table' => 'cmp_solutions', 'reverse_name' => 'filter', 'reverse_single' => true ] ],
    ];

    protected $hasManyThrough = [];

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

        $this->setBelongsToRelations();
        $this->setHasManyThroughRelations();

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

    protected function setBelongsToRelations()
    {
        // belongsTo
        // and reverse
        foreach ($this->belongsTo as $table => $relations) {
            foreach ($relations as $foreignKey => $relationship) {

                $relationship = array_add($relationship, 'name', $foreignKey);

                if (array_has($this->context->output['models'][ $table ]['relationships']['normal'], $relationship['name'])) {
                    throw new \UnexpectedValueException('Duplicate relationship method ' . $relationship['name'] . ' for table ' . $table);
                }

                $this->context->output['models'][ $table ]['relationships']['normal'][ $relationship['name'] ] = [
                    'type'     => Generator::RELATIONSHIP_BELONGS_TO,
                    'model'    => $relationship['table'],
                    'single'   => true,
                    'count'    => 1,
                    'field'    => null,
                    'key'      => $foreignKey,
                    'negative' => false,
                    'special'  => false,
                    'table'    => $relationship['table'],
                ];

                // remove from attributes
                $this->context->output['models'][ $table ]['normal_attributes'] = array_diff(
                    $this->context->output['models'][ $table ]['normal_attributes'],
                    [$foreignKey]
                );
                $this->context->output['models'][ $table ]['normal_fillable']   = array_diff(
                    $this->context->output['models'][ $table ]['normal_attributes'],
                    [$foreignKey]
                );

                if (array_key_exists($foreignKey, $this->context->output['models'][ $table ]['casts'])) {
                    unset($this->context->output['models'][ $table ]['casts'][ $foreignKey ]);
                }


                // handle reverse
                if (array_get($relationship, 'skip_reverse')) {
                    continue;
                }

                $hasOne = array_get($relationship, 'reverse_single', false);

                $defaultReverseName = $hasOne
                    ?   camel_case(str_singular($this->context->output['models'][ $table ]['name']))
                    :   camel_case(str_plural($this->context->output['models'][ $table ]['name']));
                $relationship = array_add($relationship, 'reverse_name', $defaultReverseName);


                if (array_has($this->context->output['models'][ $relationship['table'] ]['relationships']['normal'], $relationship['reverse_name'])) {
                    throw new \UnexpectedValueException('Duplicate reverse relationship method ' . $relationship['reverse_name'] . ' for table ' . $relationship['table'] . ' reversed for table ' . $table);
                }

                $this->context->output['models'][ $relationship['table'] ]['relationships']['normal'][ $relationship['reverse_name'] ] = [
                    'type'     => $hasOne ? Generator::RELATIONSHIP_HAS_ONE : Generator::RELATIONSHIP_HAS_MANY,
                    'model'    => $table,
                    'single'   => $hasOne,
                    'count'    => $hasOne ? 1 : 0,
                    'field'    => null,
                    'key'      => $foreignKey,
                    'negative' => false,
                    'special'  => false,
                    'table'    => $table,
                    'position' => array_get($relationship, 'reverse_position', false),
                ];
            }
        }

    }

    protected function setHasManyThroughRelations()
    {
        // reverse already included

        foreach ($this->hasManyThrough as $table => $relations) {
            foreach ($relations as $otherTable => $relationship) {

                if (array_has($this->context->output['models'][ $table ]['relationships']['normal'], $relationship['name'])) {
                    throw new \UnexpectedValueException('Duplicate relationship method ' . $relationship['name'] . ' for table ' . $table);
                }

                $this->context->output['models'][ $table ]['relationships']['normal'][ $relationship['name'] ] = [
                    'type'         => 'hasManyThrough',
                    'model'        => $otherTable,
                    'single'       => false,
                    'count'        => 0,
                    'field'        => null,
                    'key'          => null,
                    'negative'     => false,
                    'special'      => false,
                    'table'        => $otherTable,
                    'through'      => $relationship['through'],
                ];
            }
        }
    }
}
