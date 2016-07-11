<?php
namespace Aalberts\Generator;

use Czim\PxlCms\Generator\CmsAnalyzer;
use Czim\PxlCms\Generator\Analyzer\Steps;

class Analyzer extends CmsAnalyzer
{
    
    /**
     * Gathers the steps to pass the dataobject through as a collection
     * These are the steps for AFTER the initial checks and retrieval
     * has been handled.
     *
     * @return array
     */
    protected function processSteps()
    {
        return [
            Analyzer\Steps\CheckTables::class,
            Analyzer\Steps\ParseTableColumns::class,
            Analyzer\Steps\ManualAdjustments::class,
            Analyzer\Steps\CustomAdditions::class,
        ];
    }
    
}
