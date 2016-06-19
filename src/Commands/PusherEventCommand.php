<?php
namespace Aalberts\Commands;

use Artisan;
use Illuminate\Console\Command;

class PusherEventCommand extends Command
{
    protected $signature   = 'aalberts:event {channel} {type} {data?}';
    protected $description = 'Handles aalberts pusher events.';
    
    public function handle()
    {
        Artisan::call('aalberts:flush:cms', [ 'type' => $this->getCacheType() ]);

        $this->info('Called CMS flush command.');
    }

    protected function getCacheType()
    {
        return $this->argument('type');
    }

}