<?php
namespace Aalberts\Generator\Writer;

use Czim\PxlCms\Generator\Exceptions\ModelFileAlreadyExistsException;
use Czim\PxlCms\Generator\Generator;
use Czim\PxlCms\Generator\Writer\Model\WriterModelData;

class ModelWriter extends \Czim\PxlCms\Generator\ModelWriter
{

    /**
     * Writes output files based on the set data
     *
     * @return bool
     */
    public function writeFiles()
    {
        return parent::writeFiles();
    }

    protected function writeModels()
    {
        // find out which modules already written, do not overwrite them
        // write new models
        // warn about models not written or refernced but not updated on the other end

        /** @var CmsModelWriter $modelWriter */

        $totalToWrite             = count($this->data['models']);
        $countWritten             = 0;
        $countTranslationsWritten = 0;
        $countAlreadyExist        = 0;

        foreach ($this->data['models'] as $model) {

            // for tracking whether translation model has a written main model
            $wroteMainModel = false;

            try {
                $model = $this->appendRelatedModelsToModelData($model);

                $modelWriter = app( CmsModelWriter::class );
                $modelWriter->process( app(WriterModelData::class, [ $model ]) );

                $this->log("Wrote model {$model['name']}.");
                $countWritten++;
                $wroteMainModel = true;

            } catch (ModelFileAlreadyExistsException $e) {

                $this->log("File for model {$model['name']} already exists, did not write.");
                $countAlreadyExist++;
            }

            // also write translation?
            if ($model['is_translated']) {

                $translatedModel = $this->makeTranslatedDataFromModelData($model);

                try {

                    $modelWriter = app( CmsModelWriter::class );
                    $modelWriter->process( app(WriterModelData::class, [ $translatedModel ]) );

                    $this->log("Wrote translation for model {$model['name']}.");
                    $countTranslationsWritten++;

                    if ( ! $wroteMainModel) {
                        $this->log(
                            "Warning: translation for model {$model['name']} was written, but model itself was not (over)written.\n"
                            . "Delete the old model and try again, or check the translation setup of the model manually.",
                            Generator::LOG_LEVEL_ERROR
                        );
                    }

                } catch (ModelFileAlreadyExistsException $e) {

                    $this->log("File for translation of model {$model['name']} already exists, did not write.");
                    $countAlreadyExist++;
                }
            }
        }

        $this->log(
            "Models written: {$countWritten} of {$totalToWrite}"
            . ($countTranslationsWritten ? " (and {$countTranslationsWritten} translation model"
                . ($countTranslationsWritten != 1 ? 's' : '') . ")" : null),
            Generator::LOG_LEVEL_INFO
        );

        if ($countAlreadyExist) {
            $this->log(
                "{$countAlreadyExist} model" . ($countAlreadyExist != 1 ? 's' : '') . " already had files and "
                . ($countAlreadyExist == 1 ? 'was' : 'were') . " not (over)written",
                Generator::LOG_LEVEL_WARNING
            );
        }
    }

    /**
     * Make model data array for translation model
     *
     * @param array $model
     * @return array
     */
    protected function makeTranslatedDataFromModelData(array $model)
    {
        $array = parent::makeTranslatedDataFromModelData($model);

        $array['prefix'] = $model['prefix'];

        return $array;
    }

}
