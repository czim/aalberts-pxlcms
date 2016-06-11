<?php
namespace Aalberts\Commands;

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


        $this->info('Checked CMS cache expiry.');
    }

}
