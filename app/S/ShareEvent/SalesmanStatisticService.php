<?php
namespace App\S\ShareEvent;
use App\Lib\Redis\SalesmanStatisticRedis;
use App\Model\ActivityRegister;
use App\S\Member\MemberService;
use App\S\S;
use DB;

class SalesmanStatisticService extends S
{
	public function __construct()
    {
        parent::__construct('SalesmanStatistic');
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
        $redis = new SalesmanStatisticRedis();
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
        $redis = new SalesmanStatisticRedis();
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
            $storeRedis = new SalesmanStatisticRedis();
            return $storeRedis->update($id,$data);
        }
    }
    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new SalesmanStatisticRedis();
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

    public function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return parent::getList($where, $skip, $perPage, $orderBy, $order); // TODO: Change the autogenerated stub
    }

    public function count($where = [])
    {
        return parent::count($where); // TODO: Change the autogenerated stub
    }


}
