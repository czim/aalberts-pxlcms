<?php
namespace Aalberts\Filters;

use Czim\Filter\FilterData;

class ProductFilterData extends FilterData
{
    protected $rules = [
        // Ignore products that don't have a label set
        'has_label'                          => 'boolean',
        // Ignore products that don't have a compano image
        'has_image'                          => 'boolean',
        // Limit by specific product IDs
        'ids'                                => 'array',
        'ids.*'                              => 'integer',
        // Ignore products that don't belong to this organization's cmp_filter_salesorganizationcode list
        'for_organization'                   => 'boolean',
        // Only include products for a given cmp_productgroup (uses cmp_filter_productgroup)
        'productgroup'                       => '',

        // Sorting order to use (see filter fdor available order strings)
        'order'                              => 'string',

        // Standard product filters
        'angleofbow'                         => 'array',
        'angleofbow.*'                       => 'integer',
        'applications'                       => 'array',
        'applications.*'                     => 'integer',
        'approvals'                          => 'array',
        'approvals.*'                        => 'integer',
        'bowrange'                           => 'array',
        'bowrange.*'                         => 'integer',
        'brand'                              => 'array',
        'brand.*'                            => 'integer',
        'colors'                             => 'array',
        'colors.*'                           => 'integer',
        'connectiontype'                     => 'array',
        'connectiontype.*'                   => 'integer',
        'contourcode'                        => 'array',
        'contourcode.*'                      => 'integer',
        'externaltubediameterofconnection'   => 'array',
        'externaltubediameterofconnection.*' => 'integer',
        'finishings'                         => 'array',
        'finishings.*'                       => 'integer',
        'manufacturercode'                   => 'array',
        'manufacturercode.*'                 => 'integer',
        'materialquality'                    => 'array',
        'materialquality.*'                  => 'integer',
        'materials'                          => 'array',
        'materials.*'                        => 'integer',
        'numberofconnections'                => 'array',
        'numberofconnections.*'              => 'integer',
        'productline'                        => 'array',
        'productline.*'                      => 'integer',
        'producttype'                        => 'array',
        'producttype.*'                      => 'integer',
        'pumpbrand'                          => 'array',
        'pumpbrand.*'                        => 'integer',
        'series'                             => 'array',
        'series.*'                           => 'integer',
        'shape'                              => 'array',
        'shape.*'                            => 'integer',
        'solutions'                          => 'array',
        'solutions.*'                        => 'integer',
        'type'                               => 'array',
        'type.*'                             => 'integer',
    ];

    protected $defaults = [
        'has_label'        => true,
        'has_image'        => null,
        'ids'              => null,
        'for_organization' => true,
        'productgroup'     => null,
        'order'            => null,

        'angleofbow'                       => null,
        'applications'                     => null,
        'approvals'                        => null,
        'bowrange'                         => null,
        'brand'                            => null,
        'colors'                           => null,
        'connectiontype'                   => null,
        'contourcode'                      => null,
        'externaltubediameterofconnection' => null,
        'finishings'                       => null,
        'manufacturercode'                 => null,
        'materialquality'                  => null,
        'materials'                        => null,
        'numberofconnections'              => null,
        'productline'                      => null,
        'producttype'                      => null,
        'pumpbrand'                        => null,
        'series'                           => null,
        'shape'                            => null,
        'solutions'                        => null,
        'type'                             => null,
    ];

}
