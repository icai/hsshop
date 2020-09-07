<?php
namespace App\Lib\Redis;

/**
 * 商品属性
 */
class ProductSku extends RedisInterface
{
    protected $prefixKey = 'product_sku';
    protected $timeOut = 2592000;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}