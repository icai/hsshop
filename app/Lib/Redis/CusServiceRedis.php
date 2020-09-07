<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/11
 * Time: 11:36
 */

namespace App\Lib\Redis;


class CusServiceRedis extends RedisInterface
{
    protected $prefixKey = 'CusSerManage';
  
    public function __construct($key = '')
    {
        parent::__construct($key);
    }

    /**
     * 将客服信息存入Redis
     * @return mixed
     */
    public function set($data = [])
    {
        return $this->redis->HMSET($this->key,$data);
    }

    /**
     * todo 更改缓存中的数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return true;
        }
        return $this->redis->HMSET($this->key,$data);
    }
    
    /**
     * todo 通过key删除hash类型的缓存
     * @param $key
     * @return bool
     */
    public function deleteByKey()
    {
        return $this->redis->DEL($this->key);
    }

    /**
     * todo 获取Hash类型 某个key的对应的数值
     * @param $key
     * @return bool
     */
    public function getValueByKey()
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }
        return $this->redis->HGETALL($this->key);
    }



}