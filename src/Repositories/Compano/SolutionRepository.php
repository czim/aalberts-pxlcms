<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Solution as SolutionModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class SolutionRepository extends AbstractCompanoRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::SOLUTION ];


    public function model()
    {
        return SolutionModel::class;
    }

    /**
     * Returns solutions in use (related to) any projects.
     * Cached.
     *
     * @return mixed
     */
    public function forProjects()
    {
        $this->withBaseOnce();
        
        return $this->cachedQuery(null, [ CacheTag::SOLUTION, CacheTag::PROJECT ])
            ->has('projects')
            ->get();
    }

    /**
     * Cached.
     *
     * @param string $label
     * @return SolutionModel
     */
    public function getByLabel($label)
    {
        $this->withBaseOnce();

        return $this->cachedQuery()
            ->where('label', $label)
            ->first();
    }

    /**
     * Cached.
     *
     * @param string $slug
     * @return SolutionModel
     */
    public function getBySlug($slug)
    {
        $this->withBaseOnce();

        return $this->cachedQuery()
            ->whereTranslation('slug', $slug)
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
            'translations' => $this->eagerLoadCachedTranslationCallable(),
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
        ];
    }

    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    protected function withBaseOnce()
    {
        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

        return $this;
    }

}
