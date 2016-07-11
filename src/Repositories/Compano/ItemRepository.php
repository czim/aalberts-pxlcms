<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Item as ItemModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;

class ItemRepository extends AbstractCompanoRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_PRODUCT ];

    /**
     * @var bool
     */
    protected $filterByOrganizationCode = true;


    public function model()
    {
        return ItemModel::class;
    }


    /**
     * Returns article for detail page.
     * Cached.
     *
     * @param string|int $find      ID or slug of the record
     * @param bool       $bySlug    if true, expects $find to be a slug, otherwise an ID
     * @return ItemModel
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
     * Finds a record by its slug/label
     * Cached.
     *
     * @param string $slug
     * @return null|ItemModel
     */
    public function findBySlug($slug)
    {
        return $this->cachedQuery()
            ->where('code', $slug)
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
            'translations'         => $this->eagerLoadCachedTranslationCallable(),
            'product'              => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'product.translations' => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
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
            'finishings'                         => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'finishings.translations'            => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'materials'                          => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'materials.translations'             => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'productlines'                       => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'sealings'                           => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'sealings.translations'              => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'shapes'                             => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'shapes.translations'                => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),

            'product.sealings'              => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'product.sealings.translations' => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),
            'product.shapes'                => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'product.shapes.translations'   => $this->eagerLoadCachedTranslationCallable(null, null, [CacheTag::CMP_PRODUCT]),

            'successor'   => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'predecessor' => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
        ];
    }


    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------

}
