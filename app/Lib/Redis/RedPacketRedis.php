<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/1/9
 * Time: 9:54
 */

namespace App\Lib\Redis;


class RedPacketRedis extends RedisInterface
{
    protected $prefixKey = 'RedPacket';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }

}