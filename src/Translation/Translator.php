<?php
namespace Aalberts\Translation;

use Aalberts\Contracts\NoticesLoggerInterface;
use Aalberts\Contracts\TranslatorInterface;
use Aalberts\Enums\CacheTags;
use Aalberts\Events\DetectedMissingTranslationPhrase;
use App\Models\Aalberts\Cms\Phrase;
use App\Models\Aalberts\Cms\Translation;
use Cache;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Translator implements TranslatorInterface
{
    // defaults if not present in config
    const CACHE_KEY_PREFIX = 'aalberts-translation:';
    const CACHE_UPDATE_KEY = 'aalberts-translation-update';
    const CACHE_MINUTES    = 86400;

    /**
     * Cachekey-keyed static memory for process-scope cache.
     * This is solely to prevent the actual Cache from being spammed
     * unnecessarily during a single process/stack.
     *
     * @var array
     */
    protected static $memory = [];

    /**
     * @var NoticesLoggerInterface
     */
    protected $logger;

    /**
     * @param NoticesLoggerInterface $logger
     */
    public function __construct(NoticesLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
            $this->addTranslationToCache($label, $translation, $locale);
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
                $this->addTranslationToCache($phrase->phrase, $translation->translation, $locale);
                $localesToDo = array_diff($localesToDo, [ $locale ]);
            }

            // if the locale is not present, cache the label as its own translation (as a fallback)
            if (count($localesToDo)) {
                foreach ($localesToDo as $locale) {
                    $this->addTranslationToCache($phrase->phrase, $phrase->phrase, $locale);
                }
            }
        }

        // keep track of when the cache was last filled
        $this->markCacheUpdateTime();

        return true;
    }

    /**
     * Flushes the entire translations cache
     */
    public function flushCache()
    {
        Cache::tags([ $this->getCacheTag() ])->flush();
        static::$memory = [];
    }

    /**
     * Checks and returns whether there have been updates to the translations since
     * they were last cached.
     */
    public function checkForUpdates()
    {
        if (null ==  $this->getCacheUpdateTime()) return true;

        // get last update to the translations table
        $translation = Translation::unordered()->orderBy('modifiedts', 'desc')->take(1)->first([ 'modifiedts' ]);

        $translationUpdate = $translation->modifiedts;

        return $this->checkCacheUpdateAgainstTime($translationUpdate);
    }

    /**
     * Adds a phrase to the database, if it does not yet exist.
     *
     * @param string $phrase
     * @return boolean
     */
    public function addPhrase($phrase)
    {
        $existing = $this->getPhraseByLabel($phrase);
        if ($existing) return false;

        $new = new Phrase([
            'phrase'       => $phrase,
            'organization' => config('aalberts.organization'),
            'active'       => true,
        ]);

        if ($new->save()) {
            $this->logger->debug("Added phrase to database: '{$phrase}'.");
        }

        return false;
    }


    /**
     * Retrieves a single translation from the database and returns it.
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
            $this->handleMissingTranslationPhrase($label);
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

        if (array_key_exists($cacheKey, static::$memory)) {
            return static::$memory[ $cacheKey ];
        }

        if ( ! Cache::tags([$cacheTag])->has($cacheKey)) return false;

        $translation = Cache::tags([$cacheTag])->get($cacheKey);

        static::$memory[ $cacheKey ] = $translation;

        return $translation;
    }


    /**
     * Puts a specific translation for a phrase in the cache
     *
     * @param string      $label
     * @param string      $translation
     * @param null|string $locale
     */
    protected function addTranslationToCache($label, $translation, $locale = null)
    {
        $locale   = $locale ?: app()->getLocale();
        $cacheKey = $this->getCacheKey($locale, $label);

        Cache::tags([ $this->getCacheTag() ])
            ->put(
                $cacheKey,
                $translation,
                $this->getCacheMinutes()
            );

        static::$memory[ $cacheKey ] = $translation;
    }

    /**
     * @return string
     */
    protected function getCacheTag()
    {
        return CacheTags::TRANSLATION;
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

    /**
     * Logs a warning about a translations phrase not being present in the database
     *
     * @param string $phrase
     */
    protected function handleMissingTranslationPhrase($phrase)
    {
        $this->logger->warning("Missing translation for phrase: '{$phrase}'.");

        event( new DetectedMissingTranslationPhrase($phrase) );
    }

}
