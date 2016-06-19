<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Cms\Project as ProjectModel;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::PROJECT ];
    
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
            //CriteriaKey::ORDER => new OrderBy('position', 'asc')
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
        return $this->cachedQuery()
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

        return $this->cachedQuery()
            ->where('id', (int) $find)
            ->first();
    }

    /**
     * @param string $slug
     * @return null|ProjectModel
     */
    public function findBySlug($slug)
    {
        return $this->cachedQuery()
            ->whereTranslation('slug', $slug)
            ->first();
    }

    /**
     * Get the most recent X project items.
     * Cached.
     *
     * @param int $limit
     * @return array|Collection|ProjectModel[]
     */
    public function recent($limit = 3)
    {
        return $this->cachedQuery()
            ->take($limit)->get();
    }

    /**
     * Returns 'previous' record data given the current record.
     * Cached.
     *
     * @param ProjectModel $project
     * @param null|string  $type
     */
    public function previous(ProjectModel $project, $type = null)
    {
        $this->withoutRelationsOnce()
             ->pushCriteriaOnce(new WithRelations($this->withNextOrPrevious()), CriteriaKey::WITH);

        return $this->cachedQuery()
            ->select(['id', 'label'])
            ->where('id', '!=', $project->id)
            ->where('position', '>', $project->position)
            ->take(1)
            ->first();
    }

    /**
     * Returns 'next' record data given the current record.
     * Cached.
     *
     * @param ProjectModel $project
     * @param null|string  $type
     * @return
     */
    public function next(ProjectModel $project, $type = null)
    {
        $this->reverseDirectionOnce()
             ->pushCriteriaOnce(new WithRelations($this->withNextOrPrevious()), CriteriaKey::WITH);

        return $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->select(['id', 'label'])
            ->where('id', '!=', $project->id)
            ->where('position', '<', $project->position)
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
            'projectGalleries'                      => $this->eagerLoadCachedCallable(),
            'projectGalleries.projectGalleryImages' => $this->eagerLoadCachedCallable(),
            'translations'                          => $this->eagerLoadCachedTranslationCallable(),
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
            new OrderBy([ 'position' => 'desc' ]),
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

}
