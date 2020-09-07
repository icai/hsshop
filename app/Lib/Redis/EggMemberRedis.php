<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/3
 * Time: 17:54
 */

namespace App\Lib\Redis;


class EggMemberRedis extends RedisInterface
{
    protected $prefixKey = 'egg_member_log';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }
}