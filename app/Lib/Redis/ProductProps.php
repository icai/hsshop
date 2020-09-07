<?php
namespace App\Lib\Redis;

/**
 * 商品属性
 */
class ProductProps extends RedisInterface
{
    protected $prefixKey = 'product_props';
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
        return json_decode($this->redis->GET($this->key), true);
    }

    /**
     * 设置数据
     */
    public function set($data)
    {
        $this->redis->SET($this->key, json_encode($data));
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }
}