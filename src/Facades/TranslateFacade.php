<?php
namespace Aalberts\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TranslateFacade
 *
 * @see \Aalberts\Translation\Translator
 */
class TranslateFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'aalberts-translate';
    }

}
