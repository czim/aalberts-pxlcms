<?php
namespace Aalberts\Commands;

use Aalberts\Contracts\TranslatorInterface;
use Illuminate\Console\Command;

class CacheTranslationsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aalberts:cache:translations
                                {--force : Forces caching even if the update check is negative }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache relevant Aalberts CMS translations.';
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var TranslatorInterface $translator */
        $translator = app('aalberts-translate');

        if ( ! $this->option('force') && ! $translator->checkForUpdates()) {
            $this->info('Keeping old translations cache.');
            return;
        }

        $translator->cacheTranslations();

        $this->info('Cached translations.');
    }

}
