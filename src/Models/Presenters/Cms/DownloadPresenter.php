<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;
use App\Models\Aalberts\Cms\DownloadFile;

class DownloadPresenter extends AbstractPresenter
{

    /**
     * @return null|string
     */
    public function link()
    {
        $file = $this->entity->downloadFiles->first();

        if ( ! $file) {
            $file = $this->entity->downloadImages->first();
        }

        if ( ! $file) return null;

        /** @var DownloadFile $file */
        return $this->decorateUrlWithAalbertsUpload($file->file, 'files');
    }

}
