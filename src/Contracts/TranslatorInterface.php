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

}
