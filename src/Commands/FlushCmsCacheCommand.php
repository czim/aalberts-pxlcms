<?php
namespace Aalberts\Commands;

use Aalberts\Enums\CacheTag;
use Cache;
use Illuminate\Console\Command;

class FlushCmsCacheCommand extends Command
{
    protected $signature   = 'aalberts:flush:cms {type?}';
    protected $description = 'Flushes the Aalberts CMS content cache.';
    
    public function handle()
    {
        $tags = $this->getRelevantTags();

        Cache::tags($tags)->flush();

        $this->info(
            'Flushed CMS content cache.'
            . (count($tags) ? ' Tags: ' . implode(', ', $tags) : null)
        );
    }

    protected function getRelevantTags()
    {
        $tags = $this->getRelevantTagsForType($this->argument('type'));

        return count($tags) ? $tags : $this->getAllTags();
    }

    protected function getAllTags()
    {
        return [
            CacheTag::CONTENT,
            CacheTag::NEWS,
            CacheTag::PROJECT,
            CacheTag::DOWNLOAD,
        ];
    }

    /**
     * @param string $type
     * @return string[]
     */
    protected function getRelevantTagsForType($type)
    {
        if ( ! $type) {
            return [];
        }

        $type = strtolower($type);

        switch ($type) {

            // exceptions, combinations of cache tags based on the updated type

            // unknown, just clear the entire cache
            case 'unknown':
                return [];

            // default is to treat the type, if we can, as a tag
            default:
                try {
                    new CacheTag($type);
                } catch (\Exception $e) {
                    $this->error("Invalid cache type: '{$type}'");
                    die;
                }
                return [ $type ];
        }
    }
}
