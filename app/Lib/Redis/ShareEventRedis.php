<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/12/6
 * Time: 23:35
 */

namespace App\Lib\Redis;


class ShareEventRedis extends RedisInterface
{
    protected $prefixKey = 'ShareEvent';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }

    public function updateOne($id, $data)
    {
        if ($this->redis->exists($this->key . $id)){
            return $this->redis->hmset($this->key . $id,$data);
        }
        return true;
    }

    public function increment($id, $field, $num = 1)
    {
        if ( $this->redis->HEXISTS($this->key . $id, $field) ) {
            $value = $this->redis->HGET($this->key . $id, $field);
            $value = get_numeric($value);
            if ( is_int($value) ) {
                return $this->redis->HINCRBY($this->key . $id, $field, $num);
            } elseif ( is_float($value) ) {
                return $this->redis->HINCRBYFLOAT($this->key . $id, $field, $num);
            }
        }
    }
}