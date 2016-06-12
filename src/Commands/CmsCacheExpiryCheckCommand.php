<?php
namespace Aalberts\Commands;

use Aalberts\Repositories\Cache\CmsCacheChecker;
use Illuminate\Console\Command;

/**
 * Class CmsCacheExpiryCheckCommand
 *
 * Checks all CMS content (not Compano) for updates and
 * expires the cache for any affected tags.
 */
class CmsCacheExpiryCheckCommand extends Command
{
    protected $signature   = 'aalberts:cache:cms:check';
    protected $description = 'Checks and expires cache for Aalberts CMS data.';
    

    public function handle()
    {
        $cacheChecker = new CmsCacheChecker();
        
        if ($cacheChecker->checkNews()) $this->comment('News cache flushed.');
        if ($cacheChecker->checkContent()) $this->comment('Content cache flushed.');
        if ($cacheChecker->checkProjects()) $this->comment('Projects cache flushed.');
        if ($cacheChecker->checkDownloads()) $this->comment('Downloads cache flushed.');
        
        
        $this->info('Checked CMS cache expiry.');
    }

}
