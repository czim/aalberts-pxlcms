<?php
namespace Aalberts\Generator\Writer\Steps;

use Aalberts\Generator\Writer\CmsModelWriter;
use Aalberts\Models\CmsModel;

class StubReplaceRelationData extends \Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceRelationData
{

    /**
     * Returns the replacement for the relations config placeholder
     *
     * @return string
     */
    protected function getRelationsConfigReplace()
    {
        return '';
    }

    /**
     * Returns the replacement for the relationships placeholder
     *
     * @return string
     */
    protected function getRelationshipsReplace()
    {
        $relationships = array_merge(
            $this->data['relationships']['normal'],
            $this->data['relationships']['reverse']
        );

        $totalCount = count($relationships);
        

        if ( ! $totalCount) return '';


        $replace = "\n" . $this->tab() . "/*\n"
            . $this->tab() . " * Relationships\n"
            . $this->tab() . " */\n\n";


        /*
         * Normal and Reversed relationships
         */

        foreach ($relationships as $name => $relationship) {

            if ( ! isset($this->data['related_models'][ $relationship['model'] ]['name'])) {
                print_r($this->data->table . ' relation incomplete: ' . $name);
                continue;
            }
            //dd($this->data['related_models']);
            //s($this->data->table);
            //s( $name );
            //s($relationship);
            //s( $this->data['related_models'][ $relationship['model'] ] );

            $relatedClassName = studly_case($this->data['related_models'][ $relationship['model'] ]['name']);

            $prefix = $this->data['related_models'][ $relationship['model'] ]['prefix'];
            if ($prefix && $this->data['prefix'] != $prefix) {
                $prefix = strtolower($prefix);
                if ('cmp' == $prefix) {
                    $prefix = 'compano';
                }

                $relatedClassName = studly_case($prefix) . '\\' . $relatedClassName;

                //$baseNameSpace = config('pxlcms.generator.namespace.models');
                $baseNameSpace = 'AalbertsModels';
                $relatedClassName = $baseNameSpace . '\\' . $relatedClassName;
            }

            $relationParameters = '';

            // parameters for belongsToMany are different
            if ($relationship['type'] === 'belongsToMany') {

                if ($relationTable = array_get($relationship, 'table')) {
                    $relationParameters .= ", '{$relationTable}'";
                }

                if ($relationKey = array_get($relationship, 'key')) {
                    $relationParameters .= ", '{$relationKey}'";
                }

                if ($relationKey = array_get($relationship, 'other_key')) {
                    $relationParameters .= ", '{$relationKey}'";
                }

            } else {
                if ($relationKey = array_get($relationship, 'key')) {
                    $relationParameters .= ", '{$relationKey}'";
                }
            }


            // special stuff for methods with parameters, scope chained etc
            $parameters     = '';
            $chainedMethods = '';
            $returnDirectly = true;
            $conditionals   = '';
            $withPivot      = [];

            $relationTable   = array_get($relationship, 'table');
            $pivotTableScope = $relationTable ? $relationTable . '.' : null;

            $extra = array_get($relationship, 'extra', []);
            if (count($extra)) {
                $withPivot = array_keys($extra);
            }

            // position order
            if (array_get($relationship, 'position')) {
                $parameters .= '$ordered = true';
                $returnDirectly = false;
                $conditionals = $this->tab(2) . "if (\$ordered) {\n"
                              . $this->tab(3) . "\$query->orderBy('{$pivotTableScope}position');\n"
                              . $this->tab(2) . "}\n";
                $withPivot[] = 'position';
            }

            // active scope
            if (array_get($relationship, 'active')) {
                $parameters     .= ($parameters ? ', ' : null) . '$activeOnly = true';
                $returnDirectly  = false;
                $conditionals    = $this->tab(2) . "if (\$activeOnly) {\n"
                                 . $this->tab(3) . "\$query->where('{$pivotTableScope}active', true);\n"
                                 . $this->tab(2) . "}\n";
                $withPivot[] = 'active';
            }

            // organization scope
            if (array_get($relationship, 'organization')) {
                $parameters     .= ($parameters ? ', ' : null) . '$organization = null';
                $chainedMethods .= "\n". $this->tab(3) . "->where('{$pivotTableScope}organization', \$organization ? \$organization : config('aalberts.organization'))";
            }

            // extra fields
            if ($relationship['type'] === 'belongsToMany' && count($withPivot)) {
                $extra = implode(',', array_map(
                    function ($column) { return "'" . $column . "'"; },
                    $withPivot
                ));

                $chainedMethods .= "\n". $this->tab(3) . "->withPivot([{$extra}])";
            }


            $replace .= $this->tab() . "public function {$name}({$parameters})\n"
                . $this->tab() . "{\n"
                . $this->tab(2) . ($returnDirectly ? 'return' : '$query =')
                . " \$this->{$relationship['type']}({$relatedClassName}::class"
                . $relationParameters
                . ")"
                . $chainedMethods
                . ";\n"
                . ($conditionals ? "\n" . $conditionals . "\n" : null)
                . ($returnDirectly ? null : $this->tab(2) . "return \$query;\n")
                . $this->tab() . "}\n"
                . "\n";
        }

        return $replace;
    }
}
