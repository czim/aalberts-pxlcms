<?php
namespace Aalberts\Contracts\Filters;

interface FilterInterpretationStrategyInterface
{

    /**
     * Interprets and normalizes filter input data.
     *
     * @param mixed $data
     * @return mixed
     */
    public function interpret($data);

}
