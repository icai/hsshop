<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/14
 * Time: 9:23
 */

namespace App\Lib\Redis;


class WXXCXMicroPageRedis extends RedisInterface
{
    protected $prefixKey = 'wxxcx_micro_page';
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
     * @date 2017-09-14
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
     * @date 2017-09-14
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
     * @date 2017-09-14
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