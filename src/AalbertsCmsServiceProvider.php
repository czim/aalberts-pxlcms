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
    }

    /**
     * Register the package console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        $this->registerPxlCmsGenerate();
        $this->commands([
            'aalberts.generate'
        ]);
    }

    /**
     * Register the generate command with the container.
     *
     * @return void
     */
    protected function registerPxlCmsGenerate()
    {
        $this->app->singleton('aalberts.generate', function() {
            return new GenerateCommand;
        });
    }
}
