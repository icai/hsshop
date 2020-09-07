<?php
namespace App\Lib\Redis;

/**
 * 商品规格
 */
class ProductMsg extends RedisInterface
{
    protected $prefixKey = 'product_msg';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}