<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceImportsAndTraits as PxlCmsStubReplaceImportsAndTraits;

class StubReplaceImportsAndTraits extends PxlCmsStubReplaceImportsAndTraits
{

    /**
     * @inheritdoc
     */
    protected function collectTraits()
    {
        return array_merge(
            parent::collectTraits(),
            $this->collectOrganizationTrait()
        );
    }

    /**
     * @return array
     */
    protected function collectOrganizationTrait()
    {
        // organization
        if ( ! $this->data['has_organization']) return [];

        return [ 'ForOrganization' ];
    }


    /**
     * @inheritdoc
     */
    protected function collectImportLines()
    {
        return array_merge(
            parent::collectImportLines(),
            $this->collectAalbertsImportLines()
        );
    }

    /**
     * @return array
     */
    protected function collectAalbertsImportLines()
    {
        $lines = [];

        $lines[] = config('pxlcms.generator.namespace.models') . ' as AalbertsModels';

        if ($this->data['has_organization']) {
            $lines[] = 'Aalberts\\Models\\Scopes\\ForOrganization';
        }

        return $lines;
    }

}
