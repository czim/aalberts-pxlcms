<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\AbstractProcessStep;

class StubReplacePresenterVariable extends AbstractProcessStep
{

    protected function process()
    {
        $this->stubPregReplace('# *{{PRESENTER}}\n?#i', $this->getPresenterReplace());
    }

    protected function getPresenterReplace()
    {
        if ( ! $this->data->presenter) return null;

        $fqn = rtrim(config('pxlcms.generator.namespace.presenters'), '\\')
             . '\\' . $this->data->presenter;

        $replace = "\n"
                 . $this->tab(1) . "protected \$presenter = "
                 . class_basename($fqn)
                 . "::class;\n";
        
        return $replace;
    }

}
