<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/9/14
 * Time: 10:39
 */

namespace App\Lib\Redis;


class GroupMemberRedis extends RedisInterface
{
    protected $prefixKey = 'groupMember';
    protected $timeOut   = 7200; //2个小时有效期

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function get()
    {
        if ($re = $this->redis->lrange($this->key ,0 ,1)) {
            $this->redis->EXPIRE($this->key,$this->timeOut);
        }
        return $re;
    }

    /**
     * 只保存2个用户的mid
     * @param mid 用户id
     * @author: 梅杰 2018年9月14号
     * @return bool
     */
    public function add($mid)
    {
        $redis = $this->redis;
        return $redis->LPUSH($this->key,$mid)
            && $redis->ltrim($this->key ,0 ,1)
            && $redis->EXPIRE($this->key,$this->timeOut);
    }
}