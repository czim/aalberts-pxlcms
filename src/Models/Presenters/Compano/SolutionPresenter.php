<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;
use App\Models\Aalberts\Compano\Solution;

class SolutionPresenter extends AbstractPresenter
{
    /**
     * @var Solution
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
        if ( ! count($this->entity->solutionImages)) {
            return null;
        }

        return $this->decorateUrlWithAalbertsUpload('gray_' . $this->entity->solutionImages->first()->file, 'gallery');
    }

}
