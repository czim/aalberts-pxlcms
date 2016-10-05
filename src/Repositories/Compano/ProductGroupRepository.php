<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Productgroup as ProductGroupModel;
use Czim\Repository\Criteria\Common\WhereHas;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class ProductGroupRepository extends AbstractCompanoRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_PRODUCT ];

    /**
     * @var bool
     */
    protected $filterByOrganizationCode = true;


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
        $this->restrictForOrganizationOnce();

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
        $this->restrictForOrganizationOnce();

        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->whereTranslation('slug', $slug)
            ->first();
    }

    /**
     * Returns product groups for index listing.
     * Cached.
     *
     * @param int|null $count
     * @return mixed
     */
    public function index($count = null)
    {
        $this->restrictForOrganizationOnce();

        $query = $this->cachedQuery();

        if (null !== $count) {
            return $query->paginate($count);
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
     * Restricts the results to what has been enabled, and in the order set, in the CMS.
     *
     * @return $this
     */
    protected function restrictForOrganizationOnce()
    {
        if ( ! $this->filterByOrganizationCode) return $this;

        $this->pushCriteriaOnce(
            new WhereHas('productgroups', $this->eagerLoadCachedCallable([ CacheTag::PRODUCTGROUP ]))
        );

        return $this;
    }

}
