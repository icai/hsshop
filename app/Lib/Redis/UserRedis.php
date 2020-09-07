<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:41
 */

namespace App\Lib\Redis;


class UserRedis extends RedisInterface
{
    protected $prefixKey = 'user';
    protected $timeOut   = 86400;
    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function set($data,$timeOut='60')
    {
        $this->redis->SET($this->key, $data);
        $this->redis->EXPIRE($this->key, $timeOut);
        return true;
    }

    public function get()
    {
       return $this->redis->GET($this->key);
    }

    /**
     * 设置单条hash记录
     * @param [integer] $id   [店铺id主键]
     * @param [array]   $data [要设置的数组]
     */
    public function setRow($id,$data) 
    {
        return $this->redis->hmset($this->key . $id, $data);
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

    /**
     * 增加(或减少)
     * $num int 增量(负数则是减少量)
     */
    public function loginIncr($num=1)
    {
        // 存在key值，所自增+1，如果不存在key值，设置该key为1，过期时间1小时
        if ($this->redis->EXISTS($this->key)) {
            $this->redis->INCR($this->key);
        }else {
            $this->set($num,3600);
        }
        return true;
    }

    public function del()
    {
        if ($this->redis->exists($this->key)){
            return $this->redis->del($this->key);
        }
        return true;
    }
}