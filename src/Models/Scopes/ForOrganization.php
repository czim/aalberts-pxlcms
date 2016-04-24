<?php
namespace Aalberts\Models\Scopes;

trait ForOrganization
{

    /**
     * Boot the scope
     */
    public static function bootForOrganization()
    {
        static::addGlobalScope( new ForOrganizationScope );
    }

    /**
     * Get the name of the "organization" column.
     *
     * @return string
     */
    public function getOrganizationColumn()
    {
        return defined('static::ORGANIZATION_COLUMN')
            ?   static::ORGANIZATION_COLUMN
            :   config('pxlcms.scopes.for_organization.column', 'organization');
    }

    /**
     * Get the fully qualified column name for applying the scope
     *
     * @return string
     */
    public function getQualifiedOrganizationColumn()
    {
        return $this->getTable() . '.' . $this->getOrganizationColumn();
    }

    /**
     * Get the query builder without the scope applied
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forAnyOrganization()
    {
        return with(new static)->newQueryWithoutScope( new ForOrganizationScope );
    }
}
