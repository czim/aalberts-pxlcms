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

    
    // ------------------------------------------------------------------------------
    //      Links and URLs
    // ------------------------------------------------------------------------------

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
     * @param string      $link
     * @param string|null $directory    optional extra relative directory in path
     * @return string
     */
    protected function decorateUrlWithAalbertsUpload($link, $directory = null)
    {
        return $this->decorateUrlWithHost(config('aalberts.paths.uploads'), $link, $directory);
    }

    /**
     * Returns a link with the compano (image) host appended, if required.
     *
     * @param string      $link
     * @param string|null $directory    optional extra relative directory in path
     * @return string
     */
    protected function decorateUrlWithCompanoHost($link, $directory = null)
    {
        return $this->decorateUrlWithHost(config('aalberts.paths.compano'), $link, $directory);
    }

    /**
     * @param string      $host
     * @param string      $link
     * @param string|null $directory    optional extra relative directory in path
     * @return string
     */
    protected function decorateUrlWithHost($host, $link, $directory = null)
    {
        if (preg_match('#^https?://#i', $link)) {
            return $link;
        }

        return rtrim($host, '/') . '/'
             . ($directory ?  trim($directory, '/') . '/' : '')
             . ltrim($link, '/');
    }
}
