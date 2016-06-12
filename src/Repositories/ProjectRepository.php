<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\Project as ProjectModel;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class ProjectRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::PROJECT ];
    
    public function model()
    {
        return ProjectModel::class;
    }

    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()->merge([
            CriteriaKey::WITH  => new WithRelations($this->withBase()),
            CriteriaKey::ORDER => new OrderBy('position', 'asc')
        ]);
    }

    /**
     * Returns records for index listing.
     * Cached.
     *
     * @param int $count
     * @return mixed
     */
    public function index($count = 6)
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
     * @return ProjectModel
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
     * @param string $slug
     * @return null|ProjectModel
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
     * Get the most recent X project items.
     * Cached.
     *
     * @param int $limit
     */
    public function recent($limit = 3)
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
            'projectGalleries'                      => $this->eagerLoadCachedCallable(),
            'projectGalleries.projectGalleryImages' => $this->eagerLoadCachedCallable(),
            'translations'                          => $this->eagerLoadCachedCallable(),
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
            'contents' => $this->eagerLoadCachedCallable(null, [ CacheTags::CONTENT ]),
        ];
    }

}
