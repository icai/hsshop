<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/3
 * Time: 8:21
 */

namespace App\S\Market;


use App\Lib\Redis\EggPrizeRedis;
use App\S\S;
use Illuminate\Support\Facades\DB;

class EggPrizeService extends S
{

    /**
     * EggPrizeService constructor.
     */
    public function __construct()
    {
        parent::__construct('EggPrize');
    }

    /**
     * 批量插入奖品信息
     * @param array $data
     * @param $eggId
     * @return array|bool
     * @author: 梅杰 time
     */
    public function create($data = [],$eggId)
    {
        foreach ($data as &$v) {
            $v['eggId'] = $eggId;
        }
        return DB::table($this->model->getTable())->insert($data);
    }


    public function getListByWhere($where)
    {
        return $this->getList($where);
    }

    //更改指定奖品的剩余库存
    public function updateStock($id)
    {
        $re = $this->model->where(['id'=>$id])->decrement('left');
        if($re === false) {
            return false;
        }
        //更新redis
        $data['id'] = $id;
        $data['update_at'] = date('Y-m-d H:i:s');
        $redis = new EggPrizeRedis();
        $redis->incr($id,'left',-1);
        return true;
    }


    //根据主键获取信息
    public function getInfoById($id)
    {
        $redis = new EggPrizeRedis();
        $row = $redis->getRow($id);
        if (empty($row)) {
            //redis不存在 取数据库
            $row = $this->model->where('id', $id)->first();
            if (empty($row)) {
                return [];
            } else {
                $row = $row->toArray();
            }
            //保存redis
            $redis->add($row);
        }
        return $row;
    }
    /**
     * 根据主键id数组获取列表
     * @param array $idArr
     * @return mixed
     */
    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new EggPrizeRedis();
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

    //修改奖品时用
    //1
    public function update($prizeData)
    {
        if ($this->model->updateBatch($prizeData) === false) {
            return false;
        }
        (new EggPrizeRedis())->updateArr($prizeData);
        return true;

    }

    public function del($eggId)
    {
        $prize = $this->getList(['eggId'=>$eggId]);
        foreach ($prize as $v ) {
            $ids[] = $v['id'];
        }
        $re = $this->model->where(['eggId'=>$eggId,'status'=> 0 ])->update(['status'=>1]);
        if(!$re)
            return false;
        //删除Redis
        $redis = new EggPrizeRedis();
        return $redis->deleteArr($ids);
    }

    /**
     * @desc 删除奖品
     * @param $eggId 删除id
     * @param $otherIds 不删除的id
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 05 月 20 日
     */
    public function delPrize($eggId, $otherIds)
    {
        $this->model->where('eggId', $eggId)->whereNotIn('id', $otherIds)->delete();
    }


}