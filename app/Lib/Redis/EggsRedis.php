<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 16:43
 */

namespace App\Lib\Redis;


class EggsRedis extends RedisInterface
{
    protected $prefixKey = 'Eggs';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }


    
}