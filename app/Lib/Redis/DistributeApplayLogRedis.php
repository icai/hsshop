<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:41
 */

namespace App\Lib\Redis;


class DistributeApplayLogRedis extends RedisInterface
{
    protected $prefixKey = 'distribute_applay_Log';
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

    public function batchUpdate($ids,$data)
    {
        $data = [$ids,$data];
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data[0] as $val) {
                if ($this->redis->EXISTS($this->key . $val)){
                    $pipe->HMSET($this->key . $val, $data[1]);
                    $pipe->EXPIRE($this->key . $val, $this->timeOut);
                }
            }
        });
    }
}