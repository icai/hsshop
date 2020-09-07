<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/5/17
 * Time: 9:37
 */

namespace App\Lib\Redis;


class StoreTopNavRedis extends RedisInterface
{
    protected $prefixKey = 'store_top_nav';
    //过期时间默认为秒 过期时间为24小时
    protected $timeOut   = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * todo 获取缓存中的导航信息
     * @return bool|mixed
     * @author jonzhang
     * @date 2018-05-17
     */
    public function getOne($id)
    {
        //HGETALL 没有值时 返回空
        return $this->redis->HGETALL($this->key.$id);
    }


    /**
     * todo 更改缓存中的数据
     * @param $id
     * @param $data
     * @return bool
     * @author jonzhang
     * @date 2018-05-17
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
     * @date 2018-05-17
     */
    public function deleteRedis($id)
    {
        if(!$this->redis->EXISTS($this->key.$id))
        {
            return true;
        }
        //此处使用HDEL失败，还需要定位原因
        return $this->redis->DEL($this->key.$id);
    }
}