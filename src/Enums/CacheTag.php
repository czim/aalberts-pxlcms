<?php
namespace Aalberts\Enums;

use MyCLabs\Enum\Enum;

class CacheTag extends Enum
{

    // ------------------------------------------------------------------------------
    //      Compano
    // ------------------------------------------------------------------------------

    const CMP_PRODUCT     = 'cmp-product';
    const CMP_SUPPLIER    = 'cmp-supplier';
    const CMP_MISC        = 'cmp-misc';     // pumpbrand, measurements, random stuff (catch-all)


    // ------------------------------------------------------------------------------
    //      CMS
    // ------------------------------------------------------------------------------

    const TRANSLATION = 'translation';
    const COUNTRY     = 'country';
    const LANGUAGE    = 'language';

    const CONTENT      = 'content';
    const NEWS         = 'news';
    const PROJECT      = 'project';
    const DOWNLOAD     = 'download';
    const CMS_FUNCTION = 'cms-function';

    const RELATEDPRODUCT = 'relatedproduct';
    const CUSTOMBLOCK    = 'customblock';

    // combined all the press dimensions, tools, remarks, etc
    const PRESS = 'press';

    const PRODUCTGROUP = 'productgroup';
    const FILTERGROUP  = 'filtergroup';

    // combined popular & highlighted product
    const TOP_PRODUCT = 'top-product';

    const APPROVAL    = 'approval';
    const SOLUTION    = 'solution';
    const APPLICATION = 'application';

    const STORE = 'store';

    const EXTERNALPROJECT = 'external-project';

}
