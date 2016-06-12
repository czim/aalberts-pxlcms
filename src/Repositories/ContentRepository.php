<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\Content as ContentModel;
use Czim\Repository\Criteria\Common\FieldIsValue;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Support\Collection;

class ContentRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::CONTENT ];

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

        return $this->query()
            ->where('label', $label)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
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

        $children = $this->query()
            ->where('page', true)
            ->where('parent', $parent->id)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->get();

        return $children;
    }


    /**
     * Looks up a content entry by its translated slug.
     * Cached.
     *
     * @param string      $slug
     * @param null|bool   $page     if a boolean, whether to forge a page = t/f check
     * @param null|string $locale
     * @return null|ContentModel
     */
    public function findBySlug($slug, $page = null, $locale = null)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        if (null !== $page) {
            $this->pushCriteriaOnce(new FieldIsValue('page', (bool) $page));
        }

        return $this->query()
            ->whereHas('translations', function ($query) use ($slug, $locale) {
                return $query->where('language', $this->languageIdForLocale($locale))
                             ->where('slug', $slug);
            })
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->first();
    }

    /**
     * Looks up a content entry, that is a page, by its translated slug.
     * Cached.
     *
     * @param string      $slug
     * @param null|string $locale
     * @return ContentModel|null
     */
    public function findPageBySlug($slug, $locale = null)
    {
        return $this->findBySlug($slug, true, $locale);
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
            'contentGalleries'                      => $this->eagerLoadCachedCallable(),
            'contentGalleries.contentGalleryImages' => $this->eagerLoadCachedCallable(),
            'translations'                          => $this->eagerLoadCachedTranslationCallable(),
            'parent'                                => $this->eagerLoadCachedCallable(),
            'children'                              => $this->eagerLoadCachedCallable(),
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
            'relatedproducts' => $this->eagerLoadCachedCallable(null, [ CacheTags::PRODUCT ]),
        ];
    }

}
