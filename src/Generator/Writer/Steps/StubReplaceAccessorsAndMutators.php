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
            $this->collectCompanoFilterAccessors()
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

            $accessors['products'] = [
                'parameters' => ['$value'],
                'content'    => $content,
            ];
        }

        return $accessors;
    }

}
