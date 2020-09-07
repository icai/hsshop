<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/4/11
 * Time: 9:49
 */

namespace App\S;

use App\Lib\Redis\JPushRedis;
use App\S\S;

class JPushService extends S
{
    /**
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('JPush');
    }

    /**
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new JPushRedis();
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
        $redis = new JPushRedis();
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
     * @desc 更新数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new JPushRedis();
            return $storeRedis->update($id,$data);
        }
    }

    /**
     * @desc 删除数据
     * @param $id
     * @return bool
     */
    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new JPushRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    /**
     * @desc 添加数据
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * @desc 获取分页数据
     * @return array
     */
    public function getlistPage($where = [], $orderBy = '', $order = '',$pageSize=0 )
    {
        return $this->getListWithPage($where, $orderBy, $order ,$pageSize );
    }

    /**
     * @desc 获取统计数据
     * @param array $where
     * @return mixed
     */
    public function count($where = [])
    {
        return parent::count($where);
    }

}

