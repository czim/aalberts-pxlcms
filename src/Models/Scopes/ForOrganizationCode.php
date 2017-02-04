<?php
namespace Aalberts\Models\Scopes;

trait ForOrganizationCode
{

    /**
     * Boot the scope
     */
    public static function bootForOrganizationCode()
    {
        static::addGlobalScope( new ForOrganizationCodeScope );
    }

    /**
     * Get the name of the "organization" column.
     *
     * @return string
     */
    public function getOrganizationCodeColumn()
    {
        return defined('static::ORGANIZATION_CODE_COLUMN')
            ?   static::ORGANIZATION_CODE_COLUMN
            :   config('pxlcms.scopes.for_organization_code.column', 'salesorganizationcode');
    }

    /**
     * Get the fully qualified column name for applying the scope
     *
     * @return string
     */
    public function getQualifiedOrganizationCodeColumn()
    {
        return $this->getTable() . '.' . $this->getOrganizationCodeColumn();
    }

    /**
     * Get the query builder without the scope applied
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forAnyOrganizationCode()
    {
        return with(new static)->newQueryWithoutScope( new ForOrganizationCodeScope );
    }
}
