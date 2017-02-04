<?php
namespace Aalberts\Repositories;

use Aalberts\Models\CmsModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Czim\Repository\ExtendedRepository;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Returns a standard repository query, with caching enabled.
     * Applies defaults if not set.
     *
     * @param null $ttl
     * @param null $cacheTags
     * @return Builder|\Watson\Rememberable\Query\Builder
     */
    public function cachedQuery($ttl = null, $cacheTags = null)
    {
        if (null === $ttl)       $ttl       = $this->defaultTtl();
        if (null === $cacheTags) $cacheTags = $this->cacheTags();
        
        return $this->query()
            ->remember($ttl)
            ->cacheTags($cacheTags);
            
    }

    /**
     * @return int
     */
    public function getActiveOrganizationId()
    {
        return config('aalberts.organization');
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
     * Returns eager load callable for typical translations, which defaults to standard cache
     * settings for the repository.
     *
     * @param null          $locale
     * @param null|int      $ttl
     * @param null|string[] $cacheTags
     * @param null|array    $select     if set, the columns to select for the query
     * @return callable
     */
    protected function eagerLoadCachedTranslationCallable($locale = null, $ttl = null, $cacheTags = null, $select = null)
    {
        if (null === $cacheTags) $cacheTags = $this->cacheTags();
        if (null === $ttl)       $ttl       = $this->defaultTtl();
        
        return $this->eagerLoadTranslationCallable($locale, $ttl, $cacheTags, $select);
    }

    /**
     * @param null|string   $locale
     * @param null|int      $ttl
     * @param null|string[] $cacheTags
     * @param null|array    $select     if set, the columns to select for the query
     * @return callable
     */
    protected function eagerLoadTranslationCallable($locale = null, $ttl = null, $cacheTags = null, $select = null)
    {
        return function ($query) use ($locale, $ttl, $cacheTags, $select) {
            /** @var \Illuminate\Database\Eloquent\Builder|\Watson\Rememberable\Query\Builder $query */

            $query->where('language', $this->languageIdForLocale($locale));

            if (is_array($select)) {
                $query->select($select);
            }

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

    // ------------------------------------------------------------------------------
    //      Pagination
    // ------------------------------------------------------------------------------


    /**
     * Returns the total page count, and adjusts given page if out of bounds
     *
     * @param  int $total
     * @param  int $pageSize
     * @param  int $page    by reference, adjusted if necessary
     * @return int      total page count
     */
    protected function calculateTotalPagesWithPageCheck($total, $pageSize, &$page)
    {
        if ($pageSize < 1) {
            return 0;
        }

        $totalPages = (int) ceil($total / $pageSize);

        if ($page < 0 || $totalPages == 0) {
            $page = 0;
        } elseif ($page > $totalPages) {
            $page = $totalPages;
        }

        return $totalPages;
    }

}
