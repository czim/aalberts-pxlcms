<?php
namespace Aalberts\Filters\ParameterFilters\Product;

use App\Models\Aalberts\Filter\Producttype;

class ProducttypeParameter extends AbstractProductParameter
{

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return Producttype::class;
    }

    /**
     * Returns the column on the filterModel that contains the object model ID.
     *
     * @return string
     */
    protected function filterIdColumn()
    {
        return 'producttype';
    }

}
