<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\Country as CountryModel;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Collection;

class CountryRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::COUNTRY ];

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
     * Looks up a content entry by its translated slug.
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

        return $this->query()
            ->where('code',  $code)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
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
            'languages' => $this->eagerLoadCachedCallable(null, [ CacheTags::COUNTRY ]),
            'suppliers' => $this->eagerLoadCachedCallable(null, [ CacheTags::CMP_SUPPLIER ]),
        ];
    }

}
