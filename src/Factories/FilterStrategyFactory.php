<?php
namespace Aalberts\Factories;

use Aalberts\Contracts\Filters\FilterDecorationStrategyInterface;
use Aalberts\Contracts\Filters\FilterInterpretationStrategyInterface;
use Aalberts\Filters\Strategies\Decoration;
use Aalberts\Filters\Strategies\Interpretation;
use Aalberts\Repositories\Compano as CompanoRepositories;

class FilterStrategyFactory
{

    /**
     * @param string $slug
     * @return FilterInterpretationStrategyInterface
     */
    public function makeInterpreter($slug)
    {
        switch ($slug) {

            // Default is an array with key value pairs, where the values are just boolean true
            default:
                return new Interpretation\DefaultStrategy();
        }
    }

    /**
     * @param string $slug
     * @param mixed  $counts
     * @param mixed  $data
     * @return FilterDecorationStrategyInterface
     */
    public function makeDecorator($slug, $counts, $data)
    {
        $instance = new Decoration\DefaultStrategy($counts, $data);

        switch ($slug) {

            case 'productline':
                $repository = CompanoRepositories\ProductLineRepository::class;
                break;

            default:
                $repository = false;
        }

        if ($repository) {
            $instance->setDisplayModelRepository(app($repository));
        }

        return $instance;
    }

}
