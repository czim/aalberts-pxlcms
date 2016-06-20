<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;

class RelatedproductPresenter extends AbstractPresenter
{

    /**
     * @return bool
     */
    public function hasImage()
    {
        return (    $this->entity->relatedproductImages
                &&  count($this->entity->relatedproductImages)
                );
    }

    /**
     * @return null
     */
    public function image()
    {
        if ( ! $this->hasImage()) return null;

        return $this->entity->relatedproductImages->first()->file;
    }

    /**
     * @return null|string
     */
    public function link()
    {
        if ( ! $this->entity->filterlink) return null;

        return url($this->entity->filterlink);
    }

}
