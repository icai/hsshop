<?php
namespace App\Lib\Redis;

/**
 * 收藏redis类
 * @author 许立 2018年09月04日
 */
class FavoriteRedis extends RedisInterface
{
    protected $prefixKey = 'favorite';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}