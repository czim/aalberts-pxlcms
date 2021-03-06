<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Cms\News as NewsModel;
use App\Models\Aalberts\Cms\News;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Czim\Repository\Criteria\Common\FieldIsValue;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::NEWS ];

    public function model()
    {
        return NewsModel::class;
    }

    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()->merge([
            CriteriaKey::WITH  => new WithRelations($this->withBase()),
            CriteriaKey::ORDER => new OrderBy(['date' => 'desc', 'createdts' => 'desc' ])
        ]);
    }

    /**
     * Returns news items for index listing.
     * Cached.
     *
     * @param int         $count
     * @param null|string $type
     * @return mixed
     */
    public function index($count = 10, $type = null)
    {
        $this->forTypeOnce($type);

        return $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->paginate($count);
    }

    /**
     * Returns news item for detail page.
     * Cached.
     *
     * @param string|int $find      ID or slug of the record
     * @param bool       $bySlug    if true, expects $find to be a slug, otherwise an ID
     * @return NewsModel
     */
    public function detail($find, $bySlug = true)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        if ($bySlug) {
            return $this->findBySlug($find);
        }
        
        return $this->cachedQuery()
            ->where('id', (int) $find)
            ->first();
    }

    /**
     * Finds a record by its translated slug.
     * Cached.
     *
     * @param string $slug
     * @return null|NewsModel
     */
    public function findBySlug($slug)
    {
        return $this->cachedQuery()
            ->whereTranslation('slug', $slug)
            ->first();
    }

    /**
     * Get the most recent X news items.
     * Cached.
     *
     * @param int   $limit
     * @param int[] $excludeIds
     * @return Collection|News[]
     */
    public function recent($limit = 5, $excludeIds = [])
    {
        $query = $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class);
        
        if ( ! empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }
        
        return $query->take($limit)->get();
    }
    
    /**
     * Returns 'previous' record data given the current record.
     * Cached.
     *
     * @param NewsModel   $news
     * @param null|string $type
     */
    public function previous(NewsModel $news, $type = null)
    {
        $this->withoutRelationsOnce()
             ->pushCriteriaOnce(new WithRelations($this->withNextOrPrevious()), CriteriaKey::WITH)
             ->forTypeOnce($type);

        return $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->select(['id', 'label'])
            ->where('id', '!=', $news->id)
            ->where(function ($query) use ($news) {
                $query->where('date', '<', $news->date->timestamp)
                    ->orWhere(function ($query) use ($news) {
                        $query->where('date', '=', $news->date->timestamp)
                            ->where('createdts', '<=', $news->createdts);
                    });
            })
            ->take(1)
            ->first();
    }

    /**
     * Returns 'next' record data given the current record.
     * Cached.
     *
     * @param NewsModel   $news
     * @param null|string $type
     * @return
     */
    public function next(NewsModel $news, $type = null)
    {
        $this->reverseDirectionOnce()
             ->pushCriteriaOnce(new WithRelations($this->withNextOrPrevious()), CriteriaKey::WITH)
             ->forTypeOnce($type);

        return $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->select(['id', 'label'])
            ->where('id', '!=', $news->id)
            ->where(function ($query) use ($news) {
                $query->where('date', '>', $news->date->timestamp)
                    ->orWhere(function ($query) use ($news) {
                        $query->where('date', '=', $news->date->timestamp)
                            ->where('createdts', '>=', $news->createdts);
                    });
            })
            ->take(1)
            ->first();
    }

    /**
     * @param string   $term
     * @param null|int $count   limit results
     * @return Collection|NewsModel
     */
    public function search($term, $count = null)
    {
        $query = $this->query()
            ->whereHas('translations', function($query) use ($term) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('language', $this->languageIdForLocale())
                    ->where(function($query) use ($term) {
                        /** @var \Illuminate\Database\Eloquent\Builder $query */
                        $query->where('title', 'like', '%' . $term . '%')
                              ->orWhere('content', 'like', '%' . $term . '%');
                    });
            });

        if (null !== $count) {
            $query->take($count);
        }

        return $query->get();
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
            'translations'                    => $this->eagerLoadCachedTranslationCallable(),
            'newsGalleries'                   => $this->eagerLoadCachedCallable(),
            'newsGalleries.translations'      => $this->eagerLoadCachedCallable(),
            'newsGalleries.newsGalleryImages' => $this->eagerLoadCachedCallable(),
        ];
    }

    /**
     * Returns with parameter array to use for detail page
     *
     * @return array
     */
    protected function withDetail()
    {
        return [
            'relatedproducts'                      => $this->eagerLoadCachedCallable(null  [ CacheTag::CMP_PRODUCT ]),
            'relatedproducts.relatedproductImages' => $this->eagerLoadCachedCallable(null  [ CacheTag::CMP_PRODUCT ]),

            'contents' => $this->eagerLoadCachedCallable(null, [ CacheTag::CONTENT ]),
        ];
    }

    /**
     * Returns with parameter array to use for next/previous lookup
     * 
     * @return array
     */
    protected function withNextOrPrevious()
    {
        return [
            'translations' => $this->eagerLoadCachedTranslationCallable(
                null, null, null,
                [ 'id', 'entry', 'language', 'slug', 'title' ]
            ),
        ];
    }


    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------

    /**
     * Applies criteria once to reverse the standard sorting order
     * 
     * @return $this
     */
    protected function reverseDirectionOnce()
    {
        $this->pushCriteriaOnce(
            new OrderBy([ 'date' => 'asc', 'createdts' => 'asc' ]),
            CriteriaKey::ORDER
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function withoutRelationsOnce()
    {
        $this->removeCriteriaOnce(CriteriaKey::WITH);

        return $this;
    }

    /**
     * @param null|string $type
     * @return $this
     */
    protected function forTypeOnce($type)
    {
        if ($type) {
            $this->pushCriteriaOnce(new FieldIsValue('type', $type));
        }

        return $this;
    }

}
