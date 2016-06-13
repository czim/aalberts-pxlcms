<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;

class NewsPresenter extends AbstractPresenter
{
    
    public function date()
    {
        return $this->normalizeDate($this->entity->date);
    }

    public function dateTime()
    {
        return $this->normalizeDateTime($this->entity->date);
    }

    public function hasHeaderImage()
    {
        return (     $this->entity->newsGalleries
                &&   count($this->entity->newsGalleries)
                &&   $this->entity->newsGalleries->first()->newsGalleryImages
                &&   count($this->entity->newsGalleries->first()->newsGalleryImages)
                );
    }

    public function headerImage()
    {
        if ( ! $this->hasHeaderImage()) return null;

        return $this->entity->newsGalleries->first()->newsGalleryImages->first()->file;
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
