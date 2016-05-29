<?php
namespace Aalberts\Translation;

use App\Models\Aalberts\Cms\Phrase;
use App\Models\Aalberts\Cms\Translation;
use Cache;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Translator
{
    // defaults if not present in config
    const CACHE_KEY_PREFIX = 'aalberts-translation:';
    const CACHE_UPDATE_KEY = 'aalberts-translation-update';
    const CACHE_MINUTES    = 86400;
    const CACHE_TAG        = 'translation';


    /**
     * Translates a label into the current language
     *
     * @param string      $label
     * @param null|string $locale
     * @return string
     */
    public function translate($label, $locale = null)
    {
        $translation = $this->getCachedTranslation($label, $locale);

        if (false === $translation) {
            // get and cache new translation
            $translation = $this->retrieveTranslation($label, $locale);
        }

        return $translation ?: $label;
    }

    /**
     * Caches all translations
     *
     * @return bool
     */
    public function cacheTranslations()
    {
        $phrases = $this->getAllPhrases();

        foreach ($phrases as $phrase) {

            $localesToDo = $this->getCacheLocales();

            foreach ($phrase->translations as $translation) {

                $locale = $this->getLocaleForLanguageId($translation->language);

                Cache::tags([ $this->getCacheTag() ])
                    ->put(
                        $this->getCacheKey($locale, $phrase->phrase),
                        $translation->translation,
                        $this->getCacheMinutes()
                    );

                $localesToDo = array_diff($localesToDo, [ $locale ]);
            }

            // if the locale is not present, cache the label as its own translation (as a fallback)
            if (count($localesToDo)) {
                foreach ($localesToDo as $locale) {
                    Cache::tags([ $this->getCacheTag() ])
                        ->put(
                            $this->getCacheKey($locale, $phrase->phrase),
                            $phrase->phrase,
                            $this->getCacheMinutes()
                        );
                }
            }
        }

        // keep track of when the cache was last filled
        $this->markCacheUpdateTime();

        return true;
    }

    /**
     * Retrieves a single translation from the database, caches and returns it.
     * This *will* override the cache. It will returns the phrase if no translation
     * was found.
     *
     * @param string      $label
     * @param null|string $locale
     * @return string
     */
    protected function retrieveTranslation($label, $locale = null)
    {
        $phrase = $this->getPhraseByLabel($label);

        if ( ! $phrase) {
            //throw new ModelNotFoundException("Could not find Phrase for '{$label}'");
            return $label;
        }

        $locale = $locale ?: app()->getLocale();

        /** @var Translation $translation */
        $translation = $phrase->translations()
            ->where('language', $this->getLanguageIdForLocale($locale))
            ->first();

        if ( ! $translation) {
            return $label;
        }

        return $translation->translation;
    }

    /**
     * Retrieves and returns all phrases that may have translations
     *
     * @return \Illuminate\Database\Eloquent\Collection|Phrase[]
     */
    protected function getAllPhrases()
    {
        return Phrase::with(['translations'])->get();
    }

    /**
     * Retrieves and returns a phrase model by its label
     *
     * @param string $label
     * @return Model|Phrase
     */
    protected function getPhraseByLabel($label)
    {
        return Phrase::where('phrase', $label)->first();
    }


    // ------------------------------------------------------------------------------
    //      Cache
    // ------------------------------------------------------------------------------

    /**
     * Marks the current time as the last tag update time
     */
    protected function markCacheUpdateTime()
    {
        Cache::tags([ $this->getCacheTag() ])
            ->put($this->getCacheUpdateKey(), new DateTime(), $this->getCacheMinutes());
    }

    /**
     * Checks and returns whether there have been updates to the translations since
     * they were last cached.
     */
    public function checkForUpdates()
    {
        if (null ==  $this->getCacheUpdateTime()) return true;

        // get last update to the translations table
        $translationUpdate = new DateTime();

        return $this->checkCacheUpdateAgainstTime($translationUpdate);
    }

    /**
     * Checks the known last cache update time and checks it against a given time.
     * Returns whether any updates have been done since.
     *
     * @param DateTime $date
     * @return bool     true if updates are available
     */
    protected function checkCacheUpdateAgainstTime(DateTime $date)
    {
        $lastUpdate = $this->getCacheUpdateTime();

        if ( ! $lastUpdate) return true;

        return ($date > $lastUpdate);
    }

    /**
     * @return DateTime|null
     */
    protected function getCacheUpdateTime()
    {
        return Cache::tags([ $this->getCacheTag() ])->get($this->getCacheUpdateKey());
    }

    /**
     * Returns a translated label from the cache, if available
     *
     * @param string      $label
     * @param null|string $locale
     * @return string|false     false if not available in cache
     */
    protected function getCachedTranslation($label, $locale = null)
    {
        $locale         = $locale ?: app()->getLocale();
        $cacheKey       = $this->getCacheKey($locale, $label);
        $cacheTag       = $this->getCacheTag();

        if ( ! Cache::tags([$cacheTag])->has($cacheKey)) return false;

        return Cache::tags([$cacheTag])->get($cacheKey);
    }

    /**
     * @return string
     */
    protected function getCacheTag()
    {
        return config('aalberts.translator.cache.tag', static::CACHE_TAG);
    }

    /**
     * @param string $locale
     * @param string $label
     * @return string
     */
    protected function getCacheKey($locale, $label)
    {
        return config('aalberts.translator.cache.key', static::CACHE_KEY_PREFIX)
        . $locale . ':' . $label;
    }

    /**
     * @return string
     */
    protected function getCacheUpdateKey()
    {
        return config('aalberts.translator.cache.update-key', static::CACHE_UPDATE_KEY);
    }

    /**
     * @return int
     */
    protected function getCacheMinutes()
    {
        return config('aalberts.translator.cache.ttl', static::CACHE_MINUTES);
    }

    /**
     * Returns array with locales to cache empty values as the phrase/label for
     *
     * @return array
     */
    protected function getCacheLocales()
    {
        return config('aalberts.translator.cache.locales', []);
    }


    // ------------------------------------------------------------------------------
    //      Helpers
    // ------------------------------------------------------------------------------

    /**
     * Returns Laravel locale for CMS language ID
     *
     * @param int $id
     * @return null|string
     */
    protected function getLocaleForLanguageId($id)
    {
        return (new Phrase)->lookupLocaleForLanguageId($id);
    }

    /**
     * Returns CMS language ID for Laravel locale
     *
     * @param string $locale
     * @return null|string
     */
    protected function getLanguageIdForLocale($locale)
    {
        return (new Phrase)->lookUpLanguageIdForLocale($locale);
    }
}
