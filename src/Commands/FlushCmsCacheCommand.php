<?php
namespace Aalberts\Commands;

use Aalberts\Enums\CacheTags;
use Cache;
use Illuminate\Console\Command;

class FlushCmsCacheCommand extends Command
{

    protected $signature   = 'aalberts:flush:cms';
    protected $description = 'Flushes the Aalberts CMS content cache. Warning: this will NOT refresh the cache!';
    
    public function handle()
    {
        Cache::tags([
            CacheTags::CONTENT,
            CacheTags::NEWS,
            CacheTags::PROJECT,
            CacheTags::DOWNLOAD,
        ])->flush();

        $this->info('Flushed CMS content cache.');
    }

}
