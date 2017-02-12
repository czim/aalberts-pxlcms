<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Data\ConnectionSet;
use Illuminate\Support\Collection;

trait ProductItemSharedTrait
{
    
    /**
     * Image URL
     *
     * @param null|string $sizeQuery    size querystring: "W=316&H=316"
     * @return null|string
     */
    public function image($sizeQuery = null)
    {
        if ( ! $this->entity->image) return null;

        return $this->decorateUrlWithCompanoHost($this->entity->image, null, $sizeQuery);
    }

    /**
     * Technical drawing URL
     *
     * @param null|string $sizeQuery    size querystring: "W=316&H=316"
     * @return null|string
     */
    public function drawing($sizeQuery = null)
    {
        if ( ! $this->entity->drawing) return null;

        return $this->decorateUrlWithCompanoHost($this->entity->drawing, null, $sizeQuery);
    }


    // ------------------------------------------------------------------------------
    //      Connection types
    // ------------------------------------------------------------------------------

    /**
     * Unique connection type list with contours in parentheses, labels only.
     *
     * @return null|string
     */
    public function connectionTypeString()
    {
        return implode(
            ', ',
            array_filter(
                $this->connectionSets()
                    ->map(function (ConnectionSet $set) {
                        if ( ! $set->type) return null;
                        
                        return $set->type->label
                             . ($set->contour ? ' (' . $set->contour->label . ')' : '');
                    })
                    ->toArray()
            )
        );
    }

    /**
     * Normalized, connection-number limited set of matched connection types & contours
     *
     * @return Collection|ConnectionSet[]
     */
    public function connectionSets()
    {
        // VSH has apparently deprecated productnumberofconnections, so don't trust it.
        //$number = ($this->entity instanceof ProductModel)
        //    ?   $this->entity->productnumberofconnections
        //    :   $this->entity->product->productnumberofconnections;

        // if the number of connections is known, it should overrule the actual amount
        // of available types or contours. The types and contours may also have duplicates,
        // so they must be reduced to unique values.
        $types    = $this->uniqueConnectionTypes();
        $contours = $this->uniqueContourCodes();

        $number = max(count($types), count($contours));

        $sets = collect();

        for ($x = 0; $x < $number; $x++) {
            $sets->push(
                new ConnectionSet([
                    'type'    => $types->get($x),
                    'contour' => $contours->get($x),
                ])
            );
        }

        return $sets;
    }

    /**
     * @return string
     */
    public function uniqueConnectionTypesString()
    {
        return implode(', ', $this->uniqueConnectionTypes()->pluck('label')->toArray());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function uniqueConnectionTypes()
    {
        return $this->entity->connectiontypes->unique();
    }

    /**
     * @return string
     */
    public function uniqueContourCodesString()
    {
        return implode(', ', $this->uniqueContourCodes()->pluck('label')->toArray());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function uniqueContourCodes()
    {
        return $this->entity->contourcodes->unique();
    }

    /**
     * Comma-separated list of connection types
     *
     * Note that this contains neither the (related?) contour codes,
     * nor are the entries unique.
     *
     * @see connectionTypeString
     *
     * @return null|string
     */
    public function connectiontypes()
    {
        if ( ! $this->entity->connectiontypes) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->connectiontypes->map(function ($connectiontype) {
                    /** @var \App\Models\Aalberts\Compano\Connectiontype $connectiontype */
                    return $connectiontype->label;
                })->toArray()
            )
        );
    }

    /**
     * Comma-separated list of connection types
     *
     * Note that the entries are not unique.
     *
     * @see connectionTypeString
     *
     * @return null|string
     */
    public function contourcodes()
    {
        if ( ! $this->entity->contourcodes) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->contourcodes->map(function ($contourcode) {
                    /** @var \App\Models\Aalberts\Compano\Contourcode $contourcode */
                    return $contourcode->label;
                })->toArray()
            )
        );
    }
    
    
    // ------------------------------------------------------------------------------
    //      Concatenated string representation
    // ------------------------------------------------------------------------------

    /**
     * Comma-separated list of colors
     *
     * @return null|string
     */
    public function colors()
    {
        if ( ! $this->entity->colors) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->colors->map(function ($color) {
                    /** @var \App\Models\Aalberts\Compano\Color $color */
                    return $color->label;
                })->toArray()
            )
        );
    }

    /**
     * Comma-separated list of finishings
     *
     * @return null|string
     */
    public function finishings()
    {
        if ( ! $this->entity->finishings) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->finishings->map(function ($finishing) {
                    /** @var \App\Models\Aalberts\Compano\Finishing $finishing */
                    return $finishing->label;
                })->toArray()
            )
        );
    }

    /**
     * Comma-separated list of materials
     *
     * @return null|string
     */
    public function materials()
    {
        if ( ! $this->entity->materials) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->materials->map(function ($material) {
                    /** @var \App\Models\Aalberts\Compano\Material $material */
                    return $material->label;
                })->toArray()
            )
        );
    }

    /**
     * Comma-separated list of materials with material quality in parentheses
     *
     * @return null|string
     */
    public function materialsWithQuality()
    {
        $materials = $this->materials();
        $quality   = $this->entity->productmaterialquality ? ' (' . $this->entity->productmaterialquality . ')' : null;

        return ($materials || $quality) ? trim($materials . $quality) : null;
    }

    /**
     * Comma-separated list of sealings
     *
     * @return null|string
     */
    public function sealings()
    {
        if ( ! $this->entity->sealings) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->sealings->map(function ($sealing) {
                    /** @var \App\Models\Aalberts\Compano\Sealing $sealing */
                    return $sealing->label;
                })->toArray()
            )
        );
    }

    /**
     * Comma-separated list of shapes
     *
     * @return null|string
     */
    public function shapes()
    {
        if ( ! $this->entity->shapes) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->shapes->map(function ($shape) {
                    /** @var \App\Models\Aalberts\Compano\Shape $shape */
                    return $shape->label;
                })->toArray()
            )
        );
    }

}
