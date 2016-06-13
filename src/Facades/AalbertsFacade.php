<?php
namespace Aalberts\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class AalbertsFacade
 *
 * @see \Aalberts\AalbertsHelper
 */
class AalbertsFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'aalberts-helper';
    }

}
