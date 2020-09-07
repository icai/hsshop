<?php
namespace App\Lib\Redis;

/**
 * 商品属性
 */
class ProductPropsToValues extends RedisInterface
{
    protected $prefixKey = 'product_props_to_values';
    protected $timeOut = 2592000;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}