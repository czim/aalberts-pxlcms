<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\Content as ContentModel;

class ContentRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::CONTENT ];

    /**
     * @return string
     */
    public function model()
    {
        return ContentModel::class;
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
        return $this->query()
            ->where('label', $label)
            ->remember($this->defaultTtl())
            ->cacheTags($this->cacheTags())
            ->first();
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
    public function getBySlug($slug, $page = null, $locale = null)
    {
        $query = $this->query()
            ->whereHas('translations', function ($query) use ($slug, $locale) {
                return $query->where('language', $this->languageIdForLocale($locale))
                             ->where('slug', $slug);
            });

        if (null !== $page) {
            $query->where('page', (bool) $page);
        }

        return $query
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
    public function getPageBySlug($slug, $locale = null)
    {
        return $this->getBySlug($slug, true, $locale);
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
            'translations'                          => $this->eagerLoadCachedCallable(),
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
            'relatedproducts' => $this->eagerLoadCachedCallable(null [ CacheTags::PRODUCT ]),
            'contents'        => $this->eagerLoadCachedCallable(null, [ CacheTags::CONTENT ]),
        ];
    }

}
