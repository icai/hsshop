<?php

namespace Illuminate\Support\Facades;

/**
 * @see \Illuminate\RedisString\RedisString
 */
class RedisString extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'RedisString';
    }
}
