<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use Aalberts\Filters\ParameterCounters\Product\ProductgroupCounter;
use Aalberts\Filters\ProductFilter;
use Aalberts\Filters\ProductFilterData;
use App\Models\Aalberts\Compano\Product;
use App\Models\Aalberts\Compano\Productgroup as ProductGroupModel;
use App\Models\Aalberts\Compano\Productgroup;
use Czim\Repository\Criteria\Common\WhereHas;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Collection;

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
     * @param bool $filterEmpty     if true
     * @return ProductGroupModel[]|Collection
     */
    public function getAllForIndex($filterEmpty = true)
    {
        $this->restrictForOrganizationOnce();

        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

        $groups = $this->cachedQuery()
            ->whereHas('translations', $this->eagerLoadCachedTranslationCallable())
            ->get();


        // Filter out groups that have no records
        $filter  = new ProductFilter([]);
        $counter = new ProductgroupCounter();

        $query = $filter->getCountableBaseQuery();
        $filter->apply($query);

        if ($filterEmpty) {
            $groups = $groups->filter(function (Productgroup $group) use ($filter, $query, $counter) {
                $countQuery = clone $query;
                $counts = $counter->count('productgroup', $countQuery, $filter, $group->id);
                return (bool) array_get($counts, $group->id, false);
            });
        }


        // Order by position, label
        $groups = $groups->sort(function (ProductGroupModel $group) {

            $label = $group->productgroups->count()
                ? ($group->productgroups->first()->translations->count()
                    ? $group->productgroups->first()->translations->first()->label
                    : null)
                : null;

            if ( ! $label && $group->translations->count()) {
                $label = $group->translations->first()->label;
            }

            return ($group->position ?: 99999) . ':' . $label;
        });

        return $groups;
    }

    /**
     * Cached.
     *
     * @param string $label
     * @param bool   $translated    whether to look for a translated label
     * @return Productgroup
     */
    public function getByLabel($label, $translated = false)
    {
        $this->restrictForOrganizationOnce();

        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

        $query = $this->cachedQuery();

        if ($translated) {
            $query->whereHas('translations', function ($query) use ($label) {
                $query->where('label', $label);
            });
        } else {
            $query->where('label', $label);
        }

        /** @var ProductGroupModel $model */
        $model = $query->first();

        return $model;
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

        /** @var ProductGroupModel $model */
        $model = $this->cachedQuery()
            ->whereTranslation('slug', $slug)
            ->first();

        return $model;
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

    /**
     * Returns image link
     *
     * @param Productgroup $productgroup
     * @param null         $sizeQuery
     * @return null
     */
    public function getProductImageForProductGroup(ProductGroupModel $productgroup, $sizeQuery = null)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = app(ProductRepository::class);

        $filterData = new ProductFilterData([
            'has_image'        => true,
            'for_organization' => true,
            'productgroup'     => $productgroup->id,
        ]);

        $result = $productRepository->filter($filterData, 1, 1);

        if ( ! $result->count()) {
            return null;
        }

        /** @var Product $product */
        $product = head($result->items());

        return $product->present()->image($sizeQuery);
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
            'translations'                     => $this->eagerLoadCachedTranslationCallable(),
            'productgroups.translations'       => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::PRODUCTGROUP]),
            'productgroups'                    => $this->eagerLoadCachedCallable(null, [CacheTag::PRODUCTGROUP]),
            'productgroups.productgroupImages' => $this->eagerLoadCachedCallable(null, [CacheTag::PRODUCTGROUP]),
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
            new WhereHas('productgroups', $this->eagerLoadCachedCallable(null, [CacheTag::PRODUCTGROUP]))
        );

        return $this;
    }

}
