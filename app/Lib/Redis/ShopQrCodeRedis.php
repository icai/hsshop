<?php
namespace App\Lib\Redis;
use Log;
use Redisx;


class ShopQrCodeRedis extends RedisInterface
{

    protected $prefixKey = 'shopQrCode';
    protected $timeOut   = 900;

    public function __construct($key='') 
    {
        parent::__construct($key);
    }


    public function setRow($id,$data)
    {
        if ( $this->redis->EXISTS($this->key . $id) ) {
            return true;
        }
        $this->redis->hmset($this->key . $id, $data);
        $this->redis->EXPIRE($this->key . $id, $this->timeOut);
        return true;
    }


    public function getRow($id)
    {
        return $this->redis->HGETALL($this->key . $id);
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

    public function exists()
    {
        if($this->redis->EXISTS($this->key))
        {
            return true;
        }
        return false;
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->del($this->key.$id);
        }
        return true;
    }



}
?>
