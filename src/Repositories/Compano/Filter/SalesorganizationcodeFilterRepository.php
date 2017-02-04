<?php
namespace Aalberts\Repositories\Compano\Filter;

use Aalberts\Enums\CacheTag;
use Aalberts\Repositories\Compano\AbstractCompanoRepository;
use App\Models\Aalberts\Filter\Salesorganizationcode as SalesorganizationcodeFilterModel;

class SalesorganizationcodeFilterRepository extends AbstractCompanoRepository
{
    protected $cacheTags = [ CacheTag::CMP_PRODUCT ];

    public function model()
    {
        return SalesorganizationcodeFilterModel::class;
    }

    /**
     * Returns string with product IDs for a given sales organization code.
     *
     * @param null|string $code
     * @return null|string
     */
    public function productIds($code = null)
    {
        $code = $code ?: app('aalberts-helper')->organizationCode();
        if ( ! $code) return null;

        /** @var SalesorganizationcodeFilterModel $model */
        $model = $this->cachedQuery()
            ->where('salesorganizationcode', $code)
            ->first();

        if ( ! $model) return null;

        return $model->products;
    }

}
