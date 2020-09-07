<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/4/11
 * Time: 9:54
 */

namespace App\Lib\Redis;

class JPushRedis extends RedisInterface
{
    protected $prefixKey = 'jpush';
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


}