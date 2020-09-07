<?php
namespace App\Lib\Redis;

/**
 * 商品
 */
class Product extends RedisInterface
{
    protected $prefixKey = 'product';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function batchUpdateRedis($data)
    {
         foreach ($data as $val){
             $this->updateRow($val);
          }
    }


}