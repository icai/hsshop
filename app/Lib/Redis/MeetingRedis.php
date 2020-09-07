<?php
namespace App\Lib\Redis;
use Redisx;

/**
 * @author 陈文豪
 * @version  
 * 功能点：防止重复提交
 */
class MeetingRedis extends RedisInterface{

	protected $prefixKey = 'meeting:';
	protected $timeOut   = 7200;

    public function __construct($key='') 
    {
        parent::__construct($key);
    }

    /**
     * 设置单条hash记录
     * @param [integer] $id   [店铺id主键]
     * @param [array]   $data [要设置的数组]
     */
    public function set($id) 
    {
        $this->redis->SET($this->key . $id, 1);
        $this->redis->EXPIRE($this->key . $id, 5);
        return true;
    }

    public function get($id)
    {
        if ($this->redis->exists($this->key.$id)){
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