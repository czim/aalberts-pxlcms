<?php
namespace Aalberts\Models\Presenters\Traits;

trait HasGalleryHeaderImage
{

    /**
     * Returns whether a (first) header image is available.
     *
     * @return bool
     */
    public function hasHeaderImage()
    {
        return (     $this->entity->{$this->getGalleryEntityRelation()}
                &&   count($this->entity->{$this->getGalleryEntityRelation()})
                &&   $this->entity->{$this->getGalleryEntityRelation()}->first()->{$this->getGalleryImageEntityRelation()}
                &&   count($this->entity->{$this->getGalleryEntityRelation()}->first()->{$this->getGalleryImageEntityRelation()})
                );
    }

    /**
     * Returns (link to) image file, relative path on CMS server.
     *
     * @return string
     */
    public function headerImage()
    {
        if ( ! $this->hasHeaderImage()) return null;

        return $this->entity->{$this->getGalleryEntityRelation()}->first()
                            ->{$this->getGalleryImageEntityRelation()}->first()
                            ->file;
    }

    /**
     * Returns a caption for the header image, if there is any.
     * Defaults to 'top', but takes 'bottom' if not filled.
     *
     * @param null|string $type 'top', 'bottom'
     * @return string
     */
    public function headerCaption($type = null)
    {
        if ( ! $this->hasHeaderImage()) return null;

        $gallery = $this->entity->{$this->getGalleryEntityRelation()}->first();

        switch ($type) {

            case 'top':
                return $gallery->caption_top;

            case 'bottom':
                return $gallery->caption_top;

            default:
                return $gallery->caption_top ?: $gallery->caption_bottom;
        }
    }


    /**
     * @return string
     */
    protected function getGalleryEntityRelation()
    {
        if ( ! isset($this->galleryRelation)) {
            throw new \RuntimeException(get_class($this) . ' HasGalleryImage trait requires galleryRelation');
        }

        return $this->galleryRelation;
    }

    /**
     * @return string
     */
    protected function getGalleryImageEntityRelation()
    {
        if ( ! isset($this->galleryImageRelation)) {
            throw new \RuntimeException(get_class($this) . ' HasGalleryImage trait requires galleryImageRelation');
        }

        return $this->galleryImageRelation;
    }

}
