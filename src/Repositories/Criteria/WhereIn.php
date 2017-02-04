<?php
namespace Aalberts\Repositories\Criteria;

use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Builder;

class WhereIn extends AbstractCriteria
{
    /**
     * @var string field to where for
     */
    protected $field;

    /**
     * @var array|mixed to check for
     */
    protected $values;


    public function __construct($field, $values)
    {
        $this->field  = $field;
        $this->values = $values;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    public function applyToQuery($model)
    {
        if (is_array($this->values) || $model instanceof Arrayable) {
            return $model->whereIn($this->field, $this->values);
        }

        return $model->where($this->field, $this->values);
    }
}
