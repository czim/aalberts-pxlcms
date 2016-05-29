<?php
namespace Aalberts;

use Illuminate\Support\ServiceProvider;

class AalbertsCmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/pxlcms.php' => config_path('pxlcms.php'),
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
    }

    /**
     * Register the package console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        $this->registerGenerateCommand();

        $this->commands([
            'aalberts.generate'
        ]);
    }

    /**
     * Register the generate command with the container.
     *
     * @return void
     */
    protected function registerGenerateCommand()
    {
        $this->app->singleton('aalberts.generate', function() {
            return new Commands\GenerateCommand;
        });
    }
}
