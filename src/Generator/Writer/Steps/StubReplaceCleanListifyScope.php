<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\AbstractProcessStep;

class StubReplaceCleanListifyScope extends AbstractProcessStep
{

    protected function process()
    {
        $this->stubPregReplace('# *{{LISTIFYCLEANSCOPE}}\n?#i', $this->getListifyReplace());
    }

    protected function getListifyReplace()
    {
        // if model is not listified, nothing to do
        // if model has no global scopes, nothing to do
        if (    ! $this->data->is_listified
            ||  (   ! $this->data->scope_position
                &&  ! $this->data->scope_active
                // don't bother with the organization, since scopes will never transcend organization!
                //&&  ! $this->data->has_organization
                )
        ) {
            return '';
        }

        // get global scopes and determine 'reversal' method
        // create line that returns $query with applied, chained reversal methods
        $chainedMethods = $this->getChainedMethods();

        if ( ! count($chainedMethods)) return '';

        $replace = $this->tab(1) . "protected function cleanListifyScopedQuery(\$query)\n"
                 . $this->tab(1) . "{\n"
                 . $this->tab(2) . "return \$query->"
                 . implode('->', array_map(function ($method) { return $method . '()'; }, $chainedMethods))
                 . ";\n"
                 . $this->tab(1) . "}\n";
        
        return $replace;
    }

    /**
     * @return array
     */
    protected function getChainedMethods()
    {
        $methods = [];

        //if ($this->data->has_organization) {
        //    $methods[] = 'forAnyOrganization';
        //}

        if ($this->data->scope_position) {
            $methods[] = 'unordered';
        }

        if ($this->data->scope_active) {
            $methods[] = 'withInactive';
        }

        return $methods;
    }

}
