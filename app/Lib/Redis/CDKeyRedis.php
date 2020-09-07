<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:41
 */

namespace App\Lib\Redis;


class CDKeyRedis extends RedisInterface
{
    protected $prefixKey = 'CDKey';
    protected $timeOut   = 86400;
    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * @param $cam_id
     * @param $data
     * @return mixed
     * @author: 梅杰 cdkey 队列
     */
    public function push($cam_id,$data)
    {

        $key = $this->key . $cam_id;
        return $this->redis->pipeline(function ($pipe) use ($key,$data) {
            foreach ($data as $v) {
                $pipe->LPUSH($key, $v['id']);
            }
        });
    }

    /**
     * 获取卡密
     * @param $cam_id
     * @param $num
     * @return mixed
     * @author: 梅杰 time
     */
    public function pop($cam_id,$num)
    {
        $key = $this->key . $cam_id;
        return $this->redis->pipeline(function ($pipe) use ($key,$num) {
            for ($i = 0 ;$i < $num ; $i ++) {
                $pipe->LPOP($key);
            }
        });
    }


    /**
     * @param $cam_id
     * @param $num 取出的数量
     * @return bool
     * @author: 梅杰
     */
    public function get($cam_id)
    {
        return $this->redis->LPOP($this->key . $cam_id);
    }


    public function getLen($cam_id)
    {
        return $this->redis->LLEN($this->key . $cam_id);
    }

    public function del($cam_id)
    {
        return $this->redis->del( $this->key . $cam_id);
    }


}