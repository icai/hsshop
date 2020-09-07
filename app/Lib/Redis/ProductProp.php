<?php
namespace App\Lib\Redis;
use Redisx;

/**
 * 商品规格
 */
class ProductProp
{
    protected $prefixKey = 'product_prop';
    protected $timeOut = 86400;
    protected $redis;

    public function __construct($key = "")
    {
        if ($key != "") {
            $this->prefixKey = $this->prefixKey . $key;
        }
        $this->redis = Redisx::connection();
    }

    /**
     * 获取一个商品规格
     */
    public function get($id)
    {
        return $this->redis->HGETALL($this->prefixKey . ':' . $id);
    }

    /**
     * 保存一个商品规格
     */
    public function set($data)
    {
        return $this->redis->HMSET($this->prefixKey . ':' . $data['id'], $data);
    }

    /**
     * 添加商品规格
     */
    public function add($data)
    {
        $key = $this->prefixKey . ':' . $data['id'];
        $this->redis->HMSET($key, $data);
        $this->redis->EXPIRE($key, $this->timeOut);
    }

    /**
     * 修改
     */
    public function update($data)
    {
        $this->redis->pipeline(function ($pipe) use ($data) {
            $key = $this->prefixKey . ':' . $data['id'];
            foreach ($data as $k => $v) {
                $pipe->HSET($key, $k, $v);
            }
        });
    }

    /**
     * 获取商品规格列表
     */
    public function getList(array $idArr)
    {
        return $this->redis->pipeline(function ($pipe) use ($idArr) {
            foreach ($idArr as $id) {
                $pipe->HGETALL($this->prefixKey . ':' . $id);
            }
        });
    }

    /**
     * 设置商品规格列表
     */
    public function setList(array $data)
    {
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $v) {
                $key = $this->prefixKey . ':' . $v['id'];
                $pipe->HMSET($key, $v);
                $pipe->EXPIRE($key, $this->timeOut);
            }
        });
    }

    /**
     * 删除规格列表
     */
    public function delete(array $idArr)
    {
        return $this->redis->pipeline(function ($pipe) use ($idArr) {
            foreach ($idArr as $id) {
                $pipe->DEL($this->prefixKey . ':' . $id);
            }
        });
    }
}