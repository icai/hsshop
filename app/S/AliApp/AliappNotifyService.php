<?php

namespace App\S\AliApp;
use App\Lib\Redis\AliappNotifyRedis;
use App\S\S;

/**
 * Created by 何书哲.
 * User: 何书哲
 * Date: 2018/7/30
 * Time: 11:34
 */

class AliappNotifyService extends S
{


    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('AliappNotify');
    }


    /**
     * 根据id获取支付回调信息
     * @param $id 主键id
     * @return array
     * @author 何书哲 2018年7月30日
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new AliappNotifyRedis();
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
        $redis = new AliappNotifyRedis();
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
     * 根绝id更新支付回调信息
     * @param $id 主键id
     * @param $data 更新数组
     * @return mixed
     * @author 何书哲 2018年7月30日
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new AliappNotifyRedis();
            return $storeRedis->update($id,$data);
        }
    }

    /**
     * 根据主键删除支付回调信息
     * @param $id 主键id
     * @return bool
     * @author 何书哲 2018年7月30日
     */
    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new AliappNotifyRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    /**
     * 添加支付回调信息
     * @param $data 支付回调信息
     * @return mixed
     * @author 何书哲 2018年7月30日
     */
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage()
    {
        return $this->getListWithPage([], '', '');
    }

    public function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return parent::getList($where, $skip, $perPage, $orderBy, $order);
    }

}