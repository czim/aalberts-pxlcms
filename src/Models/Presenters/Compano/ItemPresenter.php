<?php
namespace Aalberts\Models\Presenters\Compano;

class ItemPresenter extends ProductPresenter
{
    use ProductItemSharedTrait;


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
     * String representation of packaging
     * 
     * @return string
     */
    public function packaging()
    {
        return $this->entity->packaging;
    }



    /**
     * Comma-separated list of product types
     *
     * @return null|string
     */
    public function producttypes()
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

}
