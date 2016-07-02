<?php
namespace Aalberts\Events;

class SearchPerformed extends Event
{
    
    /**
     * @var string
     */
    public $term;

    /**
     * @param string $term
     */
    public function __construct($term)
    {
        $this->term = $term;
    }

}
