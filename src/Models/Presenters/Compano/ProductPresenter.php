<?php
namespace Aalberts\Models\Presenters\Compano;

use Aalberts\Models\Presenters\AbstractPresenter;

class ProductPresenter extends AbstractPresenter
{
    use ProductItemSharedTrait;

    /**
     * Name of manufacturer
     *
     * @return string
     */
    public function manufacturer()
    {
        return $this->entity->productmanufacturercode;
    }

    /**
     * String representation of 'category' (product group + type)
     *
     * @return string
     */
    public function category()
    {
        return $this->entity->productproductgroup
             . ($this->entity->productproducttype ? ' (' . $this->entity->productproducttype . ')' : '');
    }

    /**
     * @return string
     */
    public function etimClass()
    {
        return $this->entity->productclasscodeanddescription;
    }

    /**
     * @return string
     */
    public function intrastatCode()
    {
        return $this->entity->cbscode;
    }


    /**
     * Comma-separated list of shapes, with angle of bow attached if available
     *
     * @return null|string
     */
    public function shapesWithAngleOfBow()
    {
        $shapes = $this->shapes();

        return $shapes . ($shapes ? ' ' : '')
             . ($this->entity->productangleofbow ? '(' . $this->entity->productangleofbow . 'º)' : '');
    }

    /**
     * String representation of bore with full bore in parentheses
     * 
     * @return null|string
     */
    public function boreWithFullBore()
    {
        $bore = $this->entity->productbore . 'mm';
        
        return $bore . ($bore ? ' ' : '')
             . '('
             . ($this->entity->productfullbore == 'false'
                    ?   atrans(config('aalberts.translator.phrase-mapping.no'))
                    :   atrans(config('aalberts.translator.phrase-mapping.yes'))
                )
             . ')';
    }

    /**
     * String representation for operationg pressure for liquids
     *
     * @return null|string
     */
    public function maximumOperatingPressureLiquid()
    {
        if ( ! $this->entity->productmaximumoperatingpressureliquid) return null;

        return $this->entity->productmaximumoperatingpressureliquid
             . ' bar ('
             . atrans(config('aalberts.translator.phrase-mapping.liquid'))
             . ')';
    }

    /**
     * String representation for operationg pressure for gases
     *
     * @return null|string
     */
    public function maximumOperatingPressureGas()
    {
        if ( ! $this->entity->productmaxoperatingpressuregas) return null;

        return $this->entity->productmaxoperatingpressuregas
             . ' bar ('
             . atrans(config('aalberts.translator.phrase-mapping.gas'))
             . ')';
    }

    /**
     * String representation for operationg pressure for liquids and/or gases
     *
     * @return null|string
     */
    public function maximumOperatingPressure()
    {
        $liquid = $this->maximumOperatingPressureLiquid();
        $gas    = $this->maximumOperatingPressureGas();

        return $liquid
             . ($liquid && $gas ? '<br>' : '')
             . $gas;
    }

    /**
     * String representation for minimum operating temperature
     *
     * @return null|string
     */
    public function minMediumTemperature()
    {
        return $this->temperatureInCelsius($this->entity->productminmediumtemp);
    }

    /**
     * String representation for maximum operating temperature
     *
     * @return null|string
     */
    public function maxMediumTemperature()
    {
        return $this->temperatureInCelsius($this->entity->productmaxmediumtemp);
    }

    /**
     * String representation for peak operating temperature
     *
     * @return null|string
     */
    public function peakTemperature()
    {
        return $this->temperatureInCelsius($this->entity->productpeakmediumtemp);
    }

    /**
     * The product Kvs value should apparently only be displayed if it is the
     * same for all its items.
     * 
     * @return null|string
     */
    public function kvsValue()
    {
        if ( ! $this->entity->productkvsvalue) return null;
        
        if ($this->entity->items) {
            if (count($this->entity->items->pluck('productkvsvalue')->unique()) > 1) {
                return null;
            }
        }

        return $this->entity->productkvsvalue . ' m³/h';
    }
    

    /**
     * @return null|string
     */
    public function maxDischargeFlow()
    {
        if ( ! $this->entity->productmaxdischargeflow) return null;

        return $this->entity->productmaxdischargeflow . ' l/s';
    }

    /**
     * @return null|string
     */
    public function meshSize()
    {
        if ( ! $this->entity->productmeshsize) return null;

        return $this->entity->productmeshsize . ' mm';
    }

    /**
     * @return null|string
     */
    public function powerConsumption()
    {
        if ( ! $this->entity->productpowerconsumption) return null;

        return $this->entity->productpowerconsumption . ' W';
    }

}
