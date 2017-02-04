<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceAccessorsAndMutators as PxlCmsStubReplaceAccessorsAndMutators;

class StubReplaceAccessorsAndMutators extends PxlCmsStubReplaceAccessorsAndMutators
{
    
    /**
     * @return array    name => array with properties
     */
    protected function collectAccessors()
    {
        return array_merge(
            parent::collectAccessors(),
            $this->collectCompanoFilterAccessors(),
            $this->collectSpecialDateMutators()
        );
    }


    /**
     * For Compano filters with 'products' fields
     *
     * @return array    name => array with properties
     */
    protected function collectCompanoFilterAccessors()
    {
        $accessors = [];

        if ($this->data['filter_products_column']) {
            
            $content = $this->tab(2)
                     . "return collect( empty(\$value) ? [] : explode(',', \$value) );\n";

            $accessors['productsArray'] = [
                'parameters' => ['$value'],
                'content'    => $content,
            ];
        }

        return $accessors;
    }


    protected function collectSpecialDateMutators()
    {
        $mutators = [];

        foreach ($this->data['casts'] as $column => $type) {
            if ($type !== 'date_timestamp') continue;

            $content = $this->tab(2)
                     . "\$this->attributes['{$column}'] = \$value ? strtotime(\$value) : null;\n\n"
                     . $this->tab(2)
                     . "return \$this;\n";

            $mutators[ $column ] = [
                'parameters' => ['$value'],
                'content'    => $content,
                'mutator'    => true,
            ];
        }

        return $mutators;
    }

}
