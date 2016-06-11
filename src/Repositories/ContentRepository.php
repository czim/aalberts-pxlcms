<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms\Content;

class ContentRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTags::CONTENT ];

    /**
     * @return string
     */
    public function model()
    {
        return Content::class;
    }

    /**
     * Returns a record by label.
     * Cached.
     *
     * @param string $label
     * @return null|Content
     */
    public function findByLabel($label)
    {
        return $this->where('label', $label)
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
     * @return null|Content
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
     * @return Content|null
     */
    public function getPageBySlug($slug, $locale = null)
    {
        return $this->getBySlug($slug, true, $locale);
    }

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
