<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceSimple as PxlCmsStubReplaceSimple;

class StubReplaceSimple extends PxlCmsStubReplaceSimple
{

    protected function process()
    {
        $name = $this->data->name;

        $class = str_replace($this->context->getNamespace($name) . '\\', '', $name);

        $extends = $this->context->getClassNameFromNamespace(config('pxlcms.generator.models.extend_model'));

        $this->determineIfModelIsSluggable();

        $namespace = $this->context->getNamespace( $this->context->fqnName );

        $this->stubReplace('{{MODEL_CLASSNAME}}', studly_case($class))
             ->stubReplace('{{NAMESPACE}}', $namespace)
             ->stubReplace('{{EXTENDS}}', $extends)
             ->stubPregReplace('#\s*{{IMPLEMENTS}}#i', $this->getImplementsReplace())
             ->stubPregReplace('# *{{TABLE}}\n?#i', $this->getTableReplace())
             ->stubPregReplace('# *{{TIMESTAMPS}}\n?#i', $this->getTimestampReplace());
    }

}
