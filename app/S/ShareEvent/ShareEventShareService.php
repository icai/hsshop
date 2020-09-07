<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/5/15
 * Time: 14:43
 */

namespace App\S\ShareEvent;

use App\Lib\Redis\ShareEventShareRedis;
use App\S\S;

class ShareEventShareService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('ShareEventShare');
    }

    /**
     * @author hsz
     * @desc 根据id获取记录
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new ShareEventShareRedis();
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

    /**
     * 排序获取分享记录
     * @param array $where 条件
     * @param string $order 排序字段
     * @param string $orderBy 排序方式
     * @return array
     * @author 何书哲 2018年8月9日
     */
    public function getRowOrderByWhere($where = [], $order='id', $orderBy='DESC')
    {
        $result = $this->model->where($where)->orderBy($order, $orderBy)->first();
        if (!$result) {
            return $result = [];
        }
        $result = $result->toArray();
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ShareEventShareService();
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
     * @author hsz
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new ShareEventShareRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new ShareEventShareService();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where, $orderBy = '', $order = '', $pagesize=20)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pagesize);
    }


    /**
     * 批量更新
     * @param $ids
     * @param $data
     * @return bool
     * @author 张永辉
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redis = new ShareEventShareService();
            $redisUpData = [];
            foreach ($ids as $val){
                $redisUpData[] = array_merge($data,['id'=>$val]);
            }
            return $redis->updateArr($redisUpData);
        }else{
            return false;
        }
    }

}