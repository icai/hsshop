<?php

namespace App\S\Order;

use App\S\S;
use App\Lib\Redis\OrderLogisticsRedis;

/**
 * 订单打单service类
 * create 何书哲 2018年6月26日
 */
class OrderLogisticsService extends S {
    /**
     * 构造函数
     * @create 何书哲 2018年6月26日
     */
    public function __construct() {
        parent::__construct('OrderLogistics');
    }

    /**
     * 根据id获取列表
     * @param $id 主键id
     * @return array
     * @create 何书哲 2018年6月28日 根据id获取列表
     */
    public function getRowById($id) {
        $result = [];
        $orderLogRedis = new OrderLogisticsRedis();
        $result = $orderLogRedis->getRow($id);
        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $orderLogRedis->addArr($result);
        }
        return $result;
    }

    /**
     * 根据条件获取
     * @param $where 条件数组
     * @return array 结果数组
     * @create 何书哲 2018年6月28日 根据条件获取
     */
    public function getRowByWhere($where)
    {
        $res = $this->model->wheres($where)->first();
        if (empty($res)) {
            return [];
        }
        return $res->toArray();
    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * @param array $idArr 主键id数组
     * @return array 记录数组
     * @create 何书哲 2018年6月28日
     */
    public function getListById($idArr = []) {
        $redisData = $mysqlData = [];
        $redisId = [];
        $orderLogRedis = new OrderLogisticsRedis();
        $result = $orderLogRedis->getArr($idArr);
        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $val) {
            if (empty($result[$key]))
                $redisId[] = $val;
            else
                $redisData[$val] = $result[$key];
        }
        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $orderLogRedis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 添加记录
     * @param array $data 添加数组
     * @return int 主键id
     * @create 何书哲 2018年6月26日 添加记录
     */
    public function add($data) {
        return $this->model->insertGetId($data);
    }

    /**
     * 更新记录及Redis缓存
     * @param int $id 主键id
     * @param array $data 更新数组
     * @return bool 更新是否成功
     * @create 何书哲 2018年6月26日 更新记录及Redis缓存
     */
    public function update($id, $data) {
        $res = $this->model->where('id', $id)->update($data);
        if ($res) {
            $orderLogRedis = new OrderLogisticsRedis();
            return $orderLogRedis->update($id, $data);
        }
    }

    /**
     * 获取打单列表
     * @param array $where 条件判断数组
     * @param string $orderBy 排序字段
     * @param string $order 排序方式
     * @param bool $is_page 是否分页
     * @param int $pageSize 每页条数
     * @return array 列表数组
     * @create 何书哲 2018年6月26日 获取打单列表
     */
    public function getAllList($where=[], $orderBy='id', $order='DESC', $is_page=true, $pageSize=15) {
        if ($is_page) {
            $list = $this->getListWithPage($where, $orderBy, $order, $pageSize);
        }else {
            $list = $this->getList($where);
        }
        return $list;
    }

}