<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use Aalberts\Repositories\AbstractRepository;
use App\Models\Aalberts\Compano\Supplier as SupplierModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class SupplierRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_SUPPLIER ];

    public function model()
    {
        return SupplierModel::class;
    }

    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()->merge([
            CriteriaKey::WITH => new WithRelations($this->withBase()),
        ]);
    }


    /**
     * Looks up a content entry by its translated slug.
     * Cached.
     *
     * @param string      $slug
     * @return null|SupplierModel
     */
    public function findBySlug($slug)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->where('slug',  $slug)
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
            'countries' => $this->eagerLoadCachedCallable(null, [ CacheTag::COUNTRY ]),
            'downloads' => $this->eagerLoadCachedCallable(null, [ CacheTag::DOWNLOAD ]),
        ];
    }

}
