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
            $this->collectOrganizationTrait(),
            $this->collectOrganizationCodeTrait(),
            $this->collectPresenterTrait()
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
     * @return array
     */
    protected function collectOrganizationCodeTrait()
    {
        // organization
        if ( ! $this->data['restrict_salesorganizationcode']) return [];

        return [ 'ForOrganizationCode' ];
    }

    /**
     * @return array
     */
    protected function collectPresenterTrait()
    {
        if ( ! $this->data['presenter']) return [];

        return [ 'PresentableTrait' ];
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

        if ($this->data['restrict_salesorganizationcode']) {
            $lines[] = 'Aalberts\\Models\\Scopes\\ForOrganizationCode';
        }


        if ($this->data['presenter']) {
            $lines[] = config('pxlcms.generator.models.traits.presentable_fqn');
            $lines[] = rtrim(config('pxlcms.generator.namespace.presenters'), '\\')
                     . '\\' . $this->data['presenter'];
        }

        return $lines;
    }

}
