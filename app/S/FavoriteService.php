<?php

namespace App\S;
use App\Lib\Redis\FavoriteRedis;

/**
 * 收藏类
 * @author 许立 2018年09月04日
 */
class FavoriteService extends S
{
    public function __construct()
    {
        parent::__construct('Favorite');
    }

    /**
     * 获取带分页列表
     * @param array $where 查询条件
     * @param string $orderBy 排序字段
     * @param string $order 排序顺序
     * @return array
     * @author 许立 2018年09月04日
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 根据id数组获取列表
     * @param array $idArr id数组
     * @return array
     * @author 许立 2018年09月04日
     */
    public function getListById($idArr)
    {
        $redisId = $redisData = $mysqlData = [];
        $idArr = array_values($idArr);
        $redis = new FavoriteRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key])) {
                $redisId[] = $value;
            } else {
                $redisData[$value] = $result[$key];
            }
        }
        if ($redisId) {
            $mysqlData = $this->model->whereIn('id', $redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null, 'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData));
    }

    /**
     * 获取一条收藏记录
     * @param int $mid 用户id
     * @param int $relativeId 关联元素id
     * @param int $type 收藏类型
     * @return object|NULL
     * @author 许立 2018年09月05日
     */
    public function getRow($mid, $relativeId, $type)
    {
        return $this->model
            ->where('relative_id', $relativeId)
            ->where('mid', $mid)
            ->where('type', $type)
            ->first();
    }

    /**
     * 新增一条收藏记录
     * @param array $data 新增数据
     * @return int|false
     * @author 许立 2018年09月05日
     */
    public function insertRow($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 删除一条收藏记录
     * @param int $id 主键
     * @return bool
     * @author 许立 2018年09月05日
     */
    public function deleteRow($id)
    {
        return $this->model
            ->where('id', $id)
            ->delete();
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
            $redis = new FavoriteRedis();
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