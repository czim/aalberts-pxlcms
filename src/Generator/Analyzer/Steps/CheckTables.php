<?php
namespace Aalberts\Generator\Analyzer\Steps;


use Aalberts\Generator\Analyzer\AnalyzerContext;
use DB;

class CheckTables extends \Czim\PxlCms\Generator\Analyzer\Steps\CheckTables
{
    /**
     * @var AnalyzerContext
     */
    protected $context;

    /**
     * List of columns, keyed by table name
     *
     * @var array
     */
    protected $tableColumns;


    protected function process()
    {
        $this->context->slugStructurePresent = false;

        $this->loadTableList();
        $this->loadColumnsPerTable();

        $this->context->tableColumns = $this->tableColumns;
    }


    /**
     * Returns the column names for a table
     *
     * @param string $table
     * @return array
     */
    protected function loadColumnListForTable($table)
    {
        if ($this->getDatabaseDriver() === 'sqlite') {
            $statement = "PRAGMA table_info(`{$table}`)";
        } else {
            $statement = "SHOW columns FROM `{$table}`";
        }

        $columnResults = DB::select($statement);
        $columns       = [];

        foreach ($columnResults as $columnObject) {

            $columns[] = (array) $columnObject;
        }

        return $columns;
    }

    protected function loadColumnsPerTable()
    {
        $this->tableColumns = [];

        foreach ($this->tables as $table) {

            $this->tableColumns[ $table ] = $this->loadColumnListForTable($table);
        }
    }

}

