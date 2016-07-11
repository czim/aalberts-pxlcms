<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Productgroup as ProductGroupModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class ProductGroupRepository extends AbstractCompanoRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_PRODUCT ];


    public function model()
    {
        return ProductGroupModel::class;
    }

    /**
     * Cached.
     *
     * @param string $label
     * @return ProductGroupModel
     */
    public function getByLabel($label)
    {
        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->where('label', $label)
            ->first();
    }

    /**
     * Cached.
     *
     * @param string $slug
     * @return ProductGroupModel
     */
    public function getBySlug($slug)
    {
        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

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

}
