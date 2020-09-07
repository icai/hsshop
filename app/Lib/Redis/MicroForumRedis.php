<?php
/**
 * Created by PhpStorm.
 * User: johnson
 * Date: 8/5/17
 * Time: 12:39 PM
 */

namespace App\Lib\Redis;


class MicroForumRedis extends RedisInterface
{
    protected $prefixKey = 'micro_forum';
    //过期时间默认为秒 过期时间为30s
    protected $timeOut   = 30;

}