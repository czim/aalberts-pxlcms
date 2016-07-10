<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Repositories\AbstractRepository;

abstract class AbstractCompanoRepository extends AbstractRepository
{
    /**
     * The default config key that returns the default TTL for the cache
     *
     * @var string
     */
    protected $defaultTtlConfigKey = 'aalberts.cache.ttl.compano';

}
