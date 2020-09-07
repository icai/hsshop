<?php

namespace App\Lib\Redis;

/**
 * 订单打单Redis类
 * create 何书哲 2018年6月26日
 */
class OrderLogisticsRedis extends RedisInterface {
    protected $prefixKey = 'order_logistics';
    protected $timeOut   = 86400;

    /**
     * 构造函数
     * @param $key Redis键值
     * @return Redis实例
     * @create 何书哲 2018年6月26日
     */
    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * 更新Redis缓存
     * @param $id 主键id
     * @param $data 更新内容数组
     * @return bool
     * @create 何书哲 2018年6月26日
     */
    public function update($id, $data)
    {
        $id = trim($id);
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->hmset($this->key.$id, $data);
        }
        return true;
    }

}