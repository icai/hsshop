<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:41
 */

namespace App\Lib\Redis;


class UnifiedMemberRedis extends RedisInterface
{
    protected $prefixKey = 'unified_member';
    protected $timeOut   = 86400;
    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function getRow($id)
    {
        return $this->redis->HGETALL($this->key . $id);
    }

    public function update($id,$data)
    {
        $id = trim($id);
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->hmset($this->key.$id,$data);
        }
        return true;
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->del($this->key.$id);
        }
        return true;
    }

    public function getIdByOpenid($openId)
    {
        return $this->redis->get($this->key . $openId);
    }

    public function setIdByOpenid($openId, $id)
    {
        $this->redis->SET($this->key . $openId, $id);
        $this->redis->EXPIRE($this->key . $openId, $this->timeOut);
    }
}