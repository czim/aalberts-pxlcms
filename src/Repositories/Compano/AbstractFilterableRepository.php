<?php
namespace Aalberts\Repositories\Compano;

use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractFilterableRepository extends AbstractCompanoRepository
{
    /**
     * Whether the model is translated.
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
            $criteria = $criteria->merge([
                CriteriaKey::WITH => new WithRelations($this->withBase()),
            ]);
        }

        return $criteria;
    }

    /**
     * Returns list of display labels for filters.
     *
     * @return string[]
     */
    public function getFilterDisplayLabels()
    {
        $results = $this->cachedQuery()->get();

        $attribute = $this->getFilterDisplayAttribute();

        return $results
            ->keyBy('id')
            ->transform(function (Model $model) use ($attribute) {
                return $model->{$attribute};
            })
            ->toArray();
    }

    /**
     * @return string
     */
    protected function getFilterDisplayAttribute()
    {
        return 'label';
    }


    // ------------------------------------------------------------------------------
    //      With Relations
    // ------------------------------------------------------------------------------

    /**
     * Returns with parameter array to use by default
     *
     * @return array
     */
    protected function withBase()
    {
        return [
            'translations' => $this->eagerLoadCachedTranslationCallable(),
        ];
    }

}
