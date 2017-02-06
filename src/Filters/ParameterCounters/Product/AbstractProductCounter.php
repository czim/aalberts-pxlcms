<?php
namespace Aalberts\Filters\ParameterCounters\Product;

use Aalberts\Enums\CacheTag;
use Czim\Filter\Contracts\CountableFilterInterface;
use Czim\Filter\Contracts\ParameterCounterInterface;
use Czim\Filter\Contracts\ParameterFilterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

abstract class AbstractProductCounter implements ParameterCounterInterface
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
        $ids = $this->getAllObjectIds();

        $parameter = $this->filterParameter();

        $counts = [];

        foreach ($ids as $id) {

            $clonedQuery = clone $query;

            $clonedQuery = $parameter->apply($this->countableKey(), $id, $clonedQuery, $filter);

            $count = $clonedQuery->count();

            if ( ! $count) {
                continue;
            }

            $counts[ $id ] = $count;
        }

        return $counts;
    }

    /**
     * @return int[]
     */
    protected function getAllObjectIds()
    {
        /** @var string|Model $class */
        $class = $this->objectModel();

        return $class::query()
            ->remember(config('aalberts.cache.ttl.compano'))
            ->cacheTags([ CacheTag::CMP_PRODUCT ])
            ->pluck('id')
            ->toArray();
    }

    /**
     * Returns the countable key in the product filter.
     *
     * @return string
     */
    abstract protected function countableKey();

    /**
     * Returns the model class for the filtered object (e.g.: model for cmp_productline).
     *
     * @return string
     */
    abstract protected function objectModel();

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    abstract protected function filterModel();

    /**
     * @return ParameterFilterInterface
     */
    abstract protected function filterParameter();

}
