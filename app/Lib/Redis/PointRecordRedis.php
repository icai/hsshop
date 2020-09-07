<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/13
 * Time: 14:04
 */

namespace App\Lib\Redis;


class PointRecordRedis extends RedisInterface
{
    protected $prefixKey = 'point_record';
    //过期时间默认为秒 过期时间为24小时
    protected $timeOut   = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20170925
     * @desc 批量更新
     * @param $ids
     * @param $data
     * @return mixed
     */
    public function batchUpdate($ids,$data)
    {
        $data = [$ids,$data];
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data[0] as $val) {
                if ($this->redis->EXISTS($this->key . $val)){
                    $pipe->HMSET($this->key . $val, $data[1]);
                    $pipe->EXPIRE($this->key . $val, $this->timeOut);
                }
            }
        });
    }

}