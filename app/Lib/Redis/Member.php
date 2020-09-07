<?php
namespace App\Lib\Redis;
class Member extends RedisInterface
{
    protected $prefixKey = 'member';
    protected $timeOut   = 86400;
    public function __construct($key = "") 
    {
        parent::__construct($key);
    }

    public function increment($id, $field, $num = 1)
    {
        if ( $this->redis->HEXISTS($this->key . $id, $field) ) {
            $value = $this->redis->HGET($this->key . $id, $field);
            $value = get_numeric($value);
            //if ( $value > 0 ) {
                if ( is_int($value)  ) {
                    $num = intval($num);
                    return $this->redis->HINCRBY($this->key . $id, $field, $num);
                } elseif ( is_float($value) ) {
                    return $this->redis->HINCRBYFLOAT($this->key . $id, $field, $num);
                }
            //}
        }
    }

    public function getIdByOpenid($openId)
    {
        return $this->redis->get($this->key . $openId);
    }

    public function setIdByOpenid($openId, $id)
    {
        $this->redis->SET($this->key . $openId, $id);
        $this->redis->EXPIRE($this->key . $openId, $this->timeOut);
    }

    public function updateHashRow($id, $data)
    {
        if ( ! $this->redis->EXISTS($this->key . $id) ) {
            return true;
        }
        return $this->redis->hmset($this->key . $id, $data);
    }

    public function decrement($id,$num)
    {
        if ($this->redis->exists($this->key.$id)) {
            return $this->redis->hincrby($this->key . $id, 'score', $num);
        }
        return true;
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


    /**
     * 批量更新缓存
     * @param $data
     * @return mixed
     * @author: 梅杰 2018年10月18日
     */
    public function batchUpdateHash($data)
    {
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $val) {
                if ($this->redis->EXISTS($this->key . $val['id'])){
                    foreach ($val as $k => $item) {
                        if ($k == 'id') {
                            continue;
                        }
                        $pipe->HMSET($this->key . $val['id'],$k,$item);
                    }
                }
            }
        });
    }

}