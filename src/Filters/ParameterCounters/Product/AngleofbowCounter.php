<?php
namespace Aalberts\Filters\ParameterCounters\Product;

use Aalberts\Filters\ParameterFilters\Product\AngleofbowParameter;
use Czim\Filter\Contracts\ParameterFilterInterface;

class AngleofbowCounter extends AbstractProductCounter
{

    /**
     * Returns the countable key in the product filter.
     *
     * @return string
     */
    protected function countableKey()
    {
        return 'angleofbow';
    }

    /**
     * Returns the model class for the filtered object (e.g.: model for cmp_productline).
     *
     * @return string
     */
    protected function objectModel()
    {
        return \App\Models\Aalberts\Compano\Angleofbow::class;
    }

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return \App\Models\Aalberts\Filter\Angleofbow::class;
    }

    /**
     * @return ParameterFilterInterface
     */
    protected function filterParameter()
    {
        return new AngleofbowParameter();
    }

}
