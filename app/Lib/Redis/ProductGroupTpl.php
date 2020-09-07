<?php
namespace App\Lib\Redis;

/**
 * 商品分组模板
 */
class ProductGroupTpl extends RedisInterface
{
    protected $prefixKey = 'product_group_tpl';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}