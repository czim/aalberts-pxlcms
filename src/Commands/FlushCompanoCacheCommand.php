<?php
namespace Aalberts\Commands;

use Aalberts\Enums\CacheTags;
use Cache;
use Illuminate\Console\Command;

class FlushCompanoCacheCommand extends Command
{

    protected $signature   = 'aalberts:flush:cmp';
    protected $description = 'Flushes the Aalberts Compano content cache. Warning: this will NOT refresh the cache!';
    
    public function handle()
    {
        Cache::tags([
            CacheTags::CMP_PRODUCT,
            CacheTags::CMP_SUPPLIER,
            CacheTags::CMP_MISC,
            
            CacheTags::TOP_PRODUCT,
        ])->flush();

        $this->info('Flushed Compano content cache.');
    }

}
