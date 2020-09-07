<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/10
 * Time: 13:55
 */

namespace App\Lib\Redis;


class WXXCXUserRedis extends RedisInterface
{
    protected $prefixKey = 'wxxcx_user';
    //过期时间默认为秒 过期时间为24小时
    protected $timeOut   = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * todo 更改缓存中的数据
     * @param $id
     * @param $data
     * @return bool
     * @author jonzhang
     * @date 2017-08-10
     */
    public function updateRedis($id, $data)
    {
        if(!$this->redis->EXISTS($this->key.$id))
        {
            return true;
        }
        return $this->redis->HMSET($this->key.$id,$data);
    }

    /**
     * todo 通过key来删除
     * @param $id
     * @return bool
     * @author jonzhang
     * @date 2017-08-10
     */
    public function deleteRedis($id)
    {
        if(!$this->redis->EXISTS($this->key.$id))
        {
            return true;
        }
        return $this->redis->DEL($this->key.$id);
    }
}