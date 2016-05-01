<?php
namespace Aalberts\Repositories;

use App\Models\Aalberts\Cms\Content;

class Contents extends AbstractRepository
{
    protected $translated = true;

    /**
     * @return string
     */
    public function model()
    {
        return Content::class;
    }


    /**
     * Looks up a content entry by its translated slug
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

        return $query->first();
    }

    /**
     * Looks up a content entry, that is a page, by its translated slug
     *
     * @param string      $slug
     * @param null|string $locale
     * @return Content|null
     */
    public function getPageBySlug($slug, $locale = null)
    {
        return $this->getBySlug($slug, true, $locale);
    }

}
