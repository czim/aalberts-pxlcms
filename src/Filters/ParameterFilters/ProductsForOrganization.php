<?php
namespace Aalberts\Filters\ParameterFilters;

use Aalberts\Repositories\Compano\Filter\SalesorganizationcodeFilterRepository;
use Czim\Filter\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

/**
 * Class ProductsForOrganization
 *
 * Filters products by the list of product ids listed in cmp_filter_salesorganizationcode
 */
class ProductsForOrganization extends AbstractParameterFilter
{

    /**
     * @var string
     */
    protected $organizationCode;


    /**
     * @param string|null $organizationCode     null to use current organization
     */
    public function __construct($organizationCode = null)
    {
        $this->organizationCode = $organizationCode;
    }

    /**
     * Applies parameter filtering for a given query
     *
     * @param string                  $name
     * @param mixed                   $value
     * @param EloquentBuilder|Builder $query
     * @param FilterInterface         $filter
     * @return EloquentBuilder
     */
    public function apply($name, $value, $query, FilterInterface $filter)
    {
        if ( ! $value) {
            return $query;
        }

        /** @var SalesorganizationcodeFilterRepository $filterRepository */
        $filterRepository = app(SalesorganizationcodeFilterRepository::class);

        $ids = $filterRepository->productIds($this->organizationCode);

        if ( ! empty($ids)) {
            $query->whereRaw("`cmp_product`.`id` IN ({$ids})");
        }

        return $query;
    }

}
