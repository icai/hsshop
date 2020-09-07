<?php
namespace App\Lib\Redis;

/**
 * 小程序
 */
class Liteapp extends RedisInterface
{
    protected $prefixKey = 'liteapp';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}