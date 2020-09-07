<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 17:28
 */

namespace App\Lib\Redis;


class EggsSkuRedis extends RedisInterface
{
    protected $prefixKey = 'EggsSku';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }
}