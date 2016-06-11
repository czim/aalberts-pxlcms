<?php
namespace Aalberts\Commands;

use Aalberts\Contracts\TranslatorInterface;
use Illuminate\Console\Command;

class CacheTranslationsCommand extends Command
{

    protected $signature = 'aalberts:cache:translations
                                {--force : Forces caching even if the update check is negative }';

    protected $description = 'Cache relevant Aalberts CMS translations.';
    
    
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
