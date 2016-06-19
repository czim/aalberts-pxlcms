<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Cms\Country as CountryModel;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Collection;

class CountryRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::COUNTRY ];

    public function model()
    {
        return CountryModel::class;
    }

    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()->merge([
            CriteriaKey::WITH  => new WithRelations($this->withBase()),
        ]);
    }


    /**
     * Looks up a content entry by its code.
     * Cached.
     *
     * @param string $code
     * @return null|CountryModel
     */
    public function findByCode($code)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->where('code',  $code)
            ->first();
    }

    /**
     * Returns active / available for this organization.
     * Cached.
     * 
     * @return Collection|CountryModel[]
     */
    public function available()
    {
        return $this->cachedQuery()
            ->withoutGlobalScope(PositionOrderedScope::class)
            ->join('cms_organization_language', 'cms_organization_language.language', '=', 'cms_language.id')
            ->where('cms_organization_language.organization', config('aalberts.organization'))
            ->orderBy('cms_organization_language.default', 'desc')
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Returns default for this organization.
     * Cached.
     *
     * @return null|CountryModel
     */
    public function defaultAvailable()
    {
        return $this->cachedQuery()
            ->join('cms_organization_country', 'cms_organization_country.language', '=', 'cms_country.id')
            ->where('cms_organization_country.organization', config('aalberts.organization'))
            ->where('cms_organization_country.default', true)
            ->first();
    }



    // ------------------------------------------------------------------------------
    //      With Relations
    // ------------------------------------------------------------------------------

    /**
     * Returns with parameter array to use by default
     *
     * @return array
     */
    protected function withBase()
    {
        return [];
    }

    /**
     * Returns with parameter array to use for detail page
     *
     * @return array
     */
    protected function withDetail()
    {
        return [
            'languages' => $this->eagerLoadCachedCallable(null, [ CacheTag::COUNTRY ]),
            'suppliers' => $this->eagerLoadCachedCallable(null, [ CacheTag::CMP_SUPPLIER ]),
        ];
    }

}
