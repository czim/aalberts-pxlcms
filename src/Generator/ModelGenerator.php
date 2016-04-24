<?php
namespace Aalberts\Generator;

use Aalberts\Generator\Writer\ModelWriter;
use Czim\PxlCms\Generator\Generator;
use Illuminate\Console\Command;

class ModelGenerator extends Generator
{

    /**
     * @param bool    $write    whether to write files; if false, just outputs analyzed data
     * @param Command $command  the console command, if it was called from console
     */
    public function __construct($write = true, Command $command = null)
    {
        parent::__construct($write, $command);

        $this->analyzer    = new Analyzer($command);
        $this->modelWriter = new ModelWriter();
        
        //$this->repositoryWriter = new RepositoryWriter();
    }

}
