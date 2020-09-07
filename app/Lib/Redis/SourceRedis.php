<?php 
namespace App\Lib\Redis;


class SourceRedis extends RedisInterface{

	protected $prefixKey = 'source_';
    protected $timeOut   = 7200;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * [set 设置缓存]
     * @param [string] $value [设置的值]
     */
    public function set($key = '' ,$value) 
    {
        if($key){
            $this->key .= $key;
        }
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

    public function incrNum()
    {
        return $this->redis->INCR($this->key);
    }

}
?>