<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\Language as LanguageModel;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Collection;

class LanguageRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::COUNTRY ];

    public function model()
    {
        return LanguageModel::class;
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
     * @return null|LanguageModel
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
     * Looks up a content entry by its id.
     * Cached.
     *
     * @param int $id
     * @return null|LanguageModel
     */
    public function findById($id)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->where('id',  $id)
            ->first();
    }

    /**
     * Returns active / available for this organization.
     * Cached.
     *
     * @return Collection|LanguageModel[]
     */
    public function available()
    {
        return $this->cachedQuery()
            ->join('cms_organization_language', 'cms_organization_language.language', '=', 'cms_language.id')
            ->where('cms_organization_language.organization', config('aalberts.organization'))
            ->get();
    }
    
    /**
     * Returns default for this organization.
     * Cached.
     *
     * @return null|LanguageModel
     */
    public function defaultAvailable()
    {
        return $this->cachedQuery()
            ->join('cms_organization_language', 'cms_organization_language.language', '=', 'cms_language.id')
            ->where('cms_organization_language.organization', config('aalberts.organization'))
            ->where('cms_organization_language.default', true)
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
            'countries' => $this->eagerLoadCachedCallable(null, [ CacheTags::COUNTRY ]),
        ];
    }

}
