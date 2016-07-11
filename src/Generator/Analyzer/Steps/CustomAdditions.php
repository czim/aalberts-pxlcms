<?php
namespace Aalberts\Generator\Analyzer\Steps;

use Czim\PxlCms\Generator\Analyzer\Steps\AbstractProcessStep;
use Czim\PxlCms\Generator\Generator;

/**
 * Class CustomAdditions
 *
 * Sets custom code to be added to the end of files as-is
 */
class CustomAdditions extends AbstractProcessStep
{

    protected function process()
    {
        $this->setCustomForProduct();
    }

    protected function setCustomForProduct()
    {
        $this->context->output['models']['cmp_product']['custom'] =
            "\t" . "public function productgroup()\n"
            . "\t" . "{\n"
            . "\t\t" . "/** @var \\Aalberts\\Repositories\\Compano\\ProductGroupRepository \$repository */\n"
            . "\t\t" . "\$repository = app(\Aalberts\Repositories\Compano\ProductGroupRepository::class);\n"
            . "\t\t" . "return \$repository->getByLabel(\$this->productproductgroup);\n"
            . "\t" . "}\n";
    }
}
