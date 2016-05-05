<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\AbstractProcessStep;

class StubReplaceCustomListifyScope extends AbstractProcessStep
{

    protected function process()
    {
        $this->stubPregReplace('# *{{LISTIFYCUSTOMSCOPE}}\n?#i', $this->getListifyReplace());
    }

    protected function getListifyReplace()
    {
        // build method like this:

        //protected function getCmsListifyDefaultScope()
        //{
        //    return '`' . $this->getTable() . '`.``parent` = ' . (int) $this->parent
        //    . ' and '
        //    . '`' . $this->getTable() . '`.`organization` = ' . (int) $this->organization;
        //}

        $listifyLines = $this->data->listify_scope ?: [];

        if ( ! count($listifyLines)) return '';

        $replace = $this->tab(1) . "protected function getCmsListifyDefaultScope()\n"
                 . $this->tab(1) . "{\n";

        foreach ($listifyLines as $line) {
            $replace .= $this->tab(2) . $line . "\n";
        }

        $replace .= $this->tab(1) . "}\n";
        
        return $replace;
    }

}
