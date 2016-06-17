<?php
namespace Aalberts\Listeners;

use Aalberts\Events\DetectedMissingTranslationPhrase;
use Aalberts\Facades\TranslateFacade;
use Log;

/**
 * Class MissingTranslationPhraseListener
 *
 * When listening for missing translation phrases, this will automatically
 * add the phrase to the database.
 */
class MissingTranslationPhraseListener
{

    /**
     * Handle the event.
     *
     * @param DetectedMissingTranslationPhrase $event
     */
    public function handle(DetectedMissingTranslationPhrase $event)
    {
        // only do this if enabled in the environment
        if ( ! config('aalberts.translator.add-phrases')) return;

        if ( ! TranslateFacade::addPhrase($event->phrase)) {
            Log::warning("Failed to add translation phrase '{$event->phrase}' to the Aalberts CMS!");
        }
    }

}
