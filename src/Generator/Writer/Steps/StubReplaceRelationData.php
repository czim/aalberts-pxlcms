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

            // parameters for belongsToMany are different


            $relationParameters = '';

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

            $replace .= $this->tab() . "public function {$name}()\n"
                . $this->tab() . "{\n"
                . $this->tab(2) . "return \$this->{$relationship['type']}({$relatedClassName}::class"
                . $relationParameters
                . ");\n"
                . $this->tab() . "}\n"
                . "\n";
        }

        return $replace;
    }
}
