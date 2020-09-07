<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/3
 * Time: 8:32
 */

namespace App\Lib\Redis;


class EggPrizeRedis extends RedisInterface
{
    protected $prefixKey = 'egg_prize';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }
}