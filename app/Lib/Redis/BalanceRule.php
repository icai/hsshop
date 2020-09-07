<?php
namespace App\Lib\Redis;
class BalanceRule extends RedisInterface
{
    protected $prefixKey = 'balance_rule';
    protected $timeOut   = 86400;
    public function __construct($key = "") 
    {
        parent::__construct($key);
    }

    public function updateOne($id,$data)
    {
        if ($this->redis->exists($this->key . $id)){
            return $this->redis->hmset($this->key . $id,$data);
        }
        return true;
    }
}