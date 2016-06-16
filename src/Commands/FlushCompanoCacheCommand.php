<?php
namespace Aalberts\Commands;

use Aalberts\Enums\CacheTags;
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
            CacheTags::CMP_PRODUCT,
            CacheTags::CMP_SUPPLIER,
            CacheTags::CMP_MISC,

            CacheTags::TOP_PRODUCT,
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
                    new CacheTags($type);
                } catch (\Exception $e) {
                    $this->error("Invalid cache type: '{$type}'");
                    die;
                }
                return [ $type ];
        }
    }
}
