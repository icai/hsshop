<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:41
 */

namespace App\Lib\Redis;


class DistributeApplayPageRedis extends RedisInterface
{
    protected $prefixKey = 'distribute_applay_page';
    protected $timeOut   = 86400;
    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function getRow($id)
    {
        return $this->redis->HGETALL($this->key . $id);
    }

    public function update($id,$data)
    {
        $id = trim($id);
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->hmset($this->key.$id,$data);
        }
        return true;
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->del($this->key.$id);
        }
        return true;
    }

    public function increment($id, $field, $num = 1)
    {
        if ( $this->redis->HEXISTS($this->key . $id, $field) ) {
            $value = $this->redis->HGET($this->key . $id, $field);
            $value = get_numeric($value);
            //if ( $value > 0 ) {
            if ( is_int($value)  ) {
                $num = intval($num);
                return $this->redis->HINCRBY($this->key . $id, $field, $num);
            } elseif ( is_float($value) ) {
                return $this->redis->HINCRBYFLOAT($this->key . $id, $field, $num);
            }
            //}
        }
    }

}