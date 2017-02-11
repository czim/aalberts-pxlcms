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

            case 'angleofbow':
                $repository = CompanoRepositories\AngleOfBowRepository::class;
                break;

            case 'applications':
                $repository = CompanoRepositories\ApplicationRepository::class;
                break;

            case 'approvals':
                $repository = CompanoRepositories\ApprovalRepository::class;
                break;

            case 'bowrange':
                $repository = CompanoRepositories\BowRangeRepository::class;
                break;

            case 'brand':
                $repository = CompanoRepositories\BrandRepository::class;
                break;

            case 'colors':
                $repository = CompanoRepositories\ColorRepository::class;
                break;

            case 'connectiontype':
                $repository = CompanoRepositories\ConnectionTypeRepository::class;
                break;

            case 'contourcode':
                $repository = CompanoRepositories\ContourCodeRepository::class;
                break;

            case 'externaltubediameterofconnection':
                $repository = CompanoRepositories\ExternalTubeDiameterOfConnectionRepository::class;
                break;

            case 'finishings':
                $repository = CompanoRepositories\FinishingRepository::class;
                break;

            case 'manufacturercode':
                $repository = CompanoRepositories\ManufacturerCodeRepository::class;
                break;

            case 'materialquality':
                $repository = CompanoRepositories\MaterialQualityRepository::class;
                break;

            case 'materials':
                $repository = CompanoRepositories\MaterialRepository::class;
                break;

            case 'numberofconnections':
                $repository = CompanoRepositories\NumberOfConnectionRepository::class;
                break;

            case 'productline':
                $repository = CompanoRepositories\ProductLineRepository::class;
                break;

            case 'producttype':
                $repository = CompanoRepositories\ProductTypeRepository::class;
                break;

            case 'pumpbrand':
                $repository = CompanoRepositories\PumpBrandRepository::class;
                break;

            case 'series':
                $repository = CompanoRepositories\SeriesRepository::class;
                break;

            case 'shape':
                $repository = CompanoRepositories\ShapeRepository::class;
                break;

            case 'solutions':
                $repository = CompanoRepositories\SolutionRepository::class;
                break;

            case 'type':
                $repository = CompanoRepositories\TypeRepository::class;
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
