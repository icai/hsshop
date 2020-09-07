<?php
namespace App\Lib\Redis;
use Redisx;

/**
 * @author 吴晓平 
 * @version  2018.05.21 [店铺redis]
 * 功能点：设置单条hash数据
 * 更新关联关系的redis 
 */
class ReceptionRedis extends RedisInterface{

	protected $prefixKey = 'reception:';
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
    public function setRow($id,$data) 
    {
    	if ( ! $this->redis->EXISTS($this->key . $id) ) {
            return true;
        }
        return $this->redis->hmset($this->key . $id, $data);
    }

    /**
     * [更新hash类型redis]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateHashRow($id, $data)
    {
        if ( ! $this->redis->EXISTS($this->key . $id) ) {
            return true;
        }
        return $this->redis->hmset($this->key . $id, $data);
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->del($this->key.$id);
        }
        return true;
    }


}