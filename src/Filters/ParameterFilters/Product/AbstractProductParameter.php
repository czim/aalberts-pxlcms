<?php
namespace Aalberts\Filters\ParameterFilters\Product;

use Aalberts\Enums\CacheTag;
use Aalberts\Filters\ParameterFilters\AbstractParameterFilter;
use Czim\Filter\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

abstract class AbstractProductParameter extends AbstractParameterFilter
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

        $ids = $this->getProductIdsForObjectModelId($value);

        if ( ! empty($ids)) {
            $query->whereRaw("`cmp_product`.`id` IN ({$ids})");
        }

        return $query;
    }

    /**
     * Returns string of wherein-able product IDs.
     *
     * @param int $id   object model (for what is being filtered on) id
     * @return string
     */
    protected function getProductIdsForObjectModelId($id)
    {
        /** @var string|Model $class */
        $class = $this->filterModel();

        $filter = $class::query()
            ->remember($this->defaultTtl())
            ->cacheTags([ CacheTag::CMP_PRODUCT ])
            ->where($this->filterIdColumn(), $id)
            ->first();

        if ( ! $filter) {
            return null;
        }

        return $filter->products;
    }

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    abstract protected function filterModel();

    /**
     * Returns the column on the filterModel that contains the object model ID.
     *
     * @return string
     */
    abstract protected function filterIdColumn();

}
