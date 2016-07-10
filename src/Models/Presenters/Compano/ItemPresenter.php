<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;

class ItemPresenter extends AbstractPresenter
{

    /**
     * Image URL
     *
     * @return null|string
     */
    public function image()
    {
        if ( ! $this->entity->drawing) return null;

        return $this->decorateUrlWithCompanoHost($this->entity->image);
    }

    /**
     * Technical drawing URL
     *
     * @return null|string
     */
    public function drawing()
    {
        if ( ! $this->entity->drawing) return null;

        return $this->decorateUrlWithCompanoHost($this->entity->drawing);
    }

}
