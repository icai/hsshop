<?php
namespace App\Lib\Redis;

/**
 * 商品批发价设置
 */
class ProductWholesale extends RedisInterface
{
    protected $prefixKey = 'product_wholesale';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}