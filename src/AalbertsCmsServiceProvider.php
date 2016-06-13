<?php
namespace Aalberts;

use Aalberts\Contracts\NoticesLoggerInterface;
use Aalberts\Loggers\NoticesLogger;
use Illuminate\Foundation\Application;
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
        $this->bindLoggers();
    }

    /**
     * Register the package console commands.
     */
    protected function registerConsoleCommands()
    {
        $this->registerGenerateCommand();
        $this->registerCacheTranslationsCommands();
        $this->registerCacheContentCommands();

        $this->commands([
            'aalberts.generate',
            'aalberts.cache.translations',
            'aalberts.flush.translations',
            'aalberts.cache.cms.check',
            'aalberts.flush.cms',
            'aalberts.cache.cmp.check',
            'aalberts.flush.cmp',
        ]);
    }

    protected function registerGenerateCommand()
    {
        $this->app->singleton('aalberts.generate', function() {
            return new Commands\GenerateCommand;
        });
    }

    protected function registerCacheTranslationsCommands()
    {
        $this->app->singleton('aalberts.cache.translations', function() {
            return new Commands\CacheTranslationsCommand;
        });

        $this->app->singleton('aalberts.flush.translations', function() {
            return new Commands\FlushTranslationsCommand;
        });
    }

    protected function registerCacheContentCommands()
    {
        $this->app->singleton('aalberts.cache.cms.check', function() {
            return new Commands\CmsCacheExpiryCheckCommand;
        });

        $this->app->singleton('aalberts.cache.cmp.check', function() {
            return new Commands\CompanoCacheExpiryCheckCommand;
        });

        $this->app->singleton('aalberts.flush.cms', function() {
            return new Commands\FlushCmsCacheCommand;
        });

        $this->app->singleton('aalberts.flush.cmp', function() {
            return new Commands\FlushCompanoCacheCommand;
        });
    }


    protected function bindFacades()
    {
        $this->app->singleton('aalberts-helper', function () {
            return new AalbertsHelper;
        });

        $this->app->singleton('aalberts-translate', function (Application $app) {
            return $app->make(Translation\Translator::class);
        });
    }

    protected function bindLoggers()
    {
        $this->app->singleton(NoticesLoggerInterface::class, function () {
            return new NoticesLogger();
        });
    }

}
