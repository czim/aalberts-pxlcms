<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;
use App\Models\Aalberts\Compano\Approval;

class ApprovalPresenter extends AbstractPresenter
{
    /**
     * @var Approval
     */
    protected $entity;

    /**
     * @return bool
     */
    public function hasContent()
    {
        return false;
    }

    /**
     * @return string
     */
    public function contentSlug()
    {
        return null;
    }

    /**
     * @return null|string
     */
    public function image()
    {
        if ( ! count($this->entity->approvalImages)) {
            return null;
        }

        return $this->decorateUrlWithAalbertsUpload('gray_' . $this->entity->approvalImages->first()->file, 'gallery');
    }

}
