<?php
namespace Aalberts\Filters\ParameterFilters;

use Czim\Filter\Contracts\ParameterFilterInterface;

abstract class AbstractParameterFilter implements ParameterFilterInterface
{

    /**
     * The default config key that returns the default TTL for the cache
     *
     * @var string
     */
    protected $defaultTtlConfigKey = 'aalberts.cache.ttl.cms';

    /**
     * Returns the default TTL for the cache
     *
     * @return null|int
     */
    protected function defaultTtl()
    {
        return config($this->defaultTtlConfigKey);
    }

}
