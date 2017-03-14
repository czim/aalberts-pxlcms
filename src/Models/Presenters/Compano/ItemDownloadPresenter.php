<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;
use App\Models\Aalberts\Compano\ItemDownload;

class ItemDownloadPresenter extends AbstractPresenter
{
    /**
     * @var ItemDownload
     */
    protected $entity;

    /**
     * Download URL
     *
     * @return null|string
     */
    public function link()
    {
        if ( ! $this->entity->file) return null;

        return $this->decorateUrlWithCompanoHost($this->entity->file);
    }

}
