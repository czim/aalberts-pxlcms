<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\ItemDownload as ItemDownloadModel;
use App\Models\Aalberts\Compano\Product;
use Illuminate\Support\Collection;

class ItemDownloadRepository extends AbstractCompanoRepository
{
    protected $translated = false;
    protected $cacheTags  = [ CacheTag::CMP_PRODUCT ];

    public function model()
    {
        return ItemDownloadModel::class;
    }


    /**
     * Returns by product.
     * Cached.
     *
     * @param Product $product
     * @return Collection|ItemDownloadModel[]
     */
    public function getByProduct(Product $product)
    {
        $itemIds = $product->items->pluck('id');

        return $this->cachedQuery()
            ->where('language', $this->languageIdForLocale())
            ->whereIn('item', $itemIds)
            ->groupBy('file')
            ->orderBy('file')
            ->get();
    }

}
