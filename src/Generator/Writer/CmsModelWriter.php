<?php
namespace Aalberts\Generator\Writer;

use Czim\PxlCms\Generator\Writer\Model\Steps as CzimSteps;

class CmsModelWriter extends \Czim\PxlCms\Generator\Writer\Model\CmsModelWriter
{

    protected $processContextClass = ModelWriterContext::class;

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
            CzimSteps\CheckConditionsAndSetup::class,

            Steps\StubReplaceSimple::class,
            Steps\StubReplaceAttributeData::class,
            Steps\StubReplaceRelationData::class,
            Steps\StubReplaceAccessorsAndMutators::class,
            CzimSteps\StubReplaceSluggableData::class,
            Steps\StubReplaceScopes::class,
            Steps\StubReplaceDocBlock::class,
            Steps\StubReplaceImportsAndTraits::class,
            Steps\StubReplaceCustomListifyScope::class,
            Steps\StubReplaceCleanListifyScope::class,
            Steps\StubReplacePresenterVariable::class,

            CzimSteps\WriteFile::class,
        ];
    }

}
