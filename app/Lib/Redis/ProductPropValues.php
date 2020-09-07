<?php
namespace App\Lib\Redis;

/**
 * 商品属性
 */
class ProductPropValues extends RedisInterface
{
    protected $prefixKey = 'product_prop_values';
    protected $timeOut = 2592000;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * 获取一个详情
     */
    public function get($id)
    {
        return $this->redis->HGETALL($this->key . $id);
    }
}