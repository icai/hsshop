<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */

namespace App\S\Wheel;

use App\Lib\Redis\ActivityWheelPrizeRedis;
use App\Lib\Redis\ActivityWheelRedis;
use App\Model\ActivityWheelPrize;
use App\S\S;

class ActivityWheelPrizeService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('ActivityWheelPrize');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new ActivityWheelPrizeRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->addArr($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ActivityWheelPrizeRedis();
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
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        $res = $this->model->where('id', $id)->update($data);
        if ($res) {
            $storeRedis = new ActivityWheelPrizeRedis();
            return $storeRedis->update($id, $data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id', $id)->delete();
        if ($res) {
            $storeRedis = new ActivityWheelPrizeRedis();
            return $storeRedis->del($id);
        } else {
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where, $orderBy = '', $order = '')
    {
        return $this->getListWithPage($where, $orderBy, $order);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 批量更新
     * @update 何书哲 2019年07月17日 修改redis批量更新
     */
    public function batchUpdate($ids, $data)
    {
        $res = $this->model->whereIn('id', $ids)->update($data);
        if ($res) {
            $redis = new ActivityWheelPrizeRedis();
            // update 何书哲 2019年07月17日 修改redis批量更新
            $redisUpData = [];
            foreach ($ids as $val) {
                $redisUpData[] = array_merge($data, ['id' => $val]);
            }
            return $redis->updateArr($redisUpData);
        } else {
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170801
     * @desc 根据规则id获取奖品信息
     * @param $id
     */
    public function getByWheelId($id)
    {
        $where = ['wheel_id' => $id];
        return $this->getList($where, '', '', 'grade', 'asc');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170803
     * @desc 减少奖品
     * @param $id
     */
    public function reduce($id, $num)
    {
        $where = [
            'id' => $id,
            'num' => ['>=', $num],
        ];
        $res = $this->model->wheres($where)->decrement('num', $num);
        if ($res) {
            $redis = new ActivityWheelPrizeRedis();
            $redis->decrement($id, $num);
            return true;
        } else {
            return false;
        }


    }

}























