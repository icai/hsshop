<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/24
 * Time: 18:09
 */

namespace App\Lib\Redis;


class AuthorizationRedis extends RedisInterface
{
    protected $prefixKey = 'authorization';
    protected $timeOut   = 172800;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }

    public function set($value)
    {
        $this->redis->SET($this->key, $value);
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }

    public function get()
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }

        $value = $this->redis->GET($this->key);

        return $value;
    }

    public function exists()
    {
        if($this->redis->EXISTS($this->key))
        {
            return true;
        }
        return false;
    }
}