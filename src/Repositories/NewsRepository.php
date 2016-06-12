<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\News as NewsModel;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Czim\Repository\Criteria\Common\FieldIsValue;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class NewsRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::NEWS ];

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

        return $this->query()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
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
        
        return $this->where('id', (int) $find)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
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
        return $this->query()
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->whereTranslation('slug', $slug)
            ->first();
    }

    /**
     * Get the most recent X news items.
     * Cached.
     *
     * @param int $limit
     */
    public function recent($limit = 5)
    {
        return $this->query()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->take($limit)->get();
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

        return $this->query()
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
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
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

        return $this->query()
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
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->take(1)
            ->first();
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
            'newsGalleries'                   => $this->eagerLoadCachedCallable(),
            'newsGalleries.newsGalleryImages' => $this->eagerLoadCachedCallable(),
            'translations'                    => $this->eagerLoadCachedTranslationCallable(),
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
            'relatedproducts' => $this->eagerLoadCachedCallable(null  [ CacheTags::PRODUCT ]),
            'contents'        => $this->eagerLoadCachedCallable(null, [ CacheTags::CONTENT ]),
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
