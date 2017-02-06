<?php
namespace Aalberts\Filters\ParameterFilters\Product;

use App\Models\Aalberts\Filter\Productline;

class ProductlineParameter extends AbstractProductParameter
{

    /**
     * Returns the model class for the filter (e.g.: model for cmp_filter_productline).
     *
     * @return string
     */
    protected function filterModel()
    {
        return Productline::class;
    }

    /**
     * Returns the column on the filterModel that contains the object model ID.
     *
     * @return string
     */
    protected function filterIdColumn()
    {
        return 'productline';
    }

}
