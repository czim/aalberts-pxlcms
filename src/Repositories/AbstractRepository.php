<?php
namespace Aalberts\Repositories;

use Aalberts\Models\CmsModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Czim\Repository\ExtendedRepository;

abstract class AbstractRepository extends ExtendedRepository
{

    /**
     * Whether the repository's model has a translations relation
     *
     * @var bool
     */
    protected $translated = false;


    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        $criteria = parent::defaultCriteria();

        if ($this->translated) {

            $criteria->put(
                CriteriaKey::WITH,
                new WithRelations($this->translatedWithParameters())
            );
        }

        return $criteria;
    }

    /**
     * @return array
     */
    protected function translatedWithParameters()
    {
        return [
            'translations' => $this->eagerLoadTranslationCallable()
        ];
    }


    // ------------------------------------------------------------------------------
    //      Translation
    // ------------------------------------------------------------------------------

    /**
     * @param null $locale
     * @return callable
     */
    protected function eagerLoadTranslationCallable($locale = null)
    {
        return function ($query) use ($locale) {
            return $query->where('language', $this->languageIdForLocale($locale));
        };
    }

    /**
     * @param null|string $locale
     * @return int|null
     */
    protected function languageIdForLocale($locale = null)
    {
        if (null == $locale) $locale = app()->getLocale();

        /** @var CmsModel $model */
        $class = $this->model();
        $model = new $class;

        return $model->lookUpLanguageIdForLocale($locale);
    }

}
