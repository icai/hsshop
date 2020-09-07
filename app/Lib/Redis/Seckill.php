<?php
namespace App\Lib\Redis;

/**
 * 秒杀活动
 */
class Seckill extends RedisInterface
{
    protected $prefixKey = 'seckill';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}