<?php
namespace Aalberts\Repositories;

use Aalberts\Enums\CacheTag;
use Aalberts\Enums\DownloadCategory;
use App\Models\Aalberts\Cms\Download as DownloadModel;
use App\Models\Aalberts\Compano\Product;
use Czim\Repository\Criteria\Common\WhereHas;
use Czim\Repository\Criteria\Common\WithRelations;
use Czim\Repository\Enums\CriteriaKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DownloadRepository extends AbstractRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::DOWNLOAD ];

    public function model()
    {
        return DownloadModel::class;
    }

    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()->merge([
            CriteriaKey::WITH => new WithRelations($this->withBase()),
        ]);
    }

    /**
     * Returns list of all categories, unordered (but sorted).
     * Cached.
     *
     * @param int $count
     * @return Collection|DownloadModel[]
     */
    public function index($count = 10)
    {
        // todo, but not needed right now
    }

    /**
     * Returns a list of the relevant categories (slugs) that have downloads.
     * Cached.
     *
     * @return string[]
     */
    public function categories()
    {
        $this->restrictByLanguageOnce();

        return $this->cachedQuery()
            ->distinct()
            ->select(['category'])
            ->groupBy('category')
            ->pluck('category')
            ->toArray();
    }

    /**
     * Returns a list of downloads by a given category.
     * Cached.
     *
     * @param string   $category
     * @param null|int $limit       if given, returns only the first X
     * @param null|int $pageSize    if given, returns paginated result
     * @return \App\Models\Aalberts\Cms\Download[]|Collection
     */
    public function getByCategory($category, $limit = null, $pageSize = null)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );
        
        $this->restrictByLanguageOnce();

        $query = $this->cachedQuery()
            ->where('category', $category);


        if (null !== $pageSize) {
            return $query->paginate($pageSize);
        }

        if (null !== $limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    /**
     * Returns a list of download, limited X each by category, grouped per category.
     * Cached.
     *
     * @param int $limit
     * @return array    keyed by category slug, lists of Downloads
     */
    public function listPerCategory($limit = 3)
    {
        // todo
    }

    /**
     * Returns a list of downloads for a given product.
     *
     * @param Product $product
     * @return \App\Models\Aalberts\Cms\Download[]|Collection
     */
    public function getByProduct(Product $product)
    {
        // Get downloads for productlines, applications and solutions
        // Oddly enough, the downloads should only be included if they match for ALL
        // of these related to the product.
        $lineIds        = $product->productlines->pluck('id');
        $applicationIds = $product->applications->pluck('id');
        $solutionIds    = $product->solutions->pluck('id');

        $downloadIds = \DB::table('cms_download')
            ->select('cms_download.id')
            ->join('cms_download_productline', 'cms_download_productline.download', '=', 'cms_download.id')
            ->join('cms_download_application', 'cms_download_application.download', '=', 'cms_download.id')
            ->join('cms_download_solution', 'cms_download_solution.download', '=', 'cms_download.id')
            ->whereIn('cms_download_productline.productline', $lineIds)
            ->whereIn('cms_download_application.application', $applicationIds)
            ->whereIn('cms_download_solution.solution', $solutionIds)
            ->where('active', true)
            ->where('category', DownloadCategory::DOCUMENT)
            ->pluck('id');

        if (empty($downloadIds)) {
            return new Collection;
        }

        return $this->cachedQuery()
            ->whereIn('id', $downloadIds)
            ->get();
    }
    

    /**
     * Looks up a content entry by its code.
     * Cached.
     *
     * @param string $code
     * @return null|DownloadModel
     */
    public function findByCode($code)
    {
        $this->pushCriteriaOnce(
            new WithRelations(array_merge($this->withBase(), $this->withDetail())),
            CriteriaKey::WITH
        );

        return $this->cachedQuery()
            ->where('code',  $code)
            ->first();
    }


    /**
     * @param string   $term
     * @param null|int $count   limit results
     * @return DownloadModel|Collection
     */
    public function search($term, $count = null)
    {
        $terms = array_filter(explode(' ', $term));

        $query = $this->query()
            ->whereHas('translations', function($query) use ($terms) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */

                $query->where('language', $this->languageIdForLocale())
                    ->where(function($query) use ($terms) {
                        /** @var \Illuminate\Database\Eloquent\Builder $query */

                        $query
                            ->where(function($query) use ($terms) {
                                foreach ($terms as $splitTerm) {
                                    $query->where('title', 'like', '%' . $splitTerm . '%');
                                }
                            })
                            ->orWhere(function($query) use ($terms) {
                                foreach ($terms as $splitTerm) {
                                    $query->where('description', 'like', '%' . $splitTerm . '%');
                                }
                            });
                    });
            });

        if (null !== $count) {
            $query->take($count);
        }

        return $query->get();
    }

    
    // ------------------------------------------------------------------------------
    //      Criteria
    // ------------------------------------------------------------------------------
    

    /**
     * Restricts the results by the current language
     *
     * @return $this
     */
    protected function restrictByLanguageOnce()
    {
        $this->pushCriteriaOnce(
            new WhereHas('languages', function (Builder $query) {
                $query->where('cms_language.id', $this->languageIdForLocale());
            })
        );

        return $this;
    }


    // ------------------------------------------------------------------------------
    //      With Relations
    // ------------------------------------------------------------------------------

    /**
     * Returns with parameter array to use by default
     *
     * @return array
     */
    protected function withBase()
    {
        return [
            'translations' => $this->eagerLoadCachedTranslationCallable(),
        ];
    }

    /**
     * Returns with parameter array to use for detail page
     *
     * @return array
     */
    protected function withDetail()
    {
        return [
            'contents'       => $this->eagerLoadCachedCallable(null, [CacheTag::CONTENT]),
            'suppliers'      => $this->eagerLoadCachedCallable(null, [CacheTag::CMP_SUPPLIER]),
            'downloadFiles'  => $this->eagerLoadCachedCallable(),
            'downloadImages' => $this->eagerLoadCachedCallable(),
        ];
    }

}
