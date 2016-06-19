<?php
namespace Aalberts\Enums;

use MyCLabs\Enum\Enum;

class CacheTags extends Enum
{
    
    const TRANSLATION = 'translation';
    const COUNTRY     = 'country';

    const CMP_PRODUCT     = 'cmp-product';
    const CMP_SUPPLIER    = 'cmp-supplier';
    const CMP_MISC        = 'cmp-misc';     // pumpbrand, measurements, random stuff (catch-all)

    const TOP_PRODUCT = 'top-product';

    const CONTENT      = 'content';
    const NEWS         = 'news';
    const PROJECT      = 'project';
    const DOWNLOAD     = 'download';
    const CMS_FUNCTION = 'cms-function';

    const RELATEDPRODUCT = 'relatedproduct';
    const CUSTOMBLOCK    = 'customblock';

    // combined all the press dimensions, tools, remarks, etc
    const PRESS = 'press';


}
