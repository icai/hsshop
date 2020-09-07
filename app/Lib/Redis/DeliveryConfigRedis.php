<?php

namespace App\Lib\Redis;

/**
 * 外卖订单配置redis
 * Class DeliveryConfigRedis
 * @package App\Lib\Redis
 * @author 何书哲 2018年11月14日
 */

class DeliveryConfigRedis extends RedisInterface
{
    protected $prefixKey = 'delivery_config';
    protected $timeOut   = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function getRow($id)
    {
        return $this->redis->HGETALL($this->key . $id);
    }

    public function update($id, $data)
    {
        $id = trim($id);
        if ($this->redis->exists($this->key . $id)){
            return $this->redis->hmset($this->key . $id, $data);
        }
        return true;
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key . $id)){
            return $this->redis->del($this->key . $id);
        }
        return true;
    }
}