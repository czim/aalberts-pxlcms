<?php
namespace Aalberts\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class CmsUpdateTypes
 */
class CmsUpdateTypes extends Enum
{

    // for COUNTRY & LANGUAGE the update signifies something different based on the channel
    // global (cms-all)     : updates to the record's table itself;
    // organization (cms-#) : activated or set default for that organization (relational)
    const COUNTRY = 'country';
    const LANGUAGE = 'language';

    const COUNTRY_LANGUAGE = 'country.language';
    const COUNTRY_SUPPLIER = 'country.supplier';

    const APPLICATION = 'application';
    const APPLICATION_IMAGE = 'application.image';

    const APPROVAL = 'approval';
    const APPROVAL_IMAGE = 'approval.image';

    const RELATEDPRODUCT = 'relatedproduct';
    const RELATEDPRODUCT_IMAGE = 'relatedproduct.image';
    const CUSTOMBLOCK = 'customblock';
    const CUSTOMBLOCK_IMAGE = 'customblock.image';

    const CONTENT = 'content';
    const CONTENT_GALLERY = 'content.gallery';
    const CONTENT_GALLERY_IMAGE = 'content.gallery.image';
    const CONTENT_RELATED_PRODUCTS = 'content.related-products';
    const CONTENT_RELATED_NEWS = 'content.related-news';
    const CONTENT_RELATED_PROJECTS = 'content.related-projects';
    const CONTENT_CUSTOM_BLOCKS = 'content.custom-blocks';
    const CONTENT_TILE = 'content.tile';
    const CONTENT_TILE_IMAGE = 'content.tile.image';
    const CONTENT_DOWNLOAD = 'content.download';

    const NEWS = 'news';
    const NEWS_GALLERY = 'news.gallery';
    const NEWS_GALLERY_IMAGE = 'news.gallery.image';
    const NEWS_RELATED_PRODUCTS = 'news.related-products';

    const DOWNLOAD = 'download';
    const DOWNLOAD_IMAGE = 'download.image';
    const DOWNLOAD_FILE = 'download.file';

    const EXTERNAL_PROJECT = 'external-project';

    // this is the filtergroup TRANSLATION, not the actual cmp_filtergroup!
    const FILTERGROUP = 'filtergroup';
    // this is the connection between a filter & productgroup (in Filters module)
    const FILTERGROUP_PRODUCTGROUP = 'filtergroup.productgroup';

    // model follows the same name change, but CMS calls it 'function'
    const PROJECTFUNCTION = 'function';

    const HIGHLIGHTED_PRODUCT = 'highlighted-product';
    const POPULAR_PRODUCT = 'popular-product';


    // ------------------------------------------------------------------------------
    //      Press tools
    // ------------------------------------------------------------------------------

    const PRESS              = 'press';
    const PRESS_DIMENSION    = 'press.dimension';
    const PRESS_MANUFACTURER = 'press.manufacturer';
    const PRESS_PRODUCTLINE  = 'press.productline';
    const PRESS_TOOL         = 'press.tool';
    const PRESS_REMARK       = 'press.remark';
    const PRESS_LOOKUP       = 'press.lookup';

}
