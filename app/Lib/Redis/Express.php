<?php
namespace App\Lib\Redis;

/**
 * 快递Redis
 */
class Express extends RedisInterface
{
    protected $prefixKey = 'express';
    protected $timeOut = 2592000;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * 获取数据
     * @return array
     */
    public function get()
    {
        if(!$this->redis->EXISTS($this->key)) {
            return false;
        }
        $data = $this->redis->GET($this->key);
        return json_decode($data, true);
    }

    /**
     * 设置数据
     */
    public function set($data)
    {
        $data = json_encode($data);
        $this->redis->SET($this->key, $data);
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }
}