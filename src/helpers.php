<?php

if ( ! function_exists('aalberts_uploads')) {
    /**
     * Format a relative path/file as an URL to the aalberts uploads directory
     *
     * @param  string $path
     * @return string
     */
    function aalberts_uploads($path = '')
    {
        return config('aalberts.paths.uploads') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if ( ! function_exists('aalberts_trans')) {
    /**
     * Translates a label using the Aalberts CMS phrase translations
     *
     * @param  string      $label
     * @param  null|string $locale
     * @return string
     */
    function aalberts_trans($label, $locale = null)
    {
        return app('aalberts-translate')->translate($label, $locale);
    }
}

if ( ! function_exists('atrans')) {
    /**
     * Translates a label using the Aalberts CMS phrase translations
     * (short version)
     *
     * @param  string      $label
     * @param  null|string $locale
     * @return string
     */
    function atrans($label, $locale = null)
    {
        return app('aalberts-translate')->translate($label, $locale);
    }
}
