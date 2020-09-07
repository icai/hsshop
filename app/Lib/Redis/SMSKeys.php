<?php
namespace App\Lib\Redis;
use Redisx;

class SMSKeys extends RedisInterface
{
    protected $prefixKey = 'sms_key_';
    protected $timeOut   = 600;

    public function __construct($key = "") 
    {
        parent::__construct($key);
    }

    public function set($data) 
    {
        $this->redis->SET($this->key, $data);
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }

    public function get()
    {
        return $this->redis->GET($this->key);
    }
}
