<?php
namespace App\Lib\Redis;

class Region extends RedisInterface
{
    //regionall 所有
    //regionprovince 省份列表
    protected $prefixKey = 'region';
    protected $timeOut   = 2592000;

    public function __construct($key = "") 
    {
        parent::__construct($key);
    }

    public function set($data) 
    {
        $data = json_encode($data);
        $this->redis->SET($this->key, $data);
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }

    public function get()
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }
        $data = $this->redis->GET($this->key);
        return json_decode($data, true);
    }


    public function del($key)
    {
        $this->redis->del($this->prefixKey . ':' . $key);
    }
}
