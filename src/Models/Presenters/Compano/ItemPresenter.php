<?php
namespace Aalberts\Models\Presenters\Compano;

class ItemPresenter extends ProductPresenter
{
    use ProductItemSharedTrait;

    /**
     * String representation of item/article name/title
     *
     * @return string
     */
    public function title()
    {
        return $this->entity->description;
    }

    /**
     * String representation of article code/number
     *
     * @return string
     */
    public function articleNumber()
    {
        $gtin = $this->gtin();

        return trim(
            $this->entity->code
            . ($gtin ? ' (' . $gtin . ')' : '')
        );
    }

    /**
     * Name of manufacturer (of parent product)
     *
     * @return null|string
     */
    public function manufacturer()
    {
        if ( ! $this->entity->product) return null;

        return $this->entity->product->present()->manufacturer;
    }

    /**
     * String GTIN, with fallback to product GTIN if not set for item
     *
     * @return string
     */
    public function gtin()
    {
        return (string) $this->entity->gtin ?: $this->entity->product->gtin;
    }

    /**
     * String representation of packaging
     * 
     * @return string
     */
    public function packaging()
    {
        $unit = trim($this->entity->utilizationorderratio . ' ' . $this->entity->utilizationunitcode);

        if ( ! $this->entity->packaging && ! $unit) return null;

        return trim(
            $this->entity->packaging  . ' ' . ($unit ? '(' . $unit . ')' : '')
        );
    }

    /**
     * String representation of net weight with unit
     *
     * @return null|string
     */
    public function weightWithUnit()
    {
        if (null === $this->entity->productnetweight) return null;

        return $this->entity->productnetweight . ' ' . $this->entity->productweightunitcode;
    }

    /**
     * String representation of l x w x h
     *
     * @return null|string
     */
    public function lengthWidthHeight()
    {
        if ( ! $this->entity->productvl || ! $this->entity->productvb || ! $this->entity->productvh) return null;

        return $this->entity->productvl . '&times;' . $this->entity->productvb . '&times;' . $this->entity->productvh;
    }


    /**
     * Comma-separated list of product types
     *
     * @return null|string
     */
    public function productTypes()
    {
        if ( ! $this->entity->producttypes) return null;

        return implode(
            ', ',
            array_filter(
                $this->entity->producttypes->map(function ($producttype) {
                    /** @var \App\Models\Aalberts\Compano\Producttype $producttype */
                    return $producttype->label;
                })->toArray()
            )
        );
    }

    /**
     * @return null|string
     */
    public function kvsValue()
    {
        if ( ! $this->entity->productkvsvalue) return null;

        return $this->entity->productkvsvalue . ' m³/h';
    }

    /**
     * Note the difference with kv-S-Value!
     *
     * @return null|string
     */
    public function kvValue()
    {
        return $this->entity->productkvwaarde;
    }

    /**
     * String representation of size (maat)
     *
     * @return string
     */
    public function size()
    {
        return $this->entity->productsize_description;
    }

    /**
     * List of item header display values which have a filled in value
     * for the item. Used to draw dimensions list for the article page.
     *
     * @return string[]
     */
    public function dimensionHeaders()
    {
        $fields = app('aalberts-helper')->itemTableDimensionProperties();

        if ( ! count($fields)) return [];

        $filled = [];

        foreach ($fields as $field => $header) {
            if (empty($this->entity->{$field})) continue;

            $filled[ $header ] = $field;
        }

        return array_keys($filled);
    }


    /**
     * Display value for a given dimension header
     *
     * @see dimensionHeaders
     *
     * @param string $header
     * @return string
     */
    public function dimensionValue($header)
    {
        $properties = array_keys(
            app('aalberts-helper')->itemTableDimensionProperties(),
            $header
        );

        if ( ! count($properties)) return null;

        // some exceptions apply
        switch ($header) {

            // diameter should be a combination of external/nominal or either
            case 'd0':
            case 'd1':
            case 'd2':
            case 'd3':
            case 'd4':
            case 'd5':
            case 'd6':
            case 'd7':
            case 'd8':
                $external = null;
                $nominal  = null;

                foreach ($properties as $property) {
                    if (starts_with($property, 'productexternal')) {
                        $external = $this->entity->{$property};
                    } elseif (starts_with($property, 'productnominal')) {
                        $nominal = $this->entity->{$property};
                    }
                }

                if ($external && $nominal) {
                    $nominal = '(' . $nominal . ')';
                }

                return trim($external . ' ' . $nominal);

            // insertion depth should only show if product allows it
            case 'es0':
            case 'es1':
            case 'es2':
            case 'es3':
            case 'es4':
            case 'es5':
            case 'es6':
            case 'es7':
            case 'es8':
                return $this->entity->product->productshowinsertiondepthweb ? $this->entity->{head($properties)} : null;

            // default case is: one value expected, one used as is
            default:
                return $this->entity->{head($properties)};
        }
    }

}
