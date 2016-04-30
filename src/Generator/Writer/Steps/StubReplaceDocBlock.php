<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\CmsModelWriter;
use Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceDocBlock as PxlCmsStubReplaceDocBlock;

class StubReplaceDocBlock extends PxlCmsStubReplaceDocBlock
{

    /**
     * @return array
     */
    protected function collectIdeHelperRelationRows()
    {
        if ( ! config('pxlcms.generator.models.ide_helper.tag_relationship_magic_properties')) {
            return [];
        }

        // we can trust these, because they were normalized in the relationsdata step
        $relationships = array_merge(
            $this->data['relationships']['normal'],
            $this->data['relationships']['reverse']
        );

        $rows = [];

        foreach ($relationships as $relationName => $relationship) {

            $specialType = (int) array_get($relationship, 'special');
            if ( ! $specialType && ! isset($this->data['related_models'][ $relationship['model'] ]['name'])) continue;

            // special relationships have defined model names, otherwise use the relation model name
            if ($specialType)  {
                $relatedClassName = $this->context->getModelNamespaceForSpecialModel($specialType);
            } else {
                $relatedClassName = studly_case( $this->data['related_models'][ $relationship['model'] ]['name'] );

                $prefix = $this->data['related_models'][ $relationship['model'] ]['prefix'];
                if ($this->data['prefix'] != $prefix) {
                    $relatedClassName = 'AalbertsModels\\'
                        . $this->context->prefixForClassname($prefix)
                        . $relatedClassName;
                }
            }


            // single relationships returns a single of the related model type
            // multiples return collections/arrays with related model type entries
            if ($relationship['count'] == 1) {
                $type = $relatedClassName;
            } else {
                $type = CmsModelWriter::FQN_FOR_COLLECTION . '|' . $relatedClassName . '[]';
            }

            // the read-only magic property for the relation
            $rows[] = [
                'tag'  => 'property-read',
                'type' => $type,
                'name' => '$' . $relationName,
            ];
        }

        return $rows;
    }
    
    
    /**
     * Returns whether we're using a global scope for active
     *
     * @return bool
     */
    protected function useScopeActive()
    {
        if (is_null($this->data['scope_active'])) {
            return config('pxlcms.generator.models.scopes.only_active') === CmsModelWriter::SCOPE_METHOD;
        }

        return false;
    }

    /**
     * Returns whether we're using a global scope for position
     *
     * @return bool
     */
    protected function useScopePosition()
    {
        if (is_null($this->data['scope_position'])) {
            return config('pxlcms.generator.models.scopes.position_order') === CmsModelWriter::SCOPE_METHOD;
        }

        return false;
    }
}
