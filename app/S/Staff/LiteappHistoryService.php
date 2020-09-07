<?php

namespace App\S\Staff;

use App\Lib\Redis\Liteapp;
use App\Lib\Redis\LiteappHistory;
use App\S\S;

class LiteappHistoryService extends S
{
    public function __construct()
    {
        parent::__construct('LiteappHistory');
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
        $redis = new LiteappHistory();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id', $redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null, 'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData));
    }

    /**
     * 新增案例
     */
    public function add($phoneArr, $titleArr)
    {
        foreach ($phoneArr as $k => $v) {
            if (!empty($v) && !empty($titleArr[$k])) {
                $this->model->insertGetId(['phone' => $v, 'title' => $titleArr[$k]]);
            }
        }
    }
}