<?php
namespace App\Lib\Redis;

/**
 * 注册信息redis
 */
class LiFriendRedis extends RedisInterface
{
    protected $prefixKey = 'li_friend';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function setShareData($id, $data)
    {
        if ($this->redis->EXISTS($this->key . $id)) {
            $this->redis->HMSET($this->key . $id, $data);
            $this->redis->EXPIRE($this->key . $id, $this->timeOut);
        }
        return true;
    }
    public function addLiDetail($id, $data)
    {
        $key = $this->key . $id;
        $this->redis->HMSET($key, $data);
        $this->redis->EXPIRE($key, $this->timeOut);
    }

    public function setLiFriendToStr($key,$data)
    {
        if ($this->redis->EXISTS($this->key . $key)) {
            $this->redis->SET($this->key . $key, json_encode($data));
        }
        return true;
    }

    public function getRowStrByWhere($where)
    {
        $keys = array_keys($where);
        $key = $this->key.join(':',$keys);
        return $this->redis->GET($key);
    }

    public function setAdd($key,array $mids)
    {
        if (!$this->redis->EXISTS($this->key . $key)) {
            return;
        }
        return $this->redis->pipeline(function ($pipe) use ($key,$mids) {
            foreach ($mids as $id) {
                $pipe->sadd($this->key . $key,$id);
            }
        });
    }

    public function getSmembers($key)
    {
        return $this->redis->smembers($this->key.$key);
    }
}