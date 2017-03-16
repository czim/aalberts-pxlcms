<?php
namespace Aalberts\Enums;

use MyCLabs\Enum\Enum;

class DownloadCategory extends Enum
{
    const DOCUMENT    = 'doc';
    const CERTIFICATE = 'cer';
    const CAD_DRAWING = 'cad';
    const TENDER_TEXT = 'ten';
    const IMAGE       = 'img';
    const PRESS       = 'prs';
    const MANUAL      = 'man';
    const CONDITIONS  = 'con';

    const BROCHURE           = 'bro';
    const DATA_SHEET         = 'dat';
    const SPARE_PART         = 'spa';
    const INSTRUCTION_MANUAL = 'ins';
}
