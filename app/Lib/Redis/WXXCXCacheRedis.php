<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/10
 * Time: 18:00
 */

namespace App\Lib\Redis;


class WXXCXCacheRedis extends RedisInterface
{
    protected $prefixKey = 'xcx_cache';
    protected $timeOut   = 7200;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * todo 设置小程序中缓存信息[此方法不要乱改更改前请通知张国军]
     * @param $key
     * @param $value
     */
    public function set($key,$value,$prefix='',$expireTime='')
    {
        if(!empty($prefix))
        {
            $key=$prefix.':'.$key;
        }
        $this->redis->SET($this->key.$key,$value);
        //缓存过期时间
        $time=$this->timeOut;
        if(!empty($expireTime))
        {
            $time=$expireTime;
        }
        $this->redis->EXPIRE($this->key.$key,$time);
    }

    /**
     * todo 获取小程序中缓存信息[此方法不要乱改更改前请通知张国军]
     * @param $key
     * @return bool
     */
    public function get($key,$prefix='',$delay=true)
    {
        if(!empty($prefix))
        {
            $key=$prefix.':'.$key;
        }
        if(!$this->redis->EXISTS($this->key.$key))
        {
            return false;
        }
        //有操作,则延迟缓存的过期时间
        if($delay)
        {
            $this->redis->EXPIRE($this->key . $key, $this->timeOut);
        }
        return $this->redis->GET($this->key.$key);
    }

    /**
     * todo 删除redis
     * @param $key
     * @param string $prefix
     * @return bool
     * @author jonzhang
     * @date 2018-05-03
     */
    public function delete($key,$prefix='')
    {
        if(!empty($prefix))
        {
            $key=$prefix.':'.$key;
        }
        if(!$this->redis->EXISTS($this->key.$key))
        {
            return true;
        }
        return $this->redis->DEL($this->key.$key);
    }
}