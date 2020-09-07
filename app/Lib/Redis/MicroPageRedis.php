<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/3
 * Time: 13:59
 */

namespace App\Lib\Redis;


class MicroPageRedis  extends RedisInterface
{
    protected $prefixKey = 'micro_page';
    //过期时间默认为秒 过期时间为24小时
    protected $timeOut   = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * todo 获取缓存中的公告信息
     * @return bool|mixed
     * @author jonzhang
     * @date 2017-07-03
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
     * @date 2017-07-03
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
     * @date 2017-07-03
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

    /**
     * todo 通过key删除hash类型的缓存 [此方法用于定位redis问题，仅仅用于开发调试，个人使用]
     * @param $key
     * @return bool
     * @author jonzhang
     * @date 2017-07-05
     */
    public function deleteByKey($key)
    {
        return $this->redis->DEL($key);
    }

    /**
     * todo 获取Hash类型 某个key的对应的数值 [此方法用于定位redis问题，仅仅用于开发调试，个人使用]
     * @param $key
     * @return bool
     * @author jonzhang
     * @author 2017-07-05
     */
    public function getValueByKey($key)
    {
        if(!$this->redis->EXISTS($key))
        {
            return false;
        }
        return $this->redis->HGETALL($key);
    }

    /**
     * todo 获取所有的Hash类型 key [此方法用于定位redis问题，仅仅用于开发调试，个人使用]
     * @param $key
     * @return mixed
     * @author jonzhang
     * @date 2017-07-05
     */
    public function getALLKeys($key)
    {
         return $this->redis->KEYS('*'.$key.'*');
    }
}