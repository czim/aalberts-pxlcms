<?php
namespace Aalberts\Commands;

use Aalberts\Contracts\TranslatorInterface;
use Aalberts\Enums\CacheTag;
use Aalberts\Enums\CmsUpdateAction;
use Aalberts\Enums\CmsUpdateType;
use Aalberts\Enums\PusherEvent;
use Aalberts\Events\CmsUpdateReceived;
use Aalberts\Models\CmsModel;
use Cache;
use Illuminate\Console\Command;
use Log;

class PusherEventCommand extends Command
{
    protected $signature   = 'aalberts:event {scope} {event} {type} {data?}';
    protected $description = 'Handles aalberts pusher events.';
    
    public function handle()
    {
        $scope = $this->argument('scope');
        $event = $this->argument('event');
        $type  = $this->argument('type');
        $data  = $this->decodeData( $this->argument('data') );

        // strip type parameter from data
        if ( ! is_array($data)) $data = [];
        array_forget($data, 'type');


        switch ($event) {

            case PusherEvent::UPDATE:
                event( new CmsUpdateReceived($scope, $type, $data) );
                $this->handleCmsUpdate($scope, $type, $data);
                break;

            default:
                $warning = "Unhandled event type for PusherEventCommand '{$event}' for type '{$type}'.";
                Log::warning($warning, [ $data ]);
                $this->warn('Unhandled event type: ' . $event);
        }
    }

    /**
     * Handles events of type 'update'
     *
     * @param string $scope
     * @param string $type
     * @param array  $data
     */
    protected function handleCmsUpdate($scope, $type, array $data)
    {
        $action = array_get($data, 'action');

        switch ($type) {

            case CmsUpdateType::TRANSLATION:
                $this->handleTranslationUpdate(
                    array_get($data, 'phrase'),
                    array_get($data, 'language'),
                    array_get($data, 'translation'),
                    $action === CmsUpdateAction::ACTION_DELETE
                );
                break;

            default:
                $warning = "PusherEventCommand: unknown type '{$type}'";
                Log::warning($warning, [ $data ]);
                $this->warn($warning);
        }
    }

    /**
     * Flushes the cache for a given tag or set of tags
     *
     * @param string|array $tags
     * @return $this
     */
    protected function flushCacheForTag($tags)
    {
        if ( ! is_array($tags)) $tags = [ $tags ];

        Cache::tags($tags)->flush();

        return $this;
    }

    /**
     * Handles an updated translation
     *
     * @param string      $phrase
     * @param int         $language CMS id
     * @param string|null $translation
     * @param bool        $deleted
     */
    protected function handleTranslationUpdate($phrase, $language, $translation = null, $deleted = false)
    {
        /** @var TranslatorInterface $translator */
        $translator = app('aalberts-translate');

        // in order to selectively affect the cache, we need to know
        // the phrase, and, if not deleting, the new translation.
        // if we don't, just refresh the entire translations cache
        if (    (   (null === $phrase || '' === $phrase)
                ||      ! $deleted
                    &&  (null === $translation || '' === $translation)
                )
            &&  $translator->checkForUpdates()
        ) {
            $translator->cacheTranslations();
            return;
        }

        // update specific phrase
        // if it was deleted, just use the label as a new 'translation'
        $languageModel = new CmsModel;
        $locale = $languageModel->lookupLocaleForLanguageId($language);

        $translator->cacheTranslation(
            $phrase,
            $deleted ? $phrase : $translation,
            $locale
        );
    }

    /**
     * Decodes the command line data argument
     *
     * @param string $data
     * @return array
     */
    protected function decodeData($data)
    {
        if (empty($data)) return [];

        return json_decode(base64_decode($data), true);
    }

}
