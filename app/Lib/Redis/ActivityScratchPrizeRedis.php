<?php

namespace App\Lib\Redis;


class ActivityScratchPrizeRedis extends RedisInterface
{
    protected $prefixKey = 'activity_scratch_prize';
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

    public function decrement($id,$num)
    {
        $num = -$num;
        if ($this->redis->exists($this->key.$id)) {
            return $this->redis->hincrby($this->key . $id, 'num', $num);
        }
        return true;
    }

}