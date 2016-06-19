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

            case CmsUpdateType::CONTENT:
            case CmsUpdateType::CONTENT_GALLERY:
            case CmsUpdateType::CONTENT_GALLERY_IMAGE:
            case CmsUpdateType::CONTENT_RELATED_PRODUCTS:
            case CmsUpdateType::CONTENT_RELATED_NEWS:
            case CmsUpdateType::CONTENT_RELATED_PROJECTS:
            case CmsUpdateType::CONTENT_CUSTOM_BLOCKS:
            case CmsUpdateType::CONTENT_TILE:
            case CmsUpdateType::CONTENT_TILE_IMAGE:
            case CmsUpdateType::CONTENT_DOWNLOAD:
                $this->flushCacheForTag(CacheTag::CONTENT);
                break;

            case CmsUpdateType::NEWS:
            case CmsUpdateType::NEWS_GALLERY:
            case CmsUpdateType::NEWS_GALLERY_IMAGE:
            case CmsUpdateType::NEWS_RELATED_PRODUCTS:
                $this->flushCacheForTag(CacheTag::NEWS);
                break;

            case CmsUpdateType::PROJECT:
            case CmsUpdateType::PROJECT_IMAGE:
            case CmsUpdateType::PROJECT_GALLERY:
            case CmsUpdateType::PROJECT_GALLERY_IMAGE:
                $this->flushCacheForTag(CacheTag::PROJECT);
                break;

            case CmsUpdateType::PROJECTFUNCTION:
                $this->flushCacheForTag(CacheTag::CMS_FUNCTION);
                break;

            case CmsUpdateType::RELATEDPRODUCT:
            case CmsUpdateType::RELATEDPRODUCT_IMAGE:
                $this->flushCacheForTag(CacheTag::RELATEDPRODUCT);
                break;

            case CmsUpdateType::CUSTOMBLOCK:
            case CmsUpdateType::CUSTOMBLOCK_IMAGE:
                $this->flushCacheForTag(CacheTag::CUSTOMBLOCK);
                break;

            case CmsUpdateType::DOWNLOAD:
            case CmsUpdateType::DOWNLOAD_FILE:
            case CmsUpdateType::DOWNLOAD_IMAGE:
                $this->flushCacheForTag(CacheTag::DOWNLOAD);
                break;

            case CmsUpdateType::PRESS:
            case CmsUpdateType::PRESS_DIMENSION:
            case CmsUpdateType::PRESS_MANUFACTURER:
            case CmsUpdateType::PRESS_PRODUCTLINE:
            case CmsUpdateType::PRESS_TOOL:
            case CmsUpdateType::PRESS_REMARK:
            case CmsUpdateType::PRESS_LOOKUP:
                $this->flushCacheForTag(CacheTag::PRESS);
                break;

            case CmsUpdateType::TRANSLATION:
                $this->handleTranslationUpdate(
                    array_get($data, 'phrase'),
                    array_get($data, 'language'),
                    array_get($data, 'translation'),
                    $action === CmsUpdateAction::ACTION_DELETE
                );
                break;

            case CmsUpdateType::PRODUCTGROUP:
            case CmsUpdateType::PRODUCTGROUP_IMAGE:
                $this->flushCacheForTag(CacheTag::PRODUCTGROUP);
                break;

            case CmsUpdateType::POPULAR_PRODUCT:
            case CmsUpdateType::HIGHLIGHTED_PRODUCT:
                $this->flushCacheForTag(CacheTag::TOP_PRODUCT);
                break;

            case CmsUpdateType::FILTERGROUP:
            case CmsUpdateType::FILTERGROUP_PRODUCTGROUP:
                $this->flushCacheForTag(CacheTag::FILTERGROUP);
                break;

            case CmsUpdateType::APPROVAL:
            case CmsUpdateType::APPROVAL_IMAGE:
                $this->flushCacheForTag(CacheTag::APPROVAL);
                break;

            case CmsUpdateType::SOLUTION:
            case CmsUpdateType::SOLUTION_IMAGE:
                $this->flushCacheForTag(CacheTag::SOLUTION);
                break;

            case CmsUpdateType::APPLICATION:
            case CmsUpdateType::APPLICATION_IMAGE:
                $this->flushCacheForTag(CacheTag::APPLICATION);
                break;

            case CmsUpdateType::STORE:
                $this->flushCacheForTag(CacheTag::STORE);
                break;

            case CmsUpdateType::COUNTRY_LANGUAGE:
            case CmsUpdateType::COUNTRY_SUPPLIER:
            case CmsUpdateType::COUNTRY:
                $this->flushCacheForTag(CacheTag::COUNTRY);
                break;

            case CmsUpdateType::LANGUAGE:
                $this->flushCacheForTag([ CacheTag::LANGUAGE, CacheTag::COUNTRY]);
                break;

            case CmsUpdateType::EXTERNAL_PROJECT:
                $this->flushCacheForTag(CacheTag::EXTERNALPROJECT);
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
