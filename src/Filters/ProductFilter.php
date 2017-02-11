<?php
namespace Aalberts\Filters;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Product;
use Czim\Filter\ParameterFilters as CzimParameterFilters;
use Czim\PxlCms\Models\Scopes\PositionOrderedScope;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class ProductFilter extends AbstractFilter
{
    protected $filterDataClass = ProductFilterData::class;
    protected $table = 'cmp_product';

    protected $countables = [
        'productline',
        //'producttype',
    ];

    protected $includeSelfInCount = [
        'productline',
    ];

    /**
     * List of countable keys, keyed by compano filter slug.
     *
     * @var string[]    associative, keyed by slug
     */
    protected $countableKeySlugMap = [
        'productline' => 'productline',
        'producttype' => 'producttype',
        // todo
    ];

    protected function strategies()
    {
        return [
            'has_label'        => new CzimParameterFilters\NotEmpty($this->table, 'label'),
            'for_organization' => new ParameterFilters\ProductsForOrganization(),
            'productgroup'     => new ParameterFilters\Product\ProductgroupParameter(),
            'productline'      => new ParameterFilters\Product\ProductlineParameter(),
            // todo
        ];
    }

    protected function countStrategies()
    {
        return [
            'productgroup' => new ParameterCounters\Product\ProductgroupCounter(),
            'productline'  => new ParameterCounters\Product\ProductlineCounter(),
            // todo
        ];
    }

    /**
     * Whether the current processing is done for countable queries.
     *
     * @var bool
     */
    protected $isQueryForCountable = false;


    /**
     * {@inheritdoc}
     */
    public function getCountableBaseQuery($parameter = null)
    {
        return Product::query()
            ->remember($this->defaultTtl())
            ->cacheTags([CacheTag::CMP_PRODUCT]);
    }

    /**
     * {@inheritdoc}
     */
    public function apply($query)
    {
        $this->applyBaseQuery($query);

        parent::apply($query);
    }

    /**
     * @inheritdoc
     */
    protected function applyParameter($name, $value, $query)
    {
        switch ($name) {

            // Handle sorting order and direction
            case 'order':
                if ( ! $value || $this->isQueryForCountable) return;

                if (false !== strpos($value, ':')) {
                    list($column, $direction) = explode(':', $value);
                } else {
                    $column    = $value;
                    $direction = null;
                }

                switch ($column) {

                    case 'groupcode':
                        $query->orderBy('cmp_product.groupcode', $direction === 'desc' ? 'desc' : 'asc');
                        break;
                }
                return;

            // Default omitted on purpose
        }

        parent::applyParameter($name, $value, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function getCounts($countables = [])
    {
        $this->isQueryForCountable = true;

        $counts = parent::getCounts($countables);

        $this->isQueryForCountable = false;

        return $counts;
    }

    // ------------------------------------------------------------------------------
    //      Modify base query
    // ------------------------------------------------------------------------------

    /**
     * Applies base query setup for productfilter.
     *
     * @param Builder|EloquentBuilder $query
     */
    protected function applyBaseQuery($query)
    {
        $query->withoutGlobalScope(PositionOrderedScope::class);
        $this->applyItemJoin($query);
    }

    /**
     * Applies the standard cmp_item join on the query.
     *
     * This is required for all sensible cmp_product filtering.
     *
     * @param Builder|EloquentBuilder $query
     */
    protected function applyItemJoin($query)
    {
        $query
            ->select(['cmp_product.*'])
            ->join('cmp_item', 'cmp_item.product', '=', 'cmp_product.id')
            ->where('cmp_item.salesorganizationcode', config('aalberts.salesorganizationcode'));

        if ( ! $this->isQueryForCountable) {
            $query->groupBy('cmp_product.id');
        }


        if (config('aalberts.queries.uses-is-webitem')) {
            $query->where('cmp_item.iswebitem', '=', true);
        }
    }

    // ------------------------------------------------------------------------------
    //      Countable mapping
    // ------------------------------------------------------------------------------

    /**
     * Returns countable keys for a list of filter slugs.
     *
     * @param string[] $slugs
     * @return string[]
     */
    public function getCountablesForFilterSlugs(array $slugs)
    {
        return array_values(array_only($this->countableKeySlugMap, $slugs));
    }
    
}
