<?php
namespace Aalberts\Filters\ParameterFilters\Product;

use App\Models\Aalberts\Filter\Pumpbrand;

class PumpbrandParameter extends AbstractProductParameter
{

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return Pumpbrand::class;
    }

    /**
     * Returns the column on the filterModel that contains the object model ID.
     *
     * @return string
     */
    protected function filterIdColumn()
    {
        return 'pumpbrand';
    }

}
