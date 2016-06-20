<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;
use Aalberts\Models\Presenters\Traits\HasGalleryHeaderImage;

class ContentPresenter extends AbstractPresenter
{
    use HasGalleryHeaderImage;
    
    protected $galleryRelation      = 'contentGalleries';
    protected $galleryImageRelation = 'contentGalleryImages';
    
    
    public function date()
    {
        return $this->normalizeDate($this->entity->date);
    }

    public function dateTime()
    {
        return $this->normalizeDateTime($this->entity->date);
    }

}
