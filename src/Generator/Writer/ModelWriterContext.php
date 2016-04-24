<?php
namespace Aalberts\Generator\Writer;

class ModelWriterContext extends \Czim\PxlCms\Generator\Writer\Model\ModelWriterContext
{

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }

    /**
     * Build Fully Qualified Namespace for a model name
     *
     * @param string      $name
     * @param null|string $prefix
     * @return string
     */
    public function makeFqnForModelName($name, $prefix = null)
    {
        if (null === $prefix) {
            $prefix = $this->data['prefix'];
            if (strtolower($prefix) === 'cmp') {
                $prefix = 'compano';
            }
        }

        return config('pxlcms.generator.namespace.models') . "\\"
            . ($prefix ? studly_case($prefix) . "\\" : null)
            . studly_case($name);
    }

}
