<?php
namespace Aalberts\Repositories;

use Aalberts\Data\ProductFilterGroup;
use Aalberts\Enums\CacheTag;
use Aalberts\Factories\FilterStrategyFactory;
use Aalberts\Filters\ProductFilter;
use App\Models\Aalberts\Cms\Filtergroup;
use App\Models\Aalberts\Cms\Filter as FilterModel;
use Czim\Filter\CountableResults;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Enums\CriteriaKey;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class FilterRepository
 *
 * Repository for the filters (and filter groups) related to productgroups.
 */
class FilterRepository extends AbstractRepository
{
    protected $translated = false;
    protected $cacheTags = [ CacheTag::PRODUCTGROUP, CacheTag::FILTERGROUP ];
    
    public function model()
    {
        return FilterModel::class;
    }

    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()->merge([]);
    }


    /**
     * Returns grouped filters for a given product group ID.
     * Cached.
     *
     * @param int $id compano productgroup ID
     * @return Collection|ProductFilterGroup[]
     */
    public function groupedForProductgroupId($id)
    {
        $organizationId = $this->getActiveOrganizationId();

        // Get all filtergroups relevant for the product group, in the order they should be displayed
        /** @var Collection|Model[] $groups */
        $groups = Filtergroup::query()
            ->remember($this->defaultTtl())
            ->cacheTags([ CacheTag::FILTERGROUP ])
            ->select([
                'cms_filtergroup.id',
                'cms_filtergroup.name',
                'cms_filtergroup.filters',
                'cms_productgroup_filtergroup_ml.name as ml_name',
                'cms_productgroup_filtergroup.main as main',
                'cms_productgroup_filtergroup.position as position',
            ])
            ->join('cms_productgroup_filtergroup', 'cms_productgroup_filtergroup.filtergroup', '=', 'cms_filtergroup.id')
            ->leftJoin('cms_productgroup_filtergroup_ml', function ($join) use ($organizationId) {
                $join
                    ->on('cms_productgroup_filtergroup.id', '=', 'cms_productgroup_filtergroup_ml.entry')
                    ->where('cms_productgroup_filtergroup_ml.language', '=', DB::raw($organizationId));
            })
            ->where('cms_productgroup_filtergroup.productgroup', $id)
            ->where('cms_productgroup_filtergroup.active', true)
            ->where('cms_productgroup_filtergroup.organization', $this->getActiveOrganizationId())
            ->orderBy('cms_productgroup_filtergroup.main', 'desc')
            ->orderBy('cms_productgroup_filtergroup.position')
            ->get();

        /** @var Collection|ProductFilterGroup[] $collection */
        $collection = new Collection(
            array_map(
                function ($data) {
                    return new ProductFilterGroup($data);
                },
                $groups->toArray()
            )
        );

        // For each product group, get the filters
        foreach ($collection as $group) {

            // Add the filters per group
            $group->children = $this->cachedQuery()
                ->select([
                    'cms_filter.id',
                    'cms_filter.slug',
                    'cms_filter.name',
                ])
                ->whereRaw("`cms_filter`.`id` IN ({$group->filters})")
                ->orderByRaw("FIND_IN_SET(`cms_filter`.`id`,'{$group->filters}')")
                ->get();
        }

        $collection = $collection->filter(function (ProductFilterGroup $group) {
            return (bool) count($group->children);
        });

        return $collection;
    }

    /**
     * Returns a list of countables present in a grouped
     *
     * @param Collection|ProductFilterGroup[] $groups
     * @return string[]
     * @see groupedForProductgroupId()
     */
    public function getCountablesForGroupedFilters(Collection $groups)
    {
        $slugs = $groups->reduce(function ($slugs, ProductFilterGroup $group) {
            return array_merge($slugs, $group->children()->pluck('slug')->toArray());
        }, []);

        return (new ProductFilter([]))->getCountablesForFilterSlugs($slugs);
    }

    /**
     * Decorates a group collection based on filter countable results and currently active filter data.
     *
     * @param Collection|ProductFilterGroup[] $groups
     * @param CountableResults                $counts
     * @param array                           $filterData
     * @return ProductFilterGroup[]|Collection
     */
    public function decorateGroupedFiltersForView(Collection $groups, CountableResults $counts, array $filterData)
    {
        // for each filter, apply the related countable results
        // filter slug should match the countable result
        // strategies should be used, default to simple checkbox-based multi-choice

        /** @var FilterStrategyFactory $factory */
        $factory = app(FilterStrategyFactory::class);

        $emptyGroups = [];

        foreach ($groups as $groupIndex => $group) {

            $empty = true;

            foreach ($group->children as $filter) {

                $slug = $filter->slug;

                $strategy = $factory->makeDecorator(
                    $slug,
                    $counts->get($slug),
                    array_get($filterData, $slug)
                );

                $filter->viewType = $strategy->getViewType();
                $filter->viewData = $strategy->getViewData();

                if ( ! $strategy->isEmpty()) {
                    $empty = false;
                }
            }

            if ($empty) {
                $emptyGroups[] = $groupIndex;
            }
        }

        // Filter out the completely optionless filters, if they exist.
        if (count($emptyGroups)) {
            $groups = $groups->except($emptyGroups);
        }

        return $groups;
    }


    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------

    /**
     * Applies criteria once to reverse the standard sorting order
     *
     * @return $this
     */
    protected function reverseDirectionOnce()
    {
        $this->pushCriteriaOnce(
            new OrderBy([ 'position' => 'desc' ]),
            CriteriaKey::ORDER
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function withoutRelationsOnce()
    {
        $this->removeCriteriaOnce(CriteriaKey::WITH);

        return $this;
    }

}
