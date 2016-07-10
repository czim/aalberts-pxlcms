<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use Aalberts\Repositories\Compano\Filter\SalesorganizationcodeFilterRepository;
use App\Models\Aalberts\Compano\Product as ProductModel;
use Czim\PxlCms\Models\Scopes\OnlyActiveScope;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Czim\Repository\Criteria\Common\Custom;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class ProductRepository extends AbstractCompanoRepository
{
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


    public function filter($count = 10)
    {
        // todo
    }


    /**
     * Returns products for a category, paginated.
     * Cached.
     *
     * @param string $category
     * @param int    $count
     * @return mixed
     */
    public function category($category, $count = 10)
    {
        $this->forCategoryOnce($category);

        return $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->paginate($count);
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
        $this->filterForSalesOrganizationCodeOnce();

        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        if ($bySlug) {
            return $this->findBySlug($find, true);
        }

        return $this->cachedQuery()
            ->withoutGlobalScope(OnlyActiveScope::class)
            ->where('id', (int) $find)
            ->first();
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

        return $query->first();
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
            'items' => $this->eagerLoadCachedCallable(null, [ CacheTag::CMP_PRODUCT ]),

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

            'successor'   => $this->eagerLoadCachedCallable(null, [ CacheTag::CMP_PRODUCT ]),
            'predecessor' => $this->eagerLoadCachedCallable(null, [ CacheTag::CMP_PRODUCT ]),
        ];
    }


    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    protected function filterForSalesOrganizationCodeOnce()
    {
        if ( ! $this->filterByOrganizationCode) return $this;

        /** @var SalesorganizationcodeFilterRepository $filterRepository */
        $filterRepository = app(SalesorganizationcodeFilterRepository::class);

        $ids = $filterRepository->productIds();

        $this->pushCriteriaOnce(
            new Custom(function ($query) use ($ids) {
                return $query->whereIn('cmp_product.id', $ids);
            })
        );

        return $this;
    }

    /**
     * @param null|string $category
     * @return $this
     */
    protected function forCategoryOnce($category)
    {
        //if ($category) {
        //    $this->pushCriteriaOnce(new FieldIsValue('type', $category));
        //}

        return $this;
    }
}
