<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\CmsModelWriter;

class StubReplaceScopes extends \Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceScopes
{

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
