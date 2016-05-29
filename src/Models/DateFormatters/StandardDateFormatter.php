<?php
namespace Aalberts\Models\DateFormatters;

use Aalberts\Contracts\DateFormatterInterface;
use DateTime;

class StandardDateFormatter implements DateFormatterInterface
{

    /**
     * Default month translations
     *
     * @var string[]
     */
    protected $months = [
        1  => 'jan',
        2  => 'feb',
        3  => 'mrt',
        4  => 'apr',
        5  => 'mei',
        6  => 'jun',
        7  => 'jul',
        8  => 'aug',
        9  => 'sep',
        10 => 'okt',
        11 => 'nov',
        12 => 'dec',
    ];

    /**
     * Formats a date and returns as string
     *
     * @param DateTime $date
     * @return string
     */
    public function format(DateTime $date)
    {
        return $date->format('Y') . ' '
             . $this->getMonthString($date->format('m')) . ' '
             . $date->format('d');
    }

    /**
     * Returns a string representation of the month
     *
     * @param string $month
     * @return string
     */
    protected function getMonthString($month)
    {
        // use translation
        if (    ($translationKey = config('aalberts.date.months-translate-key'))
            &&  ($translation    = trans($translationKey . '.' . $month)) !== $translationKey . '.' . $month
        ) {
            return $translation;
        }

        $month = (int) $month;

        // fall back to default translation
        if (array_key_exists($month, $this->months)) {
            return $this->months[ $month ];
        }

        return (string) $month;
    }

}
