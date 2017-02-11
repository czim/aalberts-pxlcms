<?php
namespace Aalberts\Filters\Strategies\Decoration;

use Aalberts\Contracts\Filters\FilterDecorationStrategyInterface;
use Aalberts\Repositories\Compano\AbstractCompanoRepository;
use Czim\Repository\Contracts\BaseRepositoryInterface;

/**
 * Class DefaultStrategy
 *
 * The default strategy for setting up a filter.
 * This handles a checkbox-based multi-select filter.
 */
class DefaultStrategy implements FilterDecorationStrategyInterface
{

    /**
     * Whether the data has been prepared.
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * Countable results.
     *
     * @var mixed
     */
    protected $counts;

    /**
     * Values currently selected.
     *
     * @var mixed
     */
    protected $selected;

    /**
     * Prepared view data.
     *
     * @var mixed
     */
    protected $data = [];

    /**
     * @var AbstractCompanoRepository|null
     */
    protected $repository;

    /**
     * Constructs the strategy instance using the countable result for this filter.
     *
     * @param mixed $counts     the countable results
     * @param array $selected   the value(s) currently selected
     */
    public function __construct($counts = null, $selected = null)
    {
        $this->counts   = $counts;
        $this->selected = $selected;
    }

    /**
     * Sets the repository to use for retrieving the display labels for the filter.
     *
     * @param BaseRepositoryInterface|AbstractCompanoRepository $repository
     * @return $this
     */
    public function setDisplayModelRepository(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Initializes the strategy for viewing.
     */
    protected function initialize()
    {
        if ($this->initialized) {
            return;
        }

        // Make sure this key is always present; for checking whether the accordion/filter choices should be opened
        // It should be opened when the filter is a 'main' filter type, or when anything has been selected
        $this->data['start-open'] = false;

        // Normalize counts & selected values
        $this->counts = $this->counts ?: [];

        if ( ! is_array($this->selected)) {
            if (empty($this->selected)) {
                $this->selected = [];
            } else {
                $this->selected = [ $this->selected ];
            }
        }

        // Get a list of filter object identifiers, and sort them correctly
        $this->data['options'] = array_map(
            function ($id, $count) {

                $selected = in_array($id, $this->selected);

                if ($selected) {
                    $this->data['start-open'] = true;
                }

                return [
                    'id'       => $id,
                    'count'    => $count,
                    'selected' => $selected,
                    'label'    => $id, // will be replaced
                ];
            },
            array_keys($this->counts),
            array_values($this->counts)
        );

        // For the ids present, get the display values for each option
        $this->decorateOptionsWithDisplayLabels($this->data['options']);

        // Sort them so that they are ordered by count, with the already selected filters first
        $this->data['options'] = array_sort(
            $this->data['options'],
            function($option) {
                return ($option['selected'] ? 0 : 1)
                     + (1 / $option['count'])
                     . ':' . $option['label'];
            }
        );
    }

    /**
     * Decorates a view data array with display labels.
     *
     * @param array $options    by reference
     */
    protected function decorateOptionsWithDisplayLabels(array &$options)
    {
        $labels = $this->getDisplayLabels();

        foreach ($options as &$option) {

            if ( ! array_key_exists($option['id'], $labels)) {
                continue;
            }

            $option['label'] = $labels[ $option['id'] ];
        }

        unset($option);
    }


    /**
     * Returns display labels keyed by id.
     *
     * @return string[]
     */
    protected function getDisplayLabels()
    {
        $repository = $this->getDisplayModelRepository();

        if ( ! $repository) {
            return [];
        }

        return $repository->getFilterDisplayLabels();
    }

    /**
     * Returns the repository from which the display labels should be retrieved.
     *
     * @return AbstractCompanoRepository
     */
    protected function getDisplayModelRepository()
    {
        return $this->repository;
    }

    /**
     * Returns the view type (and partial name) for the filter.
     *
     * @return string
     */
    public function getViewType()
    {
        return 'checkbox';
    }

    /**
     * Returns the view data to use in the view partial.
     *
     * @return array
     */
    public function getViewData()
    {
        $this->initialize();

        return $this->data;
    }

    /**
     * Returns whether the filter should be considered empty, and thus should be hidden.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! count($this->data['options']);
    }

}
