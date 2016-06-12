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
            CriteriaKey::ORDER => new OrderBy('date', 'desc')
        ]);
    }

    /**
     * Returns news items for index listing.
     * Cached.
     *
     * @param int $count
     * @return mixed
     */
    public function index($count = 10)
    {
        return $this->query()
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
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->take($limit)->get();
    }

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
            'translations'                    => $this->eagerLoadCachedCallable(),
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

}