<?php
namespace Aalberts\Filters;

use Czim\Filter\FilterData;

class ProductFilterData extends FilterData
{
    protected $rules = [
        'has_image' => 'boolean',
        'has_label' => 'boolean',
    ];

    protected $defaults = [
        'has_image' => null,
        'has_label' => null,
    ];

}
