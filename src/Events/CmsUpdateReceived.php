<?php
namespace Aalberts\Events;

/**
 * Class CmsUpdateReceived
 * 
 * Pusher 'update' event was received for the CMS.
 */
class CmsUpdateReceived extends Event
{
    
    /**
     * @var string
     */
    public $scope;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $data;


    /**
     * @param string $scope
     * @param string $type
     * @param array  $data
     */
    public function __construct($scope, $type, $data = [])
    {
        $this->scope = $scope;
        $this->type  = $type;
        $this->data  = $data;
    }

}
