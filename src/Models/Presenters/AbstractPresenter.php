<?php
namespace Aalberts\Models\Presenters;

use Carbon\Carbon;
use Laracasts\Presenter\Presenter;

abstract class AbstractPresenter extends Presenter
{

    /**
     * @param null|Carbon $date
     * @return null|string
     */
    protected function normalizeDate($date)
    {
        if ( ! $date) return null;

        return $date->format('Y-m-d');
    }
    
    /**
     * @param null|Carbon $date
     * @return null|string
     */
    protected function normalizeDateTime($date)
    {
        if ( ! $date) return null;

        return $date->format('Y-m-d H:i');
    }

    /**
     * @param string $url
     * @return string
     */
    protected function normalizeLink($url)
    {
        if ( ! $url) return '';

        return (( ! preg_match('#^(https?:)?//#i', $url)) ? 'http://' : '')
        . $url;
    }

    /**
     * Returns a link with the aalberts upload path appended, if required.
     * 
     * @param string $link
     * @return string
     */
    protected function decorateUrlWithAalbertsUpload($link)
    {
        if (preg_match('#^https?://#i', $link)) {
            return $link;
        }

        return rtrim(config('aalberts.paths.uploads'), '/') . '/' . $link;
    }

}
