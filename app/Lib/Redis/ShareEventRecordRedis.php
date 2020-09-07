<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/12/6
 * Time: 23:35
 */

namespace App\Lib\Redis;


class ShareEventRecordRedis extends RedisInterface
{
    protected $prefixKey = 'ShareEventRecord';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }

    public function batchUpdate($data)
    {
        $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $item) {
                if ($this->redis->EXISTS($this->key . $item['id'])){
                    $pipe->HMSET($this->key . $item['id']  , $item);
                    $pipe->EXPIRE($this->key . $item['id'], $this->timeOut);
                }
            }
        });
    }


}