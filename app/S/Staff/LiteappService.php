<?php

namespace App\S\Staff;

use App\Lib\Redis\Liteapp;
use App\S\S;

class LiteappService extends S
{
    public function __construct()
    {
        parent::__construct('Liteapp');
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 获取非分页列表
     * @return array
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 根据主键id数组获取列表
     * @param array $idArr
     * @return mixed
     */
    public function getListById(array $idArr)
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new Liteapp();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 查找小程序是否存在
     */
    public function checkExistence($title)
    {
        $row = $this->model
            ->select('id')
            ->where('title', $title)
            ->first();

        return empty($row) ? false : true;
    }

    /**
     * 获取所有列表
     */
    public function getAll()
    {
        return $this->model->orderBy('id','desc')->get()->toArray();
    }

    /**
     * 添加
     */
    public function add($titleArr)
    {
        foreach ($titleArr as $v) {
            $this->model->insertGetId(['title' => $v]);
        }
    }

    /**
     * 删除
     */
    public function delete($ids)
    {
        $redis = new Liteapp();
        foreach ($ids as $id) {
            $this->model->where('id', $id)->delete();
            $redis->delete($id);
        }
    }
}