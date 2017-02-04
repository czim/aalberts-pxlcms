<?php
namespace Aalberts\Filters;

use Czim\Filter\FilterData;

class ProductFilterData extends FilterData
{
    protected $rules = [
        // Ignore products that don't have a label set
        'has_label' => 'boolean',
        // Ignore products that don't belong to this organization's cmp_filter_salesorganizationcode list
        'for_organization' => 'boolean',
        // Only include products for a given cmp_productgroup (uses cmp_filter_productgroup)
        'productgroup' => '',

        // Sorting order to use (see filter for available order strings)
        'order' => 'string',
    ];

    protected $defaults = [
        'has_label'        => null,
        'for_organization' => null,
        'productgroup'     => null,
        'order'            => null,
    ];

}
