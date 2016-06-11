<?php
namespace Aalberts\Commands;

use Aalberts\Contracts\TranslatorInterface;
use Illuminate\Console\Command;

class FlushTranslationsCommand extends Command
{

    protected $signature   = 'aalberts:flush:translations';
    protected $description = 'Flushes the Aalberts CMS translations cache. Warning: this will NOT refresh the cache!';
    
    public function handle()
    {
        /** @var TranslatorInterface $translator */
        $translator = app('aalberts-translate');

        $translator->flushCache();

        $this->info('Flushed translations cache.');
    }

}
