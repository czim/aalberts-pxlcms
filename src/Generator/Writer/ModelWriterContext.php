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
        }

        return config('pxlcms.generator.namespace.models') . "\\"
            . $this->prefixForClassname($prefix)
            . studly_case($name);
    }

    public function prefixForClassname($prefix)
    {
        $prefix = trim($prefix);

        if (strtolower($prefix) === 'cmp') {
            $prefix = 'compano';
        }

        if ( ! $prefix) {
            return null;
        }

        return studly_case($prefix) . "\\";
    }

}
