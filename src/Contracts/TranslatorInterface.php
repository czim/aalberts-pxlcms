<?php
namespace Aalberts\Contracts;

interface TranslatorInterface
{

    /**
     * Translates a label into the current language
     *
     * @param string      $label
     * @param null|string $locale
     * @return string
     */
    public function translate($label, $locale = null);

    /**
     * Caches all translations
     *
     * @return bool
     */
    public function cacheTranslations();

    /**
     * Checks and returns whether there have been updates to the translations since
     * they were last cached.
     *
     * @return boolean      true if updates are available
     */
    public function checkForUpdates();
    
    /**
     * Flushes the entire translations cache
     */
    public function flushCache();

    /**
     * Adds a phrase to the database, if it does not yet exist.
     *
     * @param string $phrase
     * @return boolean
     */
    public function addPhrase($phrase);

}
