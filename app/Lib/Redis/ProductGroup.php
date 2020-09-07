<?php
namespace App\Lib\Redis;

/**
 * 商品分组
 */
class ProductGroup extends RedisInterface
{
    protected $prefixKey = 'product_group';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function getDataFromKey($key='')
    {
    	return $this->redis->get($this->prefixKey.$key);
    }

    public function setDataByKey($key='',$data) 
    {
    	return $this->redis->set($this->prefixKey.$key,$data);
    }
}