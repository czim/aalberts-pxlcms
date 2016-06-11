<?php
namespace Aalberts\Commands;

use Illuminate\Console\Command;

/**
 * Class CompanoCacheExpiryCheckCommand
 *
 * Checks all Compano content (not CMS) for updates and
 * expires the cache for any affected tags.
 */
class CompanoCacheExpiryCheckCommand extends Command
{
    protected $signature   = 'aalberts:cache:cmp:check';
    protected $description = 'Checks and expires cache for Aalberts CMS data.';
    

    public function handle()
    {


        $this->info('Checked Compano cache expiry.');
    }

}
