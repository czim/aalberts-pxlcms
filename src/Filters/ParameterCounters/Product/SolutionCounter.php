<?php
namespace Aalberts\Filters\ParameterCounters\Product;

use Aalberts\Filters\ParameterFilters\Product\SolutionParameter;
use Czim\Filter\Contracts\ParameterFilterInterface;

class SolutionCounter extends AbstractProductCounter
{

    /**
     * Returns the countable key in the product filter.
     *
     * @return string
     */
    protected function countableKey()
    {
        return 'solutions';
    }

    /**
     * Returns the model class for the filtered object (e.g.: model for cmp_productline).
     *
     * @return string
     */
    protected function objectModel()
    {
        return \App\Models\Aalberts\Compano\Solution::class;
    }

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return \App\Models\Aalberts\Filter\Solution::class;
    }

    /**
     * @return ParameterFilterInterface
     */
    protected function filterParameter()
    {
        return new SolutionParameter();
    }

}
