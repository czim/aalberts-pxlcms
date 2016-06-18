<?php
namespace Aalberts\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class CmsUpdateActions
 *
 * Note about upload & remove:
 * These actions indicate only that a file was uploaded or removed in a form.
 * They are fired before the parent record of the file/image is actually saved.
 */
class CmsUpdateActions extends Enum
{
    const ACTION_CREATE     = 'create';
    const ACTION_EDIT       = 'edit';
    const ACTION_DELETE     = 'delete';
    const ACTION_SORT       = 'sort';       // any change to order
    const ACTION_ACTIVE     = 'active';     // toggle active status
    const ACTION_DEFAULT    = 'default';    // item set as the default

    // actions related to file uploads
    const ACTION_UPLOAD     = 'upload';
    const ACTION_REMOVE     = 'remove';

}
