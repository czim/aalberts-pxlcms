<?php
namespace Aalberts\Filters\ParameterCounters;

use Czim\Filter\Contracts\CountableFilterInterface;
use Czim\Filter\ParameterCounters\SimpleDistinctValue;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Counts different distinct values for a single column with configurable aliases.
 * Returns boolean value keys as 'true', 'false'
 */
class SimpleDistinctBooleanValue extends SimpleDistinctValue
{

    /**
     * Returns the count for a countable parameter, given the query provided
     *
     * @param string                   $name
     * @param EloquentBuilder          $query
     * @param CountableFilterInterface $filter
     * @return array
     */
    public function count($name, $query, CountableFilterInterface $filter)
    {
        return $this->normalizeBooleanValues(
            parent::count($name, $query, $filter)
        );
    }

    /**
     * @param array|\ArrayAccess $counts
     * @return array
     */
    protected function normalizeBooleanValues($counts)
    {
        $fixedCounts = [];

        // consider null and false to both be 'false'
        $fixedCounts['false'] = 0;

        if (isset($counts[0])) {
            $fixedCounts['false'] = $counts[0];
        }

        if (isset($counts[null])) {
            $fixedCounts['false'] += $counts[null];
        }

        if (isset($counts[1])) {
            $fixedCounts['true'] = $counts[1];
        }

        // prevent empty values from being included
        if (empty($fixedCounts['false'])) {
            unset($fixedCounts['false']);
        }

        return $fixedCounts;
    }

}
