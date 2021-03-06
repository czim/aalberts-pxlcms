<?php
namespace Aalberts\Filters\ParameterCounters\Product;

use Aalberts\Filters\ParameterFilters\Product\PumpbrandParameter;
use Czim\Filter\Contracts\ParameterFilterInterface;

class PumpbrandCounter extends AbstractProductCounter
{

    /**
     * Returns the countable key in the product filter.
     *
     * @return string
     */
    protected function countableKey()
    {
        return 'pumpbrand';
    }

    /**
     * Returns the model class for the filtered object (e.g.: model for cmp_productline).
     *
     * @return string
     */
    protected function objectModel()
    {
        return \App\Models\Aalberts\Compano\Pumpbrand::class;
    }

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return \App\Models\Aalberts\Filter\Pumpbrand::class;
    }

    /**
     * @return ParameterFilterInterface
     */
    protected function filterParameter()
    {
        return new PumpbrandParameter();
    }

}
