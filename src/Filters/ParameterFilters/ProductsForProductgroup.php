<?php
namespace Aalberts\Filters\ParameterFilters;

use Aalberts\Enums\CacheTag;
use Aalberts\Repositories\Compano\ProductGroupRepository;
use App\Models\Aalberts\Cms\ProductgroupFilter;
use App\Models\Aalberts\Filter\Productgroup as FilterProductgroup;
use Czim\Filter\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

/**
 * Class ProductsForOrganization
 *
 * Filters products by the list of product ids listed in cmp_filter_salesorganizationcode
 */
class ProductsForProductgroup extends AbstractParameterFilter
{

    /**
     * Applies parameter filtering for a given query
     *
     * @param string                  $name
     * @param mixed                   $value
     * @param EloquentBuilder|Builder $query
     * @param FilterInterface         $filter
     * @return EloquentBuilder|Builder
     */
    public function apply($name, $value, $query, FilterInterface $filter)
    {
        if ( ! $value) {
            return $query;
        }

        if (is_string($value) && ! is_numeric($value)) {
            $value = $this->getIdForCategorySlug($value);
        }

        $ids = $this->getProductIdsForProductgroupId($value);

        if ( ! empty($ids)) {
            $query->whereRaw("`cmp_product`.`id` IN ({$ids})");
        }

        return $query;
    }


    /**
     * Returns the ID for the Compano Productgroup model by slug.
     *
     * @param string $category
     * @return int|null
     */
    protected function getIdForCategorySlug($category)
    {
        /** @var ProductGroupRepository $repository */
        $repository = app(ProductGroupRepository::class);

        $productGroup = $repository->getBySlug($category);

        if ( ! $productGroup) {
            return null;
        }

        return $productGroup->id;
    }

    /**
     * Returns product ids string for raw whereIn.
     *
     * @param int $id
     * @return null|string
     */
    protected function getProductIdsForProductgroupId($id)
    {
        /** @var FilterProductgroup $filter */
        $filter = FilterProductgroup::query()
            ->remember($this->defaultTtl())
            ->cacheTags([CacheTag::CMP_PRODUCT])
            ->where('productgroup', $id)
            ->first();

        if ( ! $filter) {
            return null;
        }

        return $filter->products;
    }

}
