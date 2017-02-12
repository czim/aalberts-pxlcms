<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;
use App\Models\Aalberts\Compano\Application;

class ApplicationPresenter extends AbstractPresenter
{
    /**
     * @var Application
     */
    protected $entity;

    /**
     * @return bool
     */
    public function hasContent()
    {
        return count($this->entity->contents) > 0;
    }

    /**
     * @return string
     */
    public function contentSlug()
    {
        return $this->entity->contents[0]->slug;
    }

    /**
     * @return null|string
     */
    public function image()
    {
        if ( ! count($this->entity->applicationImages)) {
            return null;
        }

        return $this->decorateUrlWithAalbertsUpload('gray_' . $this->entity->applicationImages->first()->file, 'gallery');
    }

}
