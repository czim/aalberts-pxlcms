<?php
namespace Aalberts\Filters;

use Czim\Filter\CountableFilter;

abstract class AbstractFilter extends CountableFilter
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
