<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Productline as ProductlineModel;

class ProductLineRepository extends AbstractFilterableRepository
{
    protected $translated = false;
    protected $cacheTags = [ CacheTag::CMP_MISC ];

    public function model()
    {
        return ProductlineModel::class;
    }

}
