<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */
namespace App\S\Distribute;
use App\Lib\Redis\DistributeApplayLogRedis;
use App\Lib\Redis\DistributeApplayPageRedis;
use App\Lib\Redis\DistributePurgeLogRedis;
use App\S\S;

class DistributePurgeLogService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('DistributePurgeLog');
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
        $redis = new DistributePurgeLogRedis();
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
        $redis = new DistributePurgeLogRedis();
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
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new DistributePurgeLogRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new DistributePurgeLogRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where=[])
    {
        return $this->getListWithPage($where, '', '');
    }

    /**
     * 批量删除模板
     * @param $ids
     * @author 张永辉 2018年9月29日
     */
    public function batchDel($ids)
    {
        $res = $this->model->whereIn('id',$ids)->delete();
        if ($res){
            $storeRedis = new DistributePurgeLogRedis();
            foreach ($ids as $val){
                $storeRedis->del($val);
            }
            return true;
        }else{
            return false;
        }
    }


    /**
     * 获取列表
     * @param array $where
     * @param string $skip
     * @param string $perPage
     * @param string $orderBy
     * @param string $order
     * @return mixed
     * @author 张永辉 2018年9月29日
     */
    public function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return parent::getList($where, $skip, $perPage, $orderBy, $order); // TODO: Change the autogenerated stub
    }

}























