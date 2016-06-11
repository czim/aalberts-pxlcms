<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\News as NewsModel;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class NewsRepository extends AbstractRepository
{
    protected $translated = true;

    /**
     * Returns specified model class name.
     *
     * Note that this is the only method.
     *
     * @return string
     */
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
            CriteriaKey::ORDER => new OrderBy('date', 'desc')
        ]);
    }

    /**
     * Returns news items for index listing.
     * Cached.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->query()
            ->remember(config('aalberts.cache.ttl.cms'))
            ->cacheTags($this->cacheTags())
            ->paginate(10);
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
            ->remember(config('aalberts.cache.ttl.cms'))
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
            ->remember(config('aalberts.cache.ttl.cms'))
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
            ->remember(config('aalberts.cache.ttl.cms'))
            ->cacheTags($this->cacheTags())
            ->take($limit)->get();
    }

    /**
     * Returns standard cache tags relevant for this repository's content
     * 
     * @return array
     */
    protected function cacheTags()
    {
        return [ CacheTags::NEWS ];
    }

    /**
     * Returns with parameter array to use by default
     *
     * @return array
     */
    protected function withBase()
    {
        return [
            'newsGalleries' => function ($query) {
                $query->remember(config('aalberts.cache.ttl.cms'))
                      ->cacheTags($this->cacheTags());
            },
            'newsGalleries.newsGalleryImages' => function ($query) {
                $query->remember(config('aalberts.cache.ttl.cms'))
                      ->cacheTags($this->cacheTags());
            },
            'translations' => function ($query) {
                $query->remember(config('aalberts.cache.ttl.cms'))
                      ->cacheTags($this->cacheTags());
            },
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
            'relatedproducts' => function ($query) {
                $query->remember(config('aalberts.cache.ttl.cms'))
                      ->cacheTags([ CacheTags::PRODUCT ]);
            },
            'contents' => function ($query) {
                $query->remember(config('aalberts.cache.ttl.cms'))
                      ->cacheTags([ CacheTags::CONTENT ]);
            },
        ];
    }

}
