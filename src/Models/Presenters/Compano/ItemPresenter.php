<?php
namespace Aalberts\Models\Presenters\Compano;

use App\Models\Aalberts\Compano\Item;
use App\Models\Aalberts\Compano\Product;

class ItemPresenter extends ProductPresenter
{
    use ProductItemSharedTrait;

    /**
     * @var Item
     */
    protected $entity;

    /**
     * String representation of item/article name/title
     *
     * @param string $translationKey
     * @return string
     */
    public function title($translationKey = 'products.article-of')
    {
        return $this->entity->description ?: atrans($translationKey) . $this->entity->product->present()->subTitle;
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
     * @param null|Product $product
     * @return null|string
     */
    public function manufacturer(Product $product = null)
    {
        if ( ! $this->entity->product) return null;

        if ($product) {
            return $product->present()->manufacturer;
        }

        return $this->ensureProductIsLoaded()->entity->product->present()->manufacturer;
    }

    /**
     * String GTIN, with fallback to product GTIN if not set for item
     *
     * @return string
     */
    public function gtin()
    {
        if ($this->entity->gtin) {
            return (string) $this->entity->gtin;
        }

        return (string) $this->entity->productgtin;
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
                return $this->ensureProductIsLoaded()->entity->product->productshowinsertiondepthweb ? $this->entity->{head($properties)} : null;

            // default case is: one value expected, one used as is
            default:
                return $this->entity->{head($properties)};
        }
    }

    /**
     * @return $this
     */
    protected function ensureProductIsLoaded()
    {
        if ( ! $this->entity->relationLoaded('product')) {
            $this->entity->load('product');
        }

        return $this;
    }

    /**
     * Returns whether the item/product has a technical drawing with dimensions.
     *
     * @return bool
     */
    public function hasTechnicalDrawing()
    {
        return (strlen(trim($this->entity->drawing)) && $this->entity->product->productshowtechnicalsketchweb != 'false');
    }

}
