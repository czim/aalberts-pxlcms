<?php
namespace Aalberts\Filters\ParameterCounters;

use Aalberts\Enums\CacheTag;
use Aalberts\Filters\ParameterFilters\ProductsForProductgroup;
use App\Models\Aalberts\Compano\Productgroup;
use Czim\Filter\Contracts\CountableFilterInterface;
use Czim\Filter\Contracts\ParameterCounterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;


class ProductProductgroup implements ParameterCounterInterface
{

    /**
     * Returns the count for a countable parameter, given the query provided
     *
     * @param string                   $name
     * @param Builder|EloquentBuilder  $query
     * @param CountableFilterInterface $filter
     * @param int|null                 $productGroupId      if set, only retrieves count for given id
     * @return array
     */
    public function count($name, $query, CountableFilterInterface $filter, $productGroupId = null)
    {
        // Get list of productgroup ids to check
        if ($productGroupId) {
            $ids = [ $productGroupId ];
        } else {
            $ids = $this->getAllProductGroupIds();
        }

        $parameter = new ProductsForProductgroup();

        $counts = [];

        foreach ($ids as $id) {
            $clonedQuery = clone $query;
            $clonedQuery = $parameter->apply('productgroup', $id, $clonedQuery, $filter);
            $counts[ $id ] = $clonedQuery->count();
        }

        return $counts;
    }

    /**
     * @return int[]
     */
    protected function getAllProductGroupIds()
    {
        return Productgroup::query()
            ->remember(config('aalberts.cache.ttl.compano'))
            ->cacheTags([CacheTag::CMP_PRODUCT])
            ->pluck('id')
            ->toArray();
    }

}
