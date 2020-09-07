<?php
namespace App\Lib\Redis;
use Redisx;

class ProductEvaluate
{
    protected $prefixKey = 'product_evaluate';
    protected $timeOut   = 86400;
    protected $redis;

    public function __construct($key = "") 
    {
        if ($key != "") {
            $this->prefixKey = $this->prefixKey.$key;
        }
        $this->redis = Redisx::connection();
    }

    public function getArr(array $idArr)
    {   
        return $this->redis->pipeline(function ($pipe) use ($idArr) {
            foreach ($idArr as $id) {
                $pipe->HGETALL($this->prefixKey . $id);
            }
        });
    }

    public function setArr(array $data)
    {
        return $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $evaluate) {
                $pipe->HMSET($this->prefixKey . $evaluate['id'], $evaluate);
                $pipe->EXPIRE($this->prefixKey . $evaluate['id'], $this->timeOut);
            }
        });
    }

    public function add($data)
    {
        $this->redis->HMSET($this->prefixKey . $data['id'], $data);
        $this->redis->EXPIRE($this->prefixKey . $data['id'], $this->timeOut);
        return true;
    }

    public function getPEIdByOdid(array $odidArr)
    {
        return $this->redis->pipeline(function ($pipe) use ($odidArr) {
            foreach ($odidArr as $id) {
                $pipe->GET($this->prefixKey . "_odid_" . $id);
            }
        });
    }

    public function setPEIdByOdid(array $odidArr)
    {
        return $this->redis->pipeline(function ($pipe) use ($odidArr) {
            foreach ($odidArr as $odid => $id) {
                $pipe->SET($this->prefixKey . "_odid_" . $odid, $id);
                $pipe->EXPIRE($this->prefixKey . "_odid_" . $odid, $this->timeOut);
            }
        }); 
    }
}
