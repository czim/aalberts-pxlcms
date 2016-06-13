<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;

class SupplierPresenter extends AbstractPresenter
{

    public function web()
    {
        return $this->normalizeLink($this->entity->web);
    }

}
