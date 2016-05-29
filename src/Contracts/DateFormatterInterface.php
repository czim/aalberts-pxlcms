<?php
namespace Aalberts\Contracts;

use DateTime;

interface DateFormatterInterface
{

    /**
     * Formats a date to a string
     *
     * @param DateTime $date
     * @return string
     */
    public function format(DateTime $date);

}
