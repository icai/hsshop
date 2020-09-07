<?php
namespace App\Lib\Redis;
class MemberCard extends RedisInterface
{
    protected $prefixKey = 'member_card';
    protected $timeOut   = 86400;
    public function __construct($key = "") 
    {
        parent::__construct($key);
    }

    public function getRow($id)
    {
        return $this->redis->HGETALL($this->key . $id);
    }

    public function updateOne($id,$data)
    {
        if ($this->redis->exists($this->key . $id)){
            return $this->redis->hmset($this->key . $id,$data);
        }
        return true;
    }

}