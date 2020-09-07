<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/6/14
 * Time: 15:51
 */

namespace App\S\WXXCX;
use App\Lib\Redis\WXXCXCustomFooterBarRedis;
use App\S\S;

class WXXCXCustomFooterBarService extends S{

    public function __construct()
    {
        parent::__construct('WXXCXCustomFooterBar');
        $this->redis = new WXXCXCustomFooterBarRedis();
    }

    /**
     * 获取单条记录
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        if(empty($id)){
            error('数据异常');
        }
        $data = [];
        $data = $this->redis->getRow($id);
        if(empty($data)){
            $obj = $this->model->wheres(['id' => $id])->first();
            if ($obj) {
                $data = $obj->toArray();
                $this->redis->setRow($id,$data);
            }
        }
        return $data;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = $this->redis;
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
     * 获取自定义底部导航
     * @param array $where
     * @param string $orderBy
     * @param bool $is_page
     * @param int $pageSize
     * @return array
     */
    public function getAllList($where=[],$orderBy='',$is_page=true,$pageSize=15)
    {
        $order = $orderBy ?? 'created_at';
        if ($is_page) {
            $list = $this->getListWithPage($where,$order,'ASC',$pageSize);
        }else{
            $list = $this->getList($where);
        }
        return $list;
    }

    /**
     * 添加数据
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 删除数据
     * @param $id
     * @return bool
     */
    public function del($id)
    {
        $rs = $this->model->wheres(['id' => $id])->delete();
        if($rs){
            $this->redis->del($id);
            return true;
        }

        return false;
    }



}