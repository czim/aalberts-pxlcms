<?php
namespace Aalberts\Data;

use Czim\DataObject\AbstractDataObject;

/**
 * Class ConnectionSet
 *
 * Contains data for a product or item connection set, normalized
 * as in the original aalberts models.
 *
 * @property int                                              $index
 * @property null|\App\Models\Aalberts\Compano\Connectiontype $type
 * @property null|\App\Models\Aalberts\Compano\Contourcode    $contour
 */
class ConnectionSet extends AbstractDataObject
{
}
