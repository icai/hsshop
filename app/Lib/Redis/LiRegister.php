<?php
namespace App\Lib\Redis;

/**
 * 注册信息redis
 */
class LiRegister extends RedisInterface
{
    protected $prefixKey = 'li_register';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180227
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