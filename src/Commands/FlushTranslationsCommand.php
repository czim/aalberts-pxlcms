<?php
namespace Aalberts\Commands;

use Aalberts\Contracts\TranslatorInterface;
use Illuminate\Console\Command;

class FlushTranslationsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aalberts:flush:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flushes the Aalberts CMS translations cache. Warning: this will NOT refresh the cache!';
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var TranslatorInterface $translator */
        $translator = app('aalberts-translate');

        $translator->flushCache();

        $this->info('Flushed translations cache.');
    }

}
