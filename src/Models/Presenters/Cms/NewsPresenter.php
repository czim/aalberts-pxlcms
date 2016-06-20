<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;
use Aalberts\Models\Presenters\Traits\HasGalleryHeaderImage;

class NewsPresenter extends AbstractPresenter
{
    use HasGalleryHeaderImage;

    protected $galleryRelation      = 'newsGalleries';
    protected $galleryImageRelation = 'newsGalleryImages';


    /**
     * @return null|string
     */
    public function date()
    {
        return $this->normalizeDate($this->entity->date);
    }

    /**
     * @return null|string
     */
    public function dateTime()
    {
        return $this->normalizeDateTime($this->entity->date);
    }

    /**
     * Returns the data required for rendering of a previous/next link.
     * This should return whatever the prev-next partial expects for 'prev' or 'next' key data.
     *
     * @return string
     */
    public function prevNext()
    {
        return route(
            config('aalberts.routes.news-detail', 'news-detail'),
            [ $this->entity->slug ]
        );
    }

}
