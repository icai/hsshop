<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/8/13
 * Time: 9:33
 */

namespace App\Lib\Redis;


class NewUserFlagRedis extends RedisInterface
{
    protected $prefixKey = 'newUser';
    //过期时间默认为秒 过期时间为30s
//    protected $timeOut   = 30;


    public function set($mid)
    {
        return $this->redis->set($this->key.$mid,1)->getPayload();
    }

    public function get($mid)
    {
        return $this->redis->get($this->key.$mid);
    }



}