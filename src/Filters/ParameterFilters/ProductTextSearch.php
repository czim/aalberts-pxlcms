<?php
namespace Aalberts\Filters\ParameterFilters;

use Czim\Filter\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

/**
 * Class ProductTextSearch
 *
 * Filters products by a search term string.
 */
class ProductTextSearch extends AbstractParameterFilter
{

    /**
     * Applies parameter filtering for a given query
     *
     * @param string                  $name
     * @param mixed                   $value
     * @param EloquentBuilder|Builder $query
     * @param FilterInterface         $filter
     * @return EloquentBuilder
     */
    public function apply($name, $value, $query, FilterInterface $filter)
    {
        if (empty($value)) {
            return $query;
        }

        $value = trim($value);

        // In the old codebase item.code matches were done first,
        // and if anything matched, the other results would be ignored.
        // If nothing matched, the products search is done in earnest.
        // Here both methods are combined for now.

        $query->join('cmp_search', 'cmp_search.id', '=', 'cmp_product.id');

        $query->where(function ($query) use ($value) {
            /** @var Builder|EloquentBuilder $query */

            $query
                ->where('cmp_item.code', '=', $value)
                ->orWhere('cmp_search.product', 'like', '%' . $value . '%');
        });

        return $query;
    }

}
