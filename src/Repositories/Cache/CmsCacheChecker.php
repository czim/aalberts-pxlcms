<?php
namespace Aalberts\Repositories\Cache;

use Aalberts\Enums\CacheTags;
use App\Models\Aalberts\Cms as CmsModels;
use Cache;
use Czim\PxlCms\Models\Scopes\OnlyActiveScope;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use DateTime;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CmsCacheChecker
 *
 * For checking CMS updates and expiring cache as needed.
 * Note that this depends on previous checks; if the Cache has no previous updated time,
 * it will always expire the cache to be on the safe side. This means that, in any
 * 'fresh' situation, cache may be unnecessarily cleared. Keep this in mind when
 * setting up pre-caching.
 */
class CmsCacheChecker
{
    const CACHE_UPDATE_KEY = 'aalberts-cms-update:';


    public function checkNews()
    {
        return $this->expireCacheIfUpdated(CacheTags::NEWS);
    }

    public function checkProjects()
    {
        return $this->expireCacheIfUpdated(CacheTags::PROJECT);
    }

    public function checkDownloads()
    {
        return $this->expireCacheIfUpdated(CacheTags::DOWNLOAD);
    }

    public function checkContent()
    {
        return $this->expireCacheIfUpdated(CacheTags::CONTENT);
    }


    /**
     * Checks for updates and expires cache if required
     *
     * @param string $type
     * @return bool
     */
    protected function expireCacheIfUpdated($type)
    {
        if ( ! $this->checkForUpdates($type)) return false;

        Cache::tags([ $type ])->flush();
        $this->markCacheUpdateTime($type);

        return true;
    }

    /**
     * Checks and returns whether there have been updates for the type since
     * the cache was last checked.
     *
     * @param string $type
     * @return bool
     */
    protected function checkForUpdates($type)
    {
        $lastCheckTime = $this->getCacheUpdateTime($type);
        if (null == $lastCheckTime) return true;

        $updateTime = $this->getLatestUpdateTime($type);
        if ( ! $updateTime) return true;

        return ($updateTime > $lastCheckTime);
    }

    /**
     * Gets the most recent modified/update time for a given type
     *
     * @param string $type
     * @return DateTime|null
     */
    protected function getLatestUpdateTime($type)
    {
        switch ($type) {

            case CacheTags::NEWS:
                $times = [
                    $this->getLatestTimeFromQuery(CmsModels\News::query()),
                    $this->getLatestTimeFromQuery(CmsModels\NewsGallery::query()),
                    $this->getLatestTimeFromQuery(CmsModels\NewsGalleryImage::query(), 'createdts'),
                ];
                break;

            case CacheTags::PROJECT:
                $times = [
                    $this->getLatestTimeFromQuery(CmsModels\Project::query()),
                    $this->getLatestTimeFromQuery(CmsModels\ProjectGallery::query()),
                    $this->getLatestTimeFromQuery(CmsModels\ProjectGalleryImage::query(), 'createdts'),
                    $this->getLatestTimeFromQuery(CmsModels\ProjectImage::query(), 'createdts'),
                ];
                break;

            case CacheTags::DOWNLOAD:
                $times = [
                    $this->getLatestTimeFromQuery(CmsModels\Download::query()),
                    $this->getLatestTimeFromQuery(CmsModels\DownloadFile::query(), 'createdts'),
                    $this->getLatestTimeFromQuery(CmsModels\DownloadImage::query(), 'createdts'),
                ];
                break;

            case CacheTags::CONTENT:
                $times = [
                    $this->getLatestTimeFromQuery(CmsModels\Content::query()),
                    $this->getLatestTimeFromQuery(CmsModels\ContentGallery::query(), 'createdts'),
                    $this->getLatestTimeFromQuery(CmsModels\ContentGalleryImage::query(), 'createdts'),
                ];
                break;

            default:
                throw new \UnexpectedValueException("No update time retrievable for type '{$type}'");
        }

        // remove any null values and normalize
        $times = array_filter($times);

        return max($times);
    }

    /**
     * Given a query builder, returns the latest time for a given date column
     *
     * @param mixed|Builder $query
     * @param string        $column
     * @return DateTime|null
     */
    protected function getLatestTimeFromQuery($query, $column = 'modifiedts')
    {
        $record = $query
            ->withoutGlobalScopes([ PositionOrderedScope::class, OnlyActiveScope::class ])
            ->orderBy($column, 'desc')
            ->take(1)
            ->first([ $column ]);

        if ( ! $record) return null;

        return $record[ $column ];
    }

    /**
     * Returns the last update check time.
     *
     * @param string $type
     * @return null|DateTime
     */
    protected function getCacheUpdateTime($type)
    {
        return Cache::get($this->getCacheUpdateKey($type));
    }

    /**
     * Marks the current time as the last update check time.
     *
     * @param string $type
     */
    protected function markCacheUpdateTime($type)
    {
        Cache::forever($this->getCacheUpdateKey($type), new DateTime());
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getCacheUpdateKey($type)
    {
        return static::CACHE_UPDATE_KEY . $type;
    }

}
