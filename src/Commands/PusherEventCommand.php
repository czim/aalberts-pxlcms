<?php
namespace Aalberts\Commands;

use Artisan;
use Illuminate\Console\Command;

class PusherEventCommand extends Command
{
    protected $signature   = 'aalberts:event {scope} {event} {type} {data?}';
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

    /**
     * Decodes the command line data argument
     *
     * @param string $data
     * @return array
     */
    protected function decodeData($data)
    {
        if (empty($data)) return [];

        return json_decode(base64_decode($data), true);
    }

}
