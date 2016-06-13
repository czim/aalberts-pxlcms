<?php
namespace Aalberts\Generator\Analyzer\Steps;

use Aalberts\Generator\Analyzer\AnalyzerContext;
use Czim\Processor\Steps\AbstractProcessStep;
use Czim\PxlCms\Generator\Generator;

class ParseTableColumns extends AbstractProcessStep
{
    /**
     * @var AnalyzerContext
     */
    protected $context;


    protected function process()
    {
        $this->data['tableData'] = [];

        $parsedData = [];

        // analyze the string content for each column and parse it to
        // a more useful dataset for building models from

        $index = 0;

        foreach ($this->context->tableColumns as $table => $columns) {

            $index++;

            // skip some tables that we don't care about
            if (    $table == 'sessions'
                ||  substr($table, 0, 4) == 'acl_'
                ||  substr($table, -3) == '_ml'
                // ||  substr($table, 0, 11) == 'cmp_filter_'
                ||  $table == 'cms_roles'
                ||  $table == 'cms_sor'
                ||  $table == 'cms_log_search'
                ||  $table == 'cms_module'
                ||  $table == 'cms_operation'
                ||  $table == 'cms_permission'
                ||  $table == 'cms_user'
                ||  $table == 'cms_user_module'
                ||  $table == 'cms_user_organization'
                ||  $table == 'pdf_generate_catalog'

            ) {
                continue;
            }

            $name   = $table;
            $prefix = null;

            if (preg_match('#^cmp_filter_#', $table)) {

                $prefix = 'filter';
                $name = str_replace('cmp_filter_', '', $table);
                $name = $this->sanitizeName($name);

            } else {

                if (false !== ($pos = strpos($table, '_'))) {
                    $prefix = substr($table, 0, $pos);
                    $name   = substr($table, $pos + 1);

                    $name = $this->sanitizeName($name);
                }
            }


            $tableData = [
                'prefix'                => $prefix,
                'name_without_prefix'   => $name,
                'module'                => $index,
                'name'                  => snake_case($name, ' '),
                'table'                 => $table,
                'cached'                => true,
                'is_translated'         => false,
                'is_translation'        => false,
                'is_listified'          => false,
                'ordered_by'            => [],
                'timestamps'            => null,
                'timestamp_onlycreated' => false,
                'normal_fillable'       => [],
                'translated_fillable'   => [],
                'hidden'                => [],
                'casts'                 => [],
                'dates'                 => [],
                'defaults'              => [],
                'normal_attributes'     => [],
                'translated_attributes' => [],
                'has_categories'        => false,
                'has_organization'      => false,
                'relationships'         => [
                    'normal'   => [],
                    'reverse'  => [],
                    'image'    => [],
                    'file'     => [],
                    'checkbox' => [],
                ],
                'sluggable'             => false,
                'sluggable_setup'       => [],
                'scope_active'          => null,
                'scope_position'        => null,
                'filter_products_column' => ($prefix == 'filter'),
                'presenter'             => null,
            ];

            // translated?
            if (array_key_exists($table . '_ml', $this->context->tableColumns)) {
                $tableData['is_translated'] = true;

                // get the columns from the translated table
                $translatedColumns = $this->getColumnsFromTable($table . '_ml');

                // remove typical translation column overhead
                $translatedColumns['columns'] = array_diff($translatedColumns['columns'], ['entry', 'language']);

                $tableData['translated_attributes'] = $translatedColumns['columns'];
                $tableData['translated_fillable']   = $translatedColumns['columns'];
            }

            $parsedColumns = [];
            $timestampColumnCount = 0;

            foreach ($columns as $column) {
                $name = array_get($column, 'Field');
                if ($name === 'id') continue;

                $parsedColumns[ $name ] = $this->translateColumnData($column);

                if ($name === 'createdts' || $name === 'modifiedts') {
                    $timestampColumnCount++;
                }

                if ($name === 'position') {
                    $tableData['is_listified'] = true;
                }

                if ($name == 'organization' && $parsedColumns[ $name ]['type'] == 'integer') {
                    $tableData['has_organization'] = true;
                }
            }

            // timestamps only if both columns present
            if ($timestampColumnCount > 1) {
                $tableData['timestamps'] = true;
            } elseif ($timestampColumnCount == 1) {
                $tableData['timestamp_onlycreated'] = true;
            }

            foreach ($parsedColumns as $name => $column) {

                if ($column['ignore']) {
                    $tableData['hidden'][] = $name;
                }

                // sometimes translations have the same name as the main table
                if (    in_array($name, $tableData['translated_attributes'])
                    ||  $name === 'createdts' || $name === 'modifiedts'
                ) {
                    continue;
                }

                if ($column['cast']) {
                    $tableData['casts'][ $name ] = $column['cast'];
                }

                if ($column['cast'] === 'date') {
                    $tableData['dates'][] = $name;
                }

                $tableData['normal_attributes'][] = $name;
                $tableData['normal_fillable'][] = $name;
            }

            // if dates are only the timestamps, don't set them
            if (    count($tableData['dates']) == 2
                &&  in_array('createdts', $tableData['dates'])
                &&  in_array('modifiedts', $tableData['dates'])
            ) {
                $tableData['dates'] = [];
            }

            // scopes
            $tableData['scope_active']   = in_array('active', $tableData['normal_attributes']) ? 'global' : false;
            $tableData['scope_position'] = in_array('position', $tableData['normal_attributes']) ? 'global' : false;

            $parsedData[$table] = $tableData;
        }

        $this->context->output['models'] = $parsedData;

        $this->processPivotTables();
    }


