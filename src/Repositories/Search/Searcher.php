<?php
namespace Aalberts\Repositories\Search;

use Aalberts\Events\SearchPerformed;
use Aalberts\Repositories\ContentRepository;
use Aalberts\Repositories\DownloadRepository;
use Aalberts\Repositories\NewsRepository;
use Aalberts\Repositories\ProjectRepository;
use Illuminate\Support\Collection;

class Searcher
{

    /**
     * @var ContentRepository
     */
    protected $contentRepository;

    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var DownloadRepository
     */
    protected $downloadRepository;


    public function __construct(
        ContentRepository $contentRepository,
        NewsRepository $newsRepository,
        ProjectRepository $projectRepository,
        DownloadRepository $downloadRepository
    ) {
        $this->contentRepository  = $contentRepository;
        $this->newsRepository     = $newsRepository;
        $this->projectRepository  = $projectRepository;
        $this->downloadRepository = $downloadRepository;
    }

    /**
     * @param string   $term
     * @param null|int $count   limit results for each type
     * @return Collection
     */
    public function searchEverything($term, $count = null)
    {
        event( new SearchPerformed($term) );

        return collect([
            'news'      => $this->searchNews($term, $count),
            'content'   => $this->searchContent($term, $count),
            'projects'  => $this->searchProjects($term, $count),
            'downloads' => $this->searchDownloads($term, $count),
            'products'  => $this->searchProducts($term, $count),
        ]);
    }

    /**
     * @param string $term
     * @param null|int $count   limit results for each type
     * @return \App\Models\Aalberts\Cms\News|Collection
     */
    public function searchNews($term, $count = null)
    {
        return $this->newsRepository->search($term, $count);
    }

    /**
     * @param string   $term
     * @param null|int $count limit results for each type
     * @return \App\Models\Aalberts\Cms\Content|Collection
     */
    public function searchContent($term, $count = null)
    {
        return $this->contentRepository->search($term, $count);
    }

    /**
     * @param string   $term
     * @param null|int $count   limit results for each type
     * @return \App\Models\Aalberts\Cms\Project|Collection
     */
    public function searchProjects($term, $count = null)
    {
        return $this->projectRepository->search($term, $count);
    }

    /**
     * @param string   $term
     * @param null|int $count   limit results for each type
     * @return \App\Models\Aalberts\Cms\Download|Collection
     */
    public function searchDownloads($term, $count = null)
    {
        return $this->downloadRepository->search($term, $count);
    }

    /**
     * @param string $term
     * @param null|int $count   limit results for each type
     */
    public function searchProducts($term, $count = null)
    {
        return null;
    }

}
