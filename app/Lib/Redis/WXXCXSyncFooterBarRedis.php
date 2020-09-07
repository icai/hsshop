<?php
namespace App\Lib\Redis;
use Redisx;

/**
 * @author 吴晓平 
 * @version  2017.07.28 [店铺redis]
 * 功能点：设置单条hash数据
 * 更新关联关系的redis 
 */
class WXXCXSyncFooterBarRedis extends RedisInterface{

	protected $prefixKey = 'wxxcxsyncfooterbar:';
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

    /**
     * redis以字符串类型保存数据
     * @author 吴晓平 <2018年07月19日>
     * @param [string] $key  [redis键]
     * @param [mix] $data [要保存的数据，可以数组或字符串]
     */
    public function setStringData($key,$data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        return $this->redis->set($key,$data);
    }

    /**
     * 获取保存的字符串数据
     * @param  [string] $key [redis键]
     * @return [type]      [description]
     */
    public function getStringData($key)
    {
        return $this->redis->get($key);
    }

    /**
     * 获取保存的字符串数据
     * @param  [string] $key [redis键]
     * @return [type]      [description]
     */
    public function delStringData($key)
    {
        return $this->redis->del($key);
    }


}