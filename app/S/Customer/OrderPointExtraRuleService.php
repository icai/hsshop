<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/22
 * Time: 19:44
 */

namespace App\S\Customer;
use App\S\S;
use App\Lib\Redis\OrderPointExtraRuleRedis;

class OrderPointExtraRuleService extends S
{
    public function __construct()
    {
        parent::__construct('OrderPointExtraRule');
    }
    /**
     * todo  添加数据
     * @param $data
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-19
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更新数据
     * @param $id
     * @param $data
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-19
     */
    public function updateData($id=0,$data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if($updateReturnValue===false)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new OrderPointExtraRuleRedis();
        $updateData=$data;
        $updateData['updated_at']=date('Y-m-d H:i:s');
        $i=0;
        $status=false;
        while($i<3&&!$status)
        {
            if($redis->updateRedis($id,$updateData))
            {
                $status=true;
            }
            $i++;
        }
        if(!$status)
        {
            $deleteReturnValue=$redis->deleteRedis($id);
            if(!$deleteReturnValue)
            {
                $returnData['errCode']=-4;
                $returnData['errMsg']='处理缓存失败';
                return $returnData;
            }
        }
        return $returnData;
    }

    /**
     * todo 通过范围查询 来删除订单积分的额外规则
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-06
     */
    public function deleteByCondition($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        //此处wheres不是系统自带的 是重写的
        $result = $this->model->wheres($data)->get()->toArray();
        //$result没有数据，返回[]
        if(!empty($result))
        {
            $redis=new OrderPointExtraRuleRedis();
            foreach($result as $item)
            {
                $i=0;
                $status=$redis->deleteRedis($item['id']);
                while($i<3&&!$status)
                {
                    if($redis->deleteRedis($item['id']))
                    {
                        $status=true;
                    }
                    $i++;
                }
            }
            $j=0;
            $deleteStatus=$this->model->wheres($data)->delete();
            while($j<3&&!$deleteStatus)
            {
                if($this->model->wheres($data)->delete())
                {
                    $deleteStatus=true;
                }
                $j++;
            }
        }
        return $returnData;
    }

    /**
     * todo 查询出额外规则的数据
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @param int $pageSize
     * @return array
     * @author jonzhang
     * @date 2017-07-17
     */
    public function getListByConditionWithPage($where = [], $orderBy = '', $order = '',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($where))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $result=$this->getListWithPage($where,$orderBy,$order,$pageSize);
        $returnData['data']=$result[0]['data'];
        return $returnData;
    }
    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * todo 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new OrderPointExtraRuleRedis();

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
}