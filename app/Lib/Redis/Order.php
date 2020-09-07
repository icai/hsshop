<?php
namespace App\Lib\Redis;
use Redisx;
use Log;

class Order extends RedisInterface{

	protected $prefixKey = 'order_';
    protected $timeOut   = 7200;

    public function __construct($key='') 
    {
        parent::__construct($key);
    }


    /**
     * [set 设置缓存]
     * @param [string] $value [设置的值]
     */
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


}





?>