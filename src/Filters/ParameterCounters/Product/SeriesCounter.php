<?php
namespace Aalberts\Filters\ParameterCounters\Product;

use Aalberts\Filters\ParameterFilters\Product\SeriesParameter;
use Czim\Filter\Contracts\ParameterFilterInterface;

class SeriesCounter extends AbstractProductCounter
{

    /**
     * Returns the countable key in the product filter.
     *
     * @return string
     */
    protected function countableKey()
    {
        return 'series';
    }

    /**
     * Returns the model class for the filtered object (e.g.: model for cmp_productline).
     *
     * @return string
     */
    protected function objectModel()
    {
        return \App\Models\Aalberts\Compano\Series::class;
    }

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return \App\Models\Aalberts\Filter\Series::class;
    }

    /**
     * @return ParameterFilterInterface
     */
    protected function filterParameter()
    {
        return new SeriesParameter();
    }

}
