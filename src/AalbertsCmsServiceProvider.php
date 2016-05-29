<?php
namespace Aalberts;

use Illuminate\Support\ServiceProvider;

class AalbertsCmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/pxlcms.php'   => config_path('pxlcms.php'),
            __DIR__ . '/config/aalberts.php' => config_path('aalberts.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConsoleCommands();

        $this->mergeConfigFrom(
            __DIR__ . '/config/pxlcms.php', 'pxlcms'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/aalberts.php', 'aalberts'
        );

        $this->bindFacades();
    }

    /**
     * Register the package console commands.
     */
    protected function registerConsoleCommands()
    {
        $this->registerGenerateCommand();
        $this->registerCacheTranslationsCommand();

        $this->commands([
            'aalberts.generate',
            'aalberts.cache.translations',
            'aalberts.flush.translations',
        ]);
    }

    protected function registerGenerateCommand()
    {
        $this->app->singleton('aalberts.generate', function() {
            return new Commands\GenerateCommand;
        });
    }

    protected function registerCacheTranslationsCommand()
    {
        $this->app->singleton('aalberts.cache.translations', function() {
            return new Commands\CacheTranslationsCommand;
        });

        $this->app->singleton('aalberts.flush.translations', function() {
            return new Commands\FlushTranslationsCommand;
        });
    }


    protected function bindFacades()
    {
        $this->app->singleton('aalberts-translate', function () {
            return new Translation\Translator();
        });
    }

}
