<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:41
 */

namespace App\Lib\Redis;


class BaiduRedisClient extends RedisInterface
{
    protected $prefixKey = 'baiduapp:';
    protected $timeOut   = 86400;
    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function getRedisClient()
    {
        return $this->redis;
    }





}