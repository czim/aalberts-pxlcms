<?php
namespace Aalberts\Commands;

use Aalberts\Enums\CacheTag;
use Cache;
use Illuminate\Console\Command;

class FlushCompanoCacheCommand extends Command
{

    protected $signature   = 'aalberts:flush:cmp {type?}';
    protected $description = 'Flushes the Aalberts Compano content cache.';

    public function handle()
    {
        $tags = $this->getRelevantTags();

        Cache::tags($tags)->flush();

        $this->info(
            'Flushed Compano content cache.'
            . (count($tags) ? ' Tags: ' . implode(', ', $tags) : null)
        );
    }

    protected function getRelevantTags()
    {
        $tags = $this->getRelevantTagsForType($this->argument('type'));

        return count($tags) ? $tags : $this->getAllTags();
    }

    /**
     * @return string[]
     */
    protected function getAllTags()
    {
        return [
            CacheTag::CMP_PRODUCT,
            CacheTag::CMP_SUPPLIER,
            CacheTag::CMP_MISC,

            CacheTag::TOP_PRODUCT,
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
