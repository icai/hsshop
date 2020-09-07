<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/17
 * Time: 9:29
 */

namespace App\S\Customer;
use App\S\S;
use App\S\Member\MemberService;
use PointRecordService as SPointRecordService;
use App\Lib\Redis\SharePointRuleRedis;

class SharePointRuleService extends S
{
    public function __construct()
    {
        parent::__construct('SharePointRule');
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
        $redis=new SharePointRuleRedis();
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
     * todo 获取微页面信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-07-03
     */
    public function getRowById($id,$isCache=true)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $result=[];
        $redis = new SharePointRuleRedis();
        //使用缓存
        if($isCache)
        {
            $result = $redis->getOne($id);
        }
        if(empty($result))
        {
            $result = $this->model->where(['id' => $id])->first();
            if (empty($result))
            {
                if($result===false)
                {
                    $returnData['errCode'] = -2;
                    $returnData['errMsg'] = '查询数据出现错误';
                    return $returnData;
                }
                return $returnData;
            }
            $result=$result->toArray();
            $redis->addArr($result);
        }
        $returnData['data']=$result;
        return $returnData;
    }

    /**
     * todo 通过查询条件来查询id
     * @param $wid
     * @return array
     * @author jonzhang
     * @date 2017-07-17
     */
    public function getRowByCondition($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        $result = $this->model->select(['id'])->where($data)->first();
        if(empty($result))
        {
            if($result===false)
            {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '查询数据出现错误';
                return $returnData;
            }
            //如果没有查询到符合要求的数据$result为null
            return $returnData;
        }
        $result=$result->toArray();
        return $this->getRowById($result['id']);
    }

    /**
     * todo 分享添加积分
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-17
     */
    public function addSharePointRecord($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='查询数据为空';
            return $returnData;
        }
        $mid=$data['mid'];
        unset($data['mid']);
        //data中传递的有is_on=1，表示开启分享送积分，查询出来的数据则为开启分享送积分功能中的分享规则信息
        $sharePointRule=$this->getRowByCondition($data);
        //分享积分开启，赠送积分
        if(!empty($sharePointRule)&&!empty($sharePointRule['data']))
        {
            //积分
            $shareScore=$sharePointRule['data']['basic_rule'];
            //限制分享次数
            $shareLimit=$sharePointRule['data']['limit_rule'];
            //查询条件
            $whereData=['wid'=>$data['wid'],'mid'=>$mid,'point_type'=>3,'is_add'=>1];
            //当前日期
            $currentDate=date("Y-m-d");
            //开始时间 记得要有空格' 00:00:00'
            $beginDate=$currentDate.' 00:00:00';
            //结束时间
            $endDate=$currentDate.' 23:59:59';
            //日期的范围
            $whereData['created_at']=['between',[$beginDate,$endDate]];
            //查询用户当天已经分享的次数
            $sharePointNum=SPointRecordService::selectNum($whereData);
            if($sharePointNum['errCode']==0) {
                //判断用户分享次数，是否大于最高次数
                if($sharePointNum['data']>=$shareLimit)
                {
                    $returnData['errCode']=3;
                    $returnData['errMsg']='超出当天分享送积分的次数';
                    return $returnData;
                    //$shareScore=0;
                }
                $pointRecordData = ['wid' => $data['wid'], 'mid' => $mid, 'point_type' => 3, 'is_add' => 1, 'score' =>$shareScore ];
                //消费积分记录
                $pointRecord=SPointRecordService::insertData($pointRecordData);
                if($pointRecord['errCode']==0&&!empty($pointRecord['data']))
                {
                    $memberScore=intval($shareScore);
                    //查询该用户目前积分
                    $memberData=(new MemberService())->getRowById($mid);
                    if(!empty($memberData)) {
                        $memberScore=$memberScore+$memberData['score'];
                    }
                    //更改用户积分
                    (new MemberService())->updateData($mid, ['score'=>$memberScore]);
                    $returnData['data']=$shareScore;
                }
            }
            return $returnData;
        }
        $returnData['errCode']=2;
        $returnData['errMsg']='该店铺分享送积分没有开启';
        return $returnData;
    }
}