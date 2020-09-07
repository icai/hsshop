<?php
namespace App\Lib\Redis;

/**
 * 注册信息redis
 */
class LiDetail extends RedisInterface
{
    protected $prefixKey = 'li_detail';
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
}