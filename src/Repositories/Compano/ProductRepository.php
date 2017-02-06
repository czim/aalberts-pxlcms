<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use Aalberts\Filters\ProductFilter;
use Aalberts\Filters\ProductFilterData;
use Aalberts\Repositories\Compano\Filter\SalesorganizationcodeFilterRepository;
use App\Models\Aalberts\Compano\Product as ProductModel;
use Czim\Filter\CountableResults;
use Czim\PxlCms\Models\Scopes\OnlyActiveScope;
use Czim\Repository\Criteria\Common\Custom;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends AbstractCompanoRepository
{
    const DEFAULT_PAGE_SIZE = 25;

    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_PRODUCT ];

    /**
     * @var bool
     */
    protected $filterByOrganizationCode = true;


    public function model()
    {
        return ProductModel::class;
    }


    /**
     * Filters products, given product filter data.
     * Cached.
     *
     * @param ProductFilterData $data
     * @param int|null          $page
     * @param int               $pageSize
     * @param bool              $noCache    if true, does not cache the filter query
     * @return LengthAwarePaginator
     */
    public function filter(ProductFilterData $data, $page = null, $pageSize = self::DEFAULT_PAGE_SIZE, $noCache = false)
    {
        $this->removeCriteriaOnce(CriteriaKey::ORDER);

        $this->pushCriteriaOnce(
            new WithRelations($this->withBase()),
            CriteriaKey::WITH
        );

        /** @var Builder|EloquentBuilder $results */
        $query = $noCache ? $this->query() : $this->cachedQuery();

        $filter = new ProductFilter($data);
        $filter->apply($query);

        // Handle pagination and counts
        // Clone the query so the cache/remember doesn't only stick to the count
        // Check pagination, prevent page from exceeding bounds

        $countQuery = clone $query;
        $total = $countQuery->count($query->getModel()->getTable() . '.' . $query->getModel()->getKeyName());

        $this->calculateTotalPagesWithPageCheck($total, $pageSize, $page);

        if ( ! empty($pageSize)) {
            $query->skip( max(0, $page - 1) * $pageSize)->take($pageSize);
        }

        return new LengthAwarePaginator($query->get(), $total, $pageSize, max($page, 1));
    }

    /**
     * Returns counts for filters, given product filter data.
     * Cached.
     *
     * @param ProductFilterData $data
     * @param string[]          $countables     specify which countables to include
     * @return CountableResults
     */
    public function filterCounts(ProductFilterData $data, array $countables = [])
    {
        $this->removeCriteriaOnce(CriteriaKey::ORDER);

        $filter = new ProductFilter($data);

        return $filter->getCounts($countables);
    }

    /**
     * Returns product for detail page.
     * Cached.
     *
     * @param string|int $find      ID or slug of the record
     * @param bool       $bySlug    if true, expects $find to be a slug, otherwise an ID
     * @return ProductModel
     */
    public function detail($find, $bySlug = true)
    {
        $this->filterForOrganizationOnce();

        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        if ($bySlug) {
            return $this->findBySlug($find, true);
        }

        /** @var ProductModel $product */
        $product = $this->cachedQuery()
            ->withoutGlobalScope(OnlyActiveScope::class)
            ->where('id', (int) $find)
            ->first();

        return $product;
    }

    /**
     * Finds a record by its slug/label
     * Cached.
     *
     * @param string $slug
     * @param bool   $allowInactive
     * @return ProductModel|null
     */
    public function findBySlug($slug, $allowInactive = false)
    {
        $query = $this->cachedQuery()
            ->where('label', $slug);

        if ($allowInactive) {
            $query->withoutGlobalScope(OnlyActiveScope::class);
        }

        /** @var ProductModel|null $product */
        $product = $query->first();

        return $product;
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
            'items'              => $this->eagerLoadItemCallback(),
            'items.translations' => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),

            'approvals'                 => $this->eagerLoadCachedCallable(null, [CacheTag::APPROVAL]),
            'applications'              => $this->eagerLoadCachedCallable(null, [CacheTag::APPLICATION]),
            'applications.translations' => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::APPLICATION]),
            'solutions'                 => $this->eagerLoadCachedCallable(null, [CacheTag::SOLUTION]),
            'solutions.translations'    => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::SOLUTION]),

            'colors'                             => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'colors.translations'                => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'connectiontypes'                    => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'connectiontypes.translations'       => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'connectiontypes.connectiontypeSize' => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'contourcodes'                       => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'finishings'                         => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'finishings.translations'            => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'materials'                          => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'materials.translations'             => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'productlines'                       => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'sealings'                           => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'sealings.translations'              => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'shapes'                             => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'shapes.translations'                => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),

            'successor'   => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'predecessor' => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
        ];
    }


    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    protected function filterForOrganizationOnce()
    {
        if ( ! $this->filterByOrganizationCode) return $this;

        $ids = $this->getProductIdsForOrganization();

        $this->pushCriteriaOnce(
            new Custom(function ($query) use ($ids) {
                /** @var Builder $query */
                return $query->whereRaw("`cmp_product`.`id` IN ({$ids})");
            })
        );

        return $this;
    }

    /**
     * @return null|string
     */
    protected function getProductIdsForOrganization()
    {
        /** @var SalesorganizationcodeFilterRepository $filterRepository */
        $filterRepository = app(SalesorganizationcodeFilterRepository::class);

        return $filterRepository->productIds();
    }

    /**
     * Returns callback to use for product->items relation.
     *
     * @return callback
     */
    protected function eagerLoadItemCallback()
    {
        return function ($query) {
            /** @var Builder $query */
            $query
                ->remember($this->defaultTtl())
                ->cacheTags([CacheTag::CMP_PRODUCT]);

            if (config('aalberts.queries.uses-is-webitem')) {
                $query->where('iswebitem', true);
            }
        };
    }

}
