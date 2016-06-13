<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;

class ContentPresenter extends AbstractPresenter
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
        return (     $this->entity->contentGalleries
                &&   count($this->entity->contentGalleries)
                &&   $this->entity->contentGalleries->first()->contentGalleryImages
                &&   count($this->entity->contentGalleries->first()->contentGalleryImages)
                );
    }

    public function headerImage()
    {
        if ( ! $this->hasHeaderImage()) return null;

        return $this->entity->contentGalleries->first()->contentGalleryImages->first()->file;
    }

}
