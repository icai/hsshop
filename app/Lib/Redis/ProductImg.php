<?php
namespace App\Lib\Redis;

/**
 * 商品图片
 */
class ProductImg extends RedisInterface
{
    protected $prefixKey = 'product_img';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}