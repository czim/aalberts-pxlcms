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
     * The default config key that returns the default TTL for the cache
     *
     * @var string
     */
    protected $defaultTtlConfigKey = 'aalberts.cache.ttl.cms';

    /**
     * The default cache tags to use
     *
     * @var string[]
     */
    protected $cacheTags = [];


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
     * Note that this does not use cache!
     *
     * @return array
     */
    protected function translatedWithParameters()
    {
        return [
            'translations' => $this->eagerLoadTranslationCallable()
        ];
    }

    /**
     * Returns the default TTL for the cache
     *
     * @return null|int
     */
    protected function defaultTtl()
    {
        return config($this->defaultTtlConfigKey);
    }

    /**
     * Returns the default tags for the cache
     *
     * @return string[]
     */
    protected function cacheTags()
    {
        return $this->cacheTags;
    }


    // ------------------------------------------------------------------------------
    //      With closures
    // ------------------------------------------------------------------------------

    /**
     * @param null|int      $ttl
     * @param null|string[] $cacheTags
     * @return \Closure
     */
    protected function eagerLoadCachedCallable($ttl = null, $cacheTags = null)
    {
        if (null === $cacheTags) $cacheTags = $this->cacheTags();
        if (null === $ttl)       $ttl       = $this->defaultTtl();

        return function ($query) use ($ttl, $cacheTags) {
            /** @var \Illuminate\Database\Eloquent\Builder|\Watson\Rememberable\Query\Builder $query */

            if (null !== $ttl) {
                $query->remember($ttl);

                if (null !== $cacheTags) {
                    $query->cacheTags($cacheTags);
                }
            }

            return $query;
        };
    }


    // ------------------------------------------------------------------------------
    //      Translation
    // ------------------------------------------------------------------------------

    /**
     * @param null          $locale
     * @param null|int      $ttl
     * @param null|string[] $cacheTags
     * @return callable
     */
    protected function eagerLoadTranslationCallable($locale = null, $ttl = null, $cacheTags = null)
    {
        return function ($query) use ($locale, $ttl, $cacheTags) {
            /** @var \Illuminate\Database\Eloquent\Builder|\Watson\Rememberable\Query\Builder $query */

            $query->where('language', $this->languageIdForLocale($locale));

            if (null !== $ttl) {
                $query->remember($ttl);

                if (null !== $cacheTags) {
                    $query->cacheTags($cacheTags);
                }
            }

            return $query;
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
