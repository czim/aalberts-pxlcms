<?php
namespace Aalberts\Filters\Strategies\Product;

/**
 * Class DefaultStrategy
 *
 * The default strategy for setting up a filter.
 * This handles a checkbox-based multi-select filter.
 */
class DefaultStrategy
{

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
     * Constructs the strategy instance using the countable result for this filter.
     *
     * @param mixed $counts     the countable results
     * @param array $selected   the value(s) currently selected
     */
    public function __construct($counts = null, $selected = null)
    {
        $this->counts   = $counts;
        $this->selected = $selected;

        $this->initialize();
    }

    /**
     * Initializes the strategy for viewing.
     */
    protected function initialize()
    {
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
        // Sort them so that they are ordered by count, with the already selected filters first
        $this->data['options'] = $this->counts;
        $this->data['options'] = array_sort(
            $this->data['options'],
            function($count, $id) {
                return (in_array($id, $this->selected) ? 0 : 1)
                     + (1 / $count);
            }
        );

        $this->data['options'] = array_map(
            function ($id, $count) {
                return [
                    'id'    => $id,
                    'count' => $count,
                    'label' => $id, // will be replaced
                ];
            },
            array_keys($this->data['options']),
            array_values($this->data['options'])
        );

        // For the ids present, get the display values for each option
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
        return [];
    }

}
