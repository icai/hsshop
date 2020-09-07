<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/23
 * Time: 16:30
 */

namespace App\Lib\Redis;


class MemberHomeRedis extends RedisInterface
{
    protected $prefixKey = 'member_home';
    protected $timeOut   = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * todo 获取缓存中的会员信息
     * @return bool|mixed
     * @author jonzhang
     * @date 2017-06-26
     */
    public function getOne($id)
    {
        return $this->redis->HGETALL($this->key.$id);
    }

    /**
     * todo 更改缓存中的数据
     * @param $id
     * @param $data
     * @return bool
     * @author jonzhang
     * @date 2017-06-26
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
     * @date 2017-06-26
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