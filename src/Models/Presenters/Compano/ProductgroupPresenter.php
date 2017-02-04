<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;
use App\Models\Aalberts\Compano\Productgroup;

class ProductgroupPresenter extends AbstractPresenter
{
    /**
     * @var Productgroup
     */
    protected $entity;

    /**
     * @return bool
     */
    public function hasImage()
    {
        if ( ! $this->entity->relationLoaded('productgroups')) {
            return false;
        }

        return (    $this->entity->productgroups->count()
                &&  count($this->entity->productgroups->first()->productgroupImages)
                );
    }

    /**
     * @return string|null
     */
    public function thumbImage()
    {
        if ( ! $this->hasImage()) return null;

        $file = $this->entity->productgroups->first()->productgroupImages->first()->file;

        // todo: 'vsh_pg_' prefix is in the old code, but is it correct?

        return $this->decorateUrlWithAalbertsUpload($file, 'gallery');
    }

}
