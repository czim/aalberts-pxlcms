<?php
namespace Aalberts\Filters;

use Czim\Filter\Filter;
use Czim\Filter\ParameterFilters\NotEmpty;

class ProductFilter extends Filter
{
    protected $filterDataClass = ProductFilterData::class;
    protected $table = 'cmp_product';

    protected function strategies()
    {
        return [
            'has_image' => new NotEmpty($this->table, 'image'),
            'has_label' => new NotEmpty($this->table, 'label'),
        ];
    }
    

    /**
     * @inheritdoc
     */
    protected function applyParameter($name, $value, $query)
    {


        parent::applyParameter($name, $value, $query);
    }

}
