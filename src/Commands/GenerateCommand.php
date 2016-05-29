<?php
namespace Aalberts\Commands;

use Aalberts\Generator\ModelGenerator;
use Czim\PxlCms\Generator\Generator;
use Illuminate\Console\Command;
use Event;

class GenerateCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aalberts:generate
                                {--dry-run : Analyzes and shows debug output, but does not write files }
                                {--auto : Disable user interaction }
                                {--models-only : Only generates models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate app files based on Aalberts CMS database content.';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelsOnly  = (bool) $this->option('models-only');
        $dryRun      = (bool) $this->option('dry-run');
        $interactive = ! (bool) $this->option('auto');

        $this->listenForLogEvents();

        $generator = new ModelGenerator( ! $dryRun, $this);

        $generator->generate();

        $this->info('Done.');
    }

    protected function listenForLogEvents()
    {
        $verbose = $this->option('verbose');

        Event::listen('pxlcms.logmessage', function($message, $level) use ($verbose) {

            switch ($level) {

                case Generator::LOG_LEVEL_DEBUG:
                    if ($verbose) {
                        $this->line($message);
                    }
                    break;

                case Generator::LOG_LEVEL_WARNING:
                    $this->comment($message);
                    break;

                case Generator::LOG_LEVEL_ERROR:
                    $this->error($message);
                    break;

                case Generator::LOG_LEVEL_INFO:
                default:
                    $this->info($message);
            }
        });
    }
}
