<?php
namespace Aalberts\Data;

use App\Models\Aalberts\Cms\Filter;
use Czim\DataObject\AbstractDataObject;
use Illuminate\Support\Collection;

/**
 * Class ProductFilterGroup
 *
 * @property int  $id                       of the cms_filtergroup
 * @property string $name
 * @property string $filters                list of filter strings
 * @property string $ml_name                translated label
 * @property Collection|Filter[] $children
 */
class ProductFilterGroup extends AbstractDataObject
{

    /**
     * Returns displayable label, translated if possible.
     *
     * @return string
     */
    public function label()
    {
        if ($this->ml_name) {
            return $this->ml_name;
        }

        return $this->name;
    }

    /**
     * @return int[]
     */
    public function filterIds()
    {
        if ( ! $this->filters) {
            return [];
        }

        return explode(',', $this->filters);
    }

    /**
     * @return Collection|Filter[]
     */
    public function children()
    {
        return $this->children ?: new Collection;
    }

}
