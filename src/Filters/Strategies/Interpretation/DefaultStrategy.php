<?php
namespace Aalberts\Filters\Strategies\Interpretation;

use Aalberts\Contracts\Filters\FilterInterpretationStrategyInterface;

/**
 * Class DefaultStrategy
 *
 * Interprets data as an array of ID integers.
 */
class DefaultStrategy implements FilterInterpretationStrategyInterface
{

    /**
     * Interprets and normalizes filter input data.
     *
     * @param mixed $data
     * @return mixed
     */
    public function interpret($data)
    {
        if ( ! is_array($data)) {
            if ( ! empty($data)) {
                return [ (int) $data ];
            }

            return [];
        }

        return array_map('intval', array_keys($data));
    }
}
