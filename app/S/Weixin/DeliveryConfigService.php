<?php

namespace App\S\Weixin;


use App\Lib\Redis\DeliveryConfigRedis;
use App\S\S;

/**
 * 外卖订单配置service
 * Class DeliveryConfigService
 * @package App\S\Weixin
 * @author 何书哲 2018年11月14日
 */

class DeliveryConfigService extends S
{
    protected $redis;

    /**
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        $this->redis = new DeliveryConfigRedis();
        parent::__construct('DeliveryConfig');
    }


    public function getRowById($id)
    {
        $result = [];
        $result = $this->redis->getRow($id);
        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $this->redis->addArr($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $result = $this->redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id', $redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    public function update($id,$data){
        $res = $this->model->where('id', $id)->update($data);
        if ($res) {
            return $this->redis->update($id, $data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id', $id)->delete();
        if ($res) {
            return $this->redis->del($id);
        } else {
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getRowByWhere ($where=[]) {
        $result = $this->model->where($where)->first();
        if (!$result) {
            return [];
        }
        return $result->toArray();
    }


}