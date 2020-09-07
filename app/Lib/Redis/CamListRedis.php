<?php
namespace App\Lib\Redis;
use Redisx;

/**
 * @author 吴晓平 
 * @version  2017.07.28 [店铺redis]
 * 功能点：设置单条hash数据
 * 更新关联关系的redis 
 */
class CamListRedis extends RedisInterface{

	protected $prefixKey = 'cam_list:';
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
    	if ( $this->redis->EXISTS($this->key . $id) ) {
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
     * 发送卡密商品时
     * @param array $ids
     * @param $mid 用户id
     * @return mixed
     * @author: 梅杰 2018年8月7日
     */
    public function sendCdKey($ids = [],$data)
    {
        return $this->redis->pipeline(function ($pipe) use ($ids,$data) {
           foreach ($ids as $id) {
               if ($this->redis->EXISTS($this->key.$id)) {
                   $pipe->hset($this->key.$id,'is_send',1);
                   $pipe->hset($this->key.$id,'mid',$data['mid']);
                   $pipe->hset($this->key.$id,'oid',$data['oid']);
                   $pipe->hset($this->key.$id,'getMember', $data['getMember']);
                   $pipe->hset($this->key.$id,'send_time', date('Y-m-d H:i:s'));
               }
           }
        });
    }

    /**
     * 批量删除
     * @author 吴晓平 <2018年08月08日>
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public function batchDel($ids)
    {
        return $this->redis->pipeline(function ($pipe) use ($ids) {
           foreach ($ids as $id) {
               if ($this->redis->EXISTS($this->key.$id)) {
                   $pipe->del($this->key.$id);
               }
           }
        });
    }



}