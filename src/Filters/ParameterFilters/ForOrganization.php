<?php
namespace Aalberts\Filters\ParameterFilters;

use Czim\Filter\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ForOrganization extends AbstractParameterFilter
{

    /**
     * @var int
     */
    protected $organizationId;


    public function __construct($organizationId = null)
    {
        $this->organizationId = $organizationId ?: config('aalberts.organization');
    }

    /**
     * Applies parameter filtering for a given query
     *
     * @param string          $name
     * @param mixed           $value
     * @param EloquentBuilder $query
     * @param FilterInterface $filter
     * @return EloquentBuilder
     */
    public function apply($name, $value, $query, FilterInterface $filter)
    {
        return $query->where('organization', $this->organizationId);
    }
}
