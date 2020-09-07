<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/5/15
 * Time: 15:05
 */

namespace App\S\Scratch;

use App\Lib\Redis\ActivityScratchPrizeRedis;
use App\Lib\Redis\ActivityScratchRedis;
use App\S\S;

class ActivityScratchPrizeService extends S
{
    /**
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('ActivityScratchPrize');
    }

    /**
     * @author hsz
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new ActivityScratchPrizeRedis();
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
        $redis = new ActivityScratchPrizeRedis();
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
     * @author hsz
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        $res = $this->model->where('id', $id)->update($data);
        if ($res) {
            $storeRedis = new ActivityScratchPrizeRedis();
            return $storeRedis->update($id, $data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id', $id)->delete();
        if ($res) {
            $storeRedis = new ActivityScratchPrizeRedis();
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
     * 批量更新
     * @author 何书哲 2017年07月20日
     * @update 何书哲 2019年07月17日 修改redis批量更新
     */
    public function batchUpdate($ids, $data)
    {
        $res = $this->model->whereIn('id', $ids)->update($data);
        if ($res) {
            $redis = new ActivityScratchPrizeRedis();
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
     * @author hsz
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
            $redis = new ActivityScratchPrizeRedis();
            $redis->decrement($id, $num);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @author hsz
     * @desc 根据规则id获取奖品信息
     * @param $id
     */
    public function getByScratchId($id)
    {
        $where = ['scratch_id' => $id];
        return $this->getList($where, '', '', 'grade', 'asc');
    }


}