    protected function getColumnsFromTable($table)
    {
        $columns = $this->context->tableColumns[ $table ];

        $return = [
            'columns' => [],
            'hide'    => [],
        ];

        foreach ($columns as $column) {
            $column = $this->translateColumnData($column);
            if ($column['name'] === 'id') continue;
            if ($column['ignore']) continue;

            $return['columns'][] = $column['name'];
        }

        return $return;
    }


    protected function translateColumnData(array $column)
    {
        $name = strtolower(array_get($column, 'Field'));

        $data = [
            'name'           => $name,
            'ignore'         => false,
            'type'           => 'string',
            'unix_timestamp' => false,
            'cast'           => null,
            'hide'           => false,
            'related_table'  => null,
            'relation_type'  => null,
        ];

        $type = strtolower(array_get($column, 'Type'));

        if ($type === 'timestamp') {
            $data['type'] = 'date';
            $data['cast'] = 'date';

            //if ($name == 'createdts' || $name == 'modifiedts') {
            //    $data['ignore'] = true;
            //}

        } elseif (preg_match('#tinyint\(1\)#', $type)) {
            $data['type'] = 'integer';
            $data['cast'] = 'boolean';

        } elseif (preg_match('#int\(\d+\)#', $type)) {
            $data['type'] = 'integer';
            $data['cast'] = 'integer';

            if ($name == 'organization') {
                $data['ignore'] = true;
            }

        } elseif (preg_match('#double|float#', $type)) {
            $data['type'] = 'float';
            $data['cast'] = 'float';

        } elseif (preg_match('#text|varchar#', $type)) {
            $data['type'] = 'string';

        } elseif (preg_match('#enum#', $type)) {
            $data['type'] = 'string';

        } else {
            throw new \Exception('unknown type ' . $type);
        }

        return $data;
    }


    protected function processPivotTables()
    {
        $knownPivots = config('pxlcms.aalberts_pivots');

        foreach ($this->context->output['models'] as $key => $data) {
            if ( ! array_key_exists($key, $knownPivots)) continue;

            $pivot = $knownPivots[ $key ];

            $foreignKeyA = array_keys($pivot)[0];
            $otherTableA = $pivot[ $foreignKeyA ];
            $foreignKeyB = array_keys($pivot)[1];
            $otherTableB = $pivot[ $foreignKeyB ];

            // relation 1
            $relationName = $this->normalizeBelongsToManyMethodName($foreignKeyA);
            $relation = $this->makeRelationArrayForPivotData($otherTableA, $foreignKeyB, $foreignKeyA, $pivot, $key);
            $this->context->output['models'][ $otherTableB ]['relationships']['normal'][ $relationName ] = $relation;

            // relation 2
            $relationName = $this->normalizeBelongsToManyMethodName($foreignKeyB);
            $relation = $this->makeRelationArrayForPivotData($otherTableB, $foreignKeyA, $foreignKeyB, $pivot, $key);
            $this->context->output['models'][ $otherTableA ]['relationships']['normal'][ $relationName ] = $relation;
        }

        foreach (array_keys($knownPivots) as $key) {
            unset($this->context->output['models'][ $key ]);
            unset($knownPivots[ $key ]);
        }
    }

    protected function normalizeBelongsToManyMethodName($name)
    {
        $name = str_plural($name);
        $name = camel_case($name);

        return $name;
    }

    /**
     * @param string $foreignTable
     * @param string $foreignKey
     * @param string $otherKey
     * @param array  $pivot
     * @param null   $pivotTable
     * @return array
     */
    protected function makeRelationArrayForPivotData($foreignTable, $foreignKey, $otherKey, array $pivot, $pivotTable = null)
    {
        return [
            'type'         => Generator::RELATIONSHIP_BELONGS_TO_MANY,
            'model'        => $foreignTable,
            'single'       => false,
            'count'        => 0,
            'field'        => null,
            'key'          => $foreignKey,
            'other_key'    => $otherKey,
            'negative'     => false,
            'special'      => false,
            'extra'        => array_get($pivot, 'extra', []),
            'organization' => (array_get($pivot, 'organization') === true),
            'position'     => array_get($pivot, 'position', false),
            'active'       => array_get($pivot, 'active', false),
            'table'        => $pivotTable,
        ];
    }

    protected function sanitizeName($name)
    {
        if ($name == 'function') {
            return 'project_function';
        }

        $name = str_singular($name);

        return $name;
    }

}
