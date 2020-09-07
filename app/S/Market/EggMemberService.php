<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/3
 * Time: 17:27
 */

namespace App\S\Market;


use App\Lib\Redis\EggMemberRedis;
use App\S\S;
use Illuminate\Support\Facades\DB;

class EggMemberService extends  S
{

    /**
     * EggMemberService constructor.
     */
    public function __construct()
    {
        parent::__construct('EggMember');
    }

    public function getStaticList() {
        return ['0' => '所有参与', '1' => '中奖', '2' => '未中奖'];
    }


    //添加
    public function create($data)
    {
        $re = $this->model->insertGetId($data);
        if (!$re) {
            return false;
        }
        return $re;
    }

    /**
     * 修改
     * @param $id
     * @param $data
     * @return bool
     * @author: 梅杰 2018年8月17日
     */
    public function update($id,$data)
    {
        if ($this->model->where(['id'=>$id])->update($data)) {
            //更新redis
            $data['id'] = $id;
            $data['update_at'] = date('Y-m-d H:i:s');
            $redis = new EggMemberRedis();
            $redis->updateRow($data);
            return true;
        }
        return false;
    }

    //获取砸蛋活动用户的全程参与数据
    public function getAllJoinAmount($eggId,$mid)
    {
        //1、整个活动期间的参与次数
        $where = [
            'egg_id'=>$eggId,
            'mid' =>$mid
        ];
        return $this->count($where);
    }

    //今天参与数据条数
    public function getTodayJoinAmount($eggId,$mid)
    {
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
        $where = [
            'egg_id'=>$eggId,
            'mid' => $mid,
            'created_at' => ['between',[$start,$end]],
        ];
        return $this->count($where);
    }

    //获取砸蛋活动用户的全程中奖数据
    public function getAllPrizeAmount($eggId,$mid)
    {
        //1、整个活动期间的参与次数
        $where = [
            'egg_id'=>$eggId,
            'mid' =>$mid,
            'is_prize' => 1,
        ];
        return $this->count($where);
    }

    //今天中奖数据数
    public function getTodayPrizeAmount($eggId,$mid)
    {
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
        $where = [
            'egg_id'=>$eggId,
            'mid' => $mid,
            'is_prize' => 1,
            'created_at' => ['between',[$start,$end]],
        ];
        return $this->count($where);
    }


    
    /**
     * 获取非分页列表
     * @return array
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }
    
    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @param int $pageSize
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {

        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
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
        $redis = new EggMemberRedis();
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

    public function getPrizeInfo($where)
    {
        if ($re = $this->model->where($where)->get()) {
            $re = $re->load('prize')->toArray();
        }
        return $re;

    }

    /**
     * 获取砸金蛋一条参与记录
     * @param int $id 参与记录id
     * @return array
     * @author 许立 2018年08月20日
     */
    public function getRowById($id)
    {
        $redis = new EggMemberRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result){
                return [];
            }
            $result = $result->toArray();
            $redis->add($result);
        }
        return $result;
    }


    /**
     * 活动参与次数
     * @param $eggId
     * @return array
     * @author: 梅杰 2018年8月21日
     */
    public function logCount($eggId)
    {
        //1、整个活动期间的参与次数
        $prize = $this->count(['egg_id'=>$eggId, 'is_prize' => 1]);
        $logCount = $this->count(['egg_id'=>$eggId]);
        $memberCount = $this->model->where( ['egg_id'=>$eggId])->count([
            DB::raw('DISTINCT mid'),
        ]);
        return [
            'prize' => $prize,
            'all'   => $logCount,
            'memberCount' => $memberCount
        ];
    }


    /**
     * 删除接口
     * @param $id
     * @return mixed
     * @author: 梅杰 2018年8月21日
     */
    public function del($id)
    {
        return $this->model->where(['id'=>$id])->delete();
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
            $redis = new EggMemberRedis();
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