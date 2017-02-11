<?php
namespace Aalberts\Contracts\Filters;

use Aalberts\Repositories\Compano\AbstractCompanoRepository;
use Czim\Repository\Contracts\BaseRepositoryInterface;

interface FilterDecorationStrategyInterface
{

    /**
     * Constructs the strategy instance using the countable result for this filter.
     *
     * @param mixed $counts     the countable results
     * @param array $selected   the value(s) currently selected
     */
    public function __construct($counts = null, $selected = null);

    /**
     * Sets the repository to use for retrieving the display labels for the filter.
     *
     * @param BaseRepositoryInterface|AbstractCompanoRepository $repository
     * @return $this
     */
    public function setDisplayModelRepository(BaseRepositoryInterface $repository);

    /**
     * Returns the view type (and partial name) for the filter.
     *
     * @return string
     */
    public function getViewType();

    /**
     * Returns the view data to use in the view partial.
     *
     * @return array
     */
    public function getViewData();

}
