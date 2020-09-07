<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */
namespace App\S\Groups;
use App\Lib\Redis\GroupsSkuRedis;
use App\S\S;
use StaffOperLogService;

class GroupsSkuService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('GroupsSku');
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
        $redis = new GroupsSkuRedis();
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
        $redis = new GroupsSkuRedis();
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
            $storeRedis = new GroupsSkuRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new GroupsSkuRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where)
    {
        return $this->getListWithPage($where, '', '');
    }

    public function getlistByRuleId($id)
    {
        return $this->getList(['rule_id'=>$id]);
    }

    public function getlistByRuleIds($ids)
    {
        return $this->getList(['rule_id'=>['in',$ids]]);
    }

    public function getlistByWhere($where)
    {
        return $this->getList($where);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc根据rule id 删除
     * @param $id
     */
    public function delByRuleId($id)
    {
        $skus = $this->getlistByRuleId($id);
        foreach ($skus as $val){
            $this->del($val['id']);
        }
    }


    /**
     * 根据第获取价格最低的
     * @param $id
     * @author 张永辉 2018年7月25日
     */
    public function getMinPriceByRule($id)
    {
            $res = $this->model->where('rule_id',$id)->select(['id','price'])->orderBy('price','asc')->first();
            if ($res){
                return $res->price;
            }else{
                return 0;
            }
    }



}























