<?php
namespace App\Lib\Redis;

/**
 * 小程序
 */
class LiteappHistory extends RedisInterface
{
    protected $prefixKey = 'liteapp_history';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}