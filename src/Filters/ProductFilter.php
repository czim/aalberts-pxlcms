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

    /**
     * If this is false, the product groupby must be done manually after
     * the filter is applied. This may be necessary to allow count() queries
     * to work.
     *
     * @var bool
     */
    protected $applyProductGroupBy = true;

    protected $countables = [
        'angleofbow',
        'applications',
        'approvals',
        'bowrange',
        'brand',
        'colors',
        'connectiontype',
        'contourcode',
        'externaltubediameterofconnection',
        'finishings',
        'manufacturercode',
        'materialquality',
        'material',
        'numberofconnections',
        'productline',
        'producttype',
        'pumpbrand',
        'series',
        'shape',
        'solutions',
        'type',
    ];

    protected $includeSelfInCount = [
        'angleofbow',
        'applications',
        'approvals',
        'bowrange',
        'brand',
        'colors',
        'connectiontype',
        'contourcode',
        'externaltubediameterofconnection',
        'finishings',
        'manufacturercode',
        'materialquality',
        'material',
        'numberofconnections',
        'productline',
        'producttype',
        'pumpbrand',
        'series',
        'shape',
        'solutions',
        'type',
    ];

    /**
     * List of countable keys, keyed by compano filter slug.
     *
     * @var string[]    associative, keyed by slug
     */
    protected $countableKeySlugMap = [
        'angleofbow'                       => 'angleofbow',
        'applications'                     => 'applications',
        'approvals'                        => 'approvals',
        'bowrange'                         => 'bowrange',
        'brand'                            => 'brand',
        'colors'                           => 'colors',
        'connectiontype'                   => 'connectiontype',
        'contourcode'                      => 'contourcode',
        'externaltubediameterofconnection' => 'externaltubediameterofconnection',
        'finishings'                       => 'finishings',
        'manufacturercode'                 => 'manufacturercode',
        'materialquality'                  => 'materialquality',
        'material'                         => 'material',
        'numberofconnections'              => 'numberofconnections',
        'productline'                      => 'productline',
        'producttype'                      => 'producttype',
        'pumpbrand'                        => 'pumpbrand',
        'series'                           => 'series',
        'shape'                            => 'shape',
        'solutions'                        => 'solutions',
        'type'                             => 'type',
    ];

    protected function strategies()
    {
        return [
            'has_label'                        => new CzimParameterFilters\NotEmpty($this->table, 'label'),
            'has_image'                        => new CzimParameterFilters\NotEmpty($this->table, 'image'),
            'ids'                              => new CzimParameterFilters\SimpleInteger($this->table, 'id'),
            'for_organization'                 => new ParameterFilters\ProductsForOrganization(),
            'productgroup'                     => new ParameterFilters\Product\ProductgroupParameter(),
            'search'                           => new ParameterFilters\ProductTextSearch(),
            'angleofbow'                       => new ParameterFilters\Product\AngleofbowParameter(),
            'applications'                     => new ParameterFilters\Product\ApplicationParameter(),
            'approvals'                        => new ParameterFilters\Product\ApprovalParameter(),
            'bowrange'                         => new ParameterFilters\Product\BowrangeParameter(),
            'brand'                            => new ParameterFilters\Product\BrandParameter(),
            'colors'                           => new ParameterFilters\Product\ColorParameter(),
            'connectiontype'                   => new ParameterFilters\Product\ConnectiontypeParameter(),
            'contourcode'                      => new ParameterFilters\Product\ContourcodeParameter(),
            'externaltubediameterofconnection' => new ParameterFilters\Product\ExternaltubediameterofconnectionParameter(),
            'finishings'                       => new ParameterFilters\Product\FinishingParameter(),
            'manufacturercode'                 => new ParameterFilters\Product\ManufacturercodeParameter(),
            'materialquality'                  => new ParameterFilters\Product\MaterialqualityParameter(),
            'materials'                        => new ParameterFilters\Product\MaterialParameter(),
            'numberofconnections'              => new ParameterFilters\Product\NumberofconnectionsParameter(),
            'productline'                      => new ParameterFilters\Product\ProductlineParameter(),
            'producttype'                      => new ParameterFilters\Product\ProducttypeParameter(),
            'pumpbrand'                        => new ParameterFilters\Product\PumpbrandParameter(),
            'series'                           => new ParameterFilters\Product\SeriesParameter(),
            'shape'                            => new ParameterFilters\Product\ShapeParameter(),
            'solutions'                        => new ParameterFilters\Product\SolutionParameter(),
            'type'                             => new ParameterFilters\Product\TypeParameter(),
        ];
    }

    protected function countStrategies()
    {
        return [
            'productgroup'                     => new ParameterCounters\Product\ProductgroupCounter(),
            'angleofbow'                       => new ParameterCounters\Product\AngleofbowCounter(),
            'applications'                     => new ParameterCounters\Product\ApplicationCounter(),
            'approvals'                        => new ParameterCounters\Product\ApprovalCounter(),
            'bowrange'                         => new ParameterCounters\Product\BowrangeCounter(),
            'brand'                            => new ParameterCounters\Product\BrandCounter(),
            'colors'                           => new ParameterCounters\Product\ColorCounter(),
            'connectiontype'                   => new ParameterCounters\Product\ConnectiontypeCounter(),
            'contourcode'                      => new ParameterCounters\Product\ContourcodeCounter(),
            'externaltubediameterofconnection' => new ParameterCounters\Product\ExternaltubediameterofconnectionCounter(),
            'finishings'                       => new ParameterCounters\Product\FinishingCounter(),
            'manufacturercode'                 => new ParameterCounters\Product\ManufacturercodeCounter(),
            'materialquality'                  => new ParameterCounters\Product\MaterialqualityCounter(),
            'materials'                        => new ParameterCounters\Product\MaterialCounter(),
            'numberofconnections'              => new ParameterCounters\Product\NumberofconnectionsCounter(),
            'productline'                      => new ParameterCounters\Product\ProductlineCounter(),
            'producttype'                      => new ParameterCounters\Product\ProducttypeCounter(),
            'pumpbrand'                        => new ParameterCounters\Product\PumpbrandCounter(),
            'series'                           => new ParameterCounters\Product\SeriesCounter(),
            'shape'                            => new ParameterCounters\Product\ShapeCounter(),
            'solutions'                        => new ParameterCounters\Product\SolutionCounter(),
            'type'                             => new ParameterCounters\Product\TypeCounter(),
        ];
    }

    /**
     * @param bool $apply
     * @return $this
     */
    public function setApplyGroupBy($apply = true)
    {
        $this->applyProductGroupBy = (bool) $apply;

        return $this;
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

        if ( ! $this->isQueryForCountable && $this->applyProductGroupBy) {
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
