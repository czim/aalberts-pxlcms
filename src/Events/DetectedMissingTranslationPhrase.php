<?php
namespace Aalberts\Events;

class DetectedMissingTranslationPhrase extends Event
{
    /**
     * @var string
     */
    public $phrase;

    /**
     * @param string $phrase
     */
    public function __construct($phrase)
    {
        $this->phrase = $phrase;
    }

}
