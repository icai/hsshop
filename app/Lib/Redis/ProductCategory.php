<?php
namespace App\Lib\Redis;

class ProductCategory extends RedisInterface
{
    protected $prefixKey = 'product_category';
    protected $timeOut   = 86400;

    public function __construct($key = "") 
    {
        parent::__construct($key);
    }

    public function set($data) 
    {
        $data = json_encode($data);
        $this->redis->SET($this->key, $data);
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }

    public function addOne($data)
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }
        $redisData = $this->get();
        $redisData[] = $data;
        $this->set($redisData);
        return true;
    }

    public function updateOne($id, $data)
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }
        $redisData = $this->get();
        foreach ($redisData as $key => &$value) {
            if($value['id'] == $id){
                $value = $data;
                break;    
            } 
        }
        $this->set($redisData);
        return true;
    }

    public function get()
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }
        $data = $this->redis->GET($this->key);
        return json_decode($data, true);
    }

    public function deleteOne($id)
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }
        $redisData = $this->get();
        foreach ($redisData as $key => &$value) {
            if($value['id'] == $id){
                unset($redisData[$key]);
                break;    
            } 
        }
        $this->set($redisData);
        return true;        
    }
}
