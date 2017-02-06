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
     * Constructs the strategy instance using the countable result for this filter.
     *
     * @param mixed $counts
     */
    public function __construct($counts = null)
    {
        $this->counts = $counts;

        $this->initialize();
    }

    /**
     * Initializes the strategy for viewing.
     */
    protected function initialize()
    {
        // todo
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
