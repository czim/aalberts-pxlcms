<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Cms\Content as ContentModel;
use App\Models\Aalberts\Cms\Content;
use Czim\Repository\Criteria\Common\FieldIsValue;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Support\Collection;

class ContentRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CONTENT ];

    public function model()
    {
        return ContentModel::class;
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
     * Returns a record by label.
     * Cached.
     *
     * @param string $label
     * @return null|ContentModel
     */
    public function findByLabel($label)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->where('label', $label)
            ->first();
    }

    /**
     * Returns the children for a content parent.
     * Cached.
     *
     * @param ContentModel $parent
     * @param int          $depth maximum depth to check, 1 = only direct children
     * @return Collection|ContentModel[]
     */
    public function getChildren(ContentModel $parent, $depth = 3)
    {
        // build with relations 'tree' parameter
        $childrenWith = [];

        for ($x = 1; $x <= $depth; $x++) {
            $childrenWith[ rtrim(str_repeat('children.', $x), '.') ] = $this->eagerLoadCachedCallable();
        }

        if ($depth > 0) {
            $this->pushCriteriaOnce(new WithRelations(
                array_merge($this->withBase(), $childrenWith)
            ), CriteriaKey::WITH);
        }

        $children = $this->cachedQuery()
            ->where('page', true)
            ->where('parent', $parent->id)
            ->get();

        return $children;
    }


    /**
     * Looks up a content entry by its translated slug.
     * Cached.
     *
     * @param string                 $slug
     * @param int|Content|null|false $parent    force check for children of a given parent; false to force top-level
     * @param null|bool              $page      if a boolean, whether to forge a page = t/f check
     * @param null|string            $locale
     * @return null|ContentModel
     */
    public function findBySlug($slug, $parent = null, $page = null, $locale = null)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        if (null !== $page) {
            $this->pushCriteriaOnce(new FieldIsValue('page', (bool) $page));
        }

        $query = $this->cachedQuery()
            ->whereHas('translations', function ($query) use ($slug, $locale) {
                return $query->where('language', $this->languageIdForLocale($locale))
                    ->where('slug', $slug);
            });

        if (false === $parent) {
            $query->where('parent', 0);
        } elseif (null !== $parent) {
            $parent = $parent instanceof Content ? $parent->getKey() : $parent;
            $query->where('parent', $parent);
        }

        return $query->first();
    }

    /**
     * Looks up a content entry, that is a page, by its translated slug.
     * Cached.
     *
     * @param string                 $slug
     * @param int|Content|null|false $parent    force check for children of a given parent; false to force top-level
     * @param null|string            $locale
     * @return ContentModel|null
     */
    public function findPageBySlug($slug, $parent = null, $locale = null)
    {
        return $this->findBySlug($slug, $parent, true, $locale);
    }

    /**
     * @param string   $term
     * @param null|int $count   limit results
     * @return ContentModel|Collection
     */
    public function search($term, $count = null)
    {
        $query = $this->query()
            ->whereHas('translations', function($query) use ($term) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('language', $this->languageIdForLocale())
                    ->where(function($query) use ($term) {
                        /** @var \Illuminate\Database\Eloquent\Builder $query */
                        $query->where('title', 'like', '%' . $term . '%')
                              ->orWhere('content', 'like', '%' . $term . '%');
                    });
            });

        if (null !== $count) {
            $query->take($count);
        }

        return $query->get();
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
        return [
            'translations'                          => $this->eagerLoadCachedTranslationCallable(),
            'parent'                                => $this->eagerLoadCachedCallable(),
            'children'                              => $this->eagerLoadCachedCallable(),
            'contentGalleries'                      => $this->eagerLoadCachedCallable(),
            'contentGalleries.translations'         => $this->eagerLoadCachedCallable(),
            'contentGalleries.contentGalleryImages' => $this->eagerLoadCachedCallable(),
        ];
    }

    /**
     * Returns with parameter array to use for detail page
     *
     * @return array
     */
    protected function withDetail()
    {
        return [
            'relatedproducts'                      => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'relatedproducts.translations'         => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),
            'relatedproducts.relatedproductImages' => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_PRODUCT]),

            'news'              => $this->eagerLoadCachedCallable(null, [CacheTag::NEWS]),
            'news.translations' => $this->eagerLoadCachedCallable(null, [CacheTag::NEWS]),

            'projects'                                       => $this->eagerLoadCachedCallable(null, [CacheTag::PROJECT]),
            'projects.translations'                          => $this->eagerLoadCachedCallable(null, [CacheTag::PROJECT]),
            'projects.projectGalleries'                      => $this->eagerLoadCachedCallable(null, [CacheTag::PROJECT]),
            'projects.projectGalleries.projectGalleryImages' => $this->eagerLoadCachedCallable(null, [CacheTag::PROJECT]),

            'downloads'               => $this->eagerLoadCachedCallable(null, [CacheTag::DOWNLOAD]),
            'downloads.translations'  => $this->eagerLoadCachedCallable(null, [CacheTag::DOWNLOAD]),
            'downloads.downloadFiles' => $this->eagerLoadCachedCallable(null, [CacheTag::DOWNLOAD]),

            'applications'    => $this->eagerLoadCachedCallable(null, [CacheTag::APPLICATION]),
            'solutions'       => $this->eagerLoadCachedCallable(null, [CacheTag::SOLUTION]),
            'functions'       => $this->eagerLoadCachedCallable(null, [CacheTag::CMS_FUNCTION]),
        ];
    }

}
