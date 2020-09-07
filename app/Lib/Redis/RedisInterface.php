<?php
namespace App\Lib\Redis;
use Redisx;

abstract class RedisInterface {
    protected $redis = null;
    protected $key = '';
    public function __construct($key = '', $contect = '') {
        $this->key = $key ? $this->prefixKey . ':' . $key : $this->prefixKey . ':';
        $this->redis = Redisx::connection($contect);    
    }

    //批量取hash格式的数据
    public function getArr(array $idArr)
    {   
        return $this->redis->pipeline(function ($pipe) use ($idArr) {
            foreach ($idArr as $id) {
                $pipe->HGETALL($this->key . $id);
            }
        });
    }

    //批量设置hash格式的数据
    public function setArr(array $data)
    {
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $d) {
                $pipe->HMSET($this->key . $d['id'], $d);
                $pipe->EXPIRE($this->key . $d['id'], $this->timeOut);
            }
        });
    }

    //批量修改hash格式数据
    public function updateArr(array $data)
    {
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $d) {
                if ($this->redis->EXISTS($this->key . $d['id'])) {
                    $pipe->HMSET($this->key . $d['id'], $d);
                    $pipe->EXPIRE($this->key . $d['id'], $this->timeOut);
                }
            }
        });
    }

    /**
     * 获取一条hash详情
     */
    public function getRow($id)
    {
        return $this->redis->HGETALL($this->key . $id);
    }

    /**
     * 添加
     */
    public function add($data)
    {
        $key = $this->key . $data['id'];
        $this->redis->HMSET($key, $data);
        $this->redis->EXPIRE($key, $this->timeOut);
    }

    /**
     * 修改
     */
    public function updateRow($data)
    {
        if (empty($data['id'])) {
            return false;
        }
        $this->redis->pipeline(function ($pipe) use ($data) {
            $key = $this->key . $data['id'];
            if ($this->redis->EXISTS($key)) {
                //Herry redis更新修改时间字段
                $data['updated_at'] = date('Y-m-d H:i:s');
                foreach ($data as $k => $v) {
                    $pipe->HSET($key, $k, $v);
                }
            }
        });
    }

    /**
     * 删除一条
     */
    public function delete($id)
    {
        if ($this->redis->EXISTS($this->key . $id)) {
           return $this->redis->DEL($this->key . $id);
        }
    }

    /**
     * 批量删除
     */
    public function deleteArr(array $idArr)
    {
        return $this->redis->pipeline(function ($pipe) use ($idArr) {
            foreach ($idArr as $id) {
                $pipe->DEL($this->key . $id);
            }
        });
    }

    /**
     * 增加(或减少)
     * $id int 主键ID
     * $field string 字段名
     * $num int 增量(负数则是减少量)
     */
    public function incr($id, $field, $num)
    {
        if ($this->redis->EXISTS($this->key . $id)) {
            $this->redis->HINCRBY($this->key . $id, $field, $num);
        }
    }

    //添加一条hash数据
    public function addArr($data)
    {
        $this->redis->HMSET($this->key  . $data['id'], $data);
        $this->redis->EXPIRE($this->key . $data['id'], $this->timeOut);
        return true;
    }
}