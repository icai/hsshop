<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/19
 * Time: 10:22
 */


namespace App\S\Customer;
use App\Jobs\CheckGrantCard;
use App\Jobs\SubMsgPushJob;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use App\S\S;
use App\S\Member\MemberService as PMemberService;
use App\Lib\Redis\PointRecordRedis;
use App\S\WXXCX\SubscribeMessagePushService;

class PointRecordService extends S
{
    public function __construct()
    {
        parent::__construct('PointRecord');
    }
    /**
     * todo  添加数据
     * @param $data
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-19
     * @update 何书哲 2018年11月08日 发放积分消息通知
     * @update 吴晓平 修改小程序积分变更提醒发送订阅模板消息 2019年12月20日 13:40:51
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
        //验证按规则发放会员卡
        dispatch((new CheckGrantCard($data['mid'],$data['wid']))->onQueue('CheckGrantCard'));
        // @update 吴晓平 修改小程序积分变更提醒发送订阅模板消息 2019年12月20日 13:40:51
        // 发送模板的相关内容
        $param = [
            'mid' => $data['mid'],
            'score' => $data['score'],
            'point_type' => $data['point_type'] ?? 0
        ];

        // 模板发送的初步数据
        $packData = [
            'wid' => $data['wid'],
            'openid' => '',
            'param' => []
        ];
        // 组装后的数据
        $sendData = app(SubscribeMessagePushService::class)->packageSendData(5, $packData);
        dispatch(new SubMsgPushJob(5, $data['wid'], $sendData, $param));

        //何书哲 2018年11月08日 发放积分消息通知
        (new MessagePushModule($data['wid'], MessagesPushService::PointConsume))->sendMsg($data);

        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 显示会员的积分变更记录
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-05
     */
    public function getMemberPointRecord($data=[],$isScore=false)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','totalScore'=>0,'data' => [],'currentPage'=>0,'pageSize'=>15,'total'=>0,'links' => ''];
        if (empty($data)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询数据为空';
            return $returnData;
        }
        $totalScore=0;
        if($isScore)
        {
            if (!empty($data['mid']))
            {
                $memberService=new PMemberService();
                $currentScoreData =$memberService->getRowById($data['mid']);
                if (!empty($currentScoreData))
                {
                    $totalScore = $currentScoreData['score'];
                }
            }
            $returnData['totalScore']=$totalScore;
        }

        $result=$this->getListByConditionWithPage($data,'id','desc');
        if(!empty($result[1]))
        {
            $returnData['links'] = $result[1];
        }
        //add by jonzhang 2017-10-11 自定义分页方法
        if(!empty($result[0]))
        {
            $returnData['currentPage']=$result[0]['current_page'];
            $returnData['pageSize']=$result[0]['per_page'];
            $returnData['total']=$result[0]['total'];
        }
        $resultList = $result[0];

        $pointData=[];
        if(!empty($resultList['data']))
        {
            $i=0;
            //当分页时，totalScore显示的数据会有问题，暂且不要显示totalScore 张国军 2018年07月31日
            $prevTotalScore=0;
            foreach($resultList['data'] as $item)
            {
                if($isScore)
                {
                    if ($i == 0)
                    {
                        $item['totalScore'] = $totalScore;
                        $prevTotalScore=$totalScore;
                    }
                    else
                    {
                        $item['totalScore']=$prevTotalScore;
                    }
                    if ($item['is_add'] == 1)
                    {
                        $prevTotalScore = $prevTotalScore - $item['score'];
                    }
                    else
                    {
                        $prevTotalScore = $prevTotalScore + $item['score'];
                    }
                    $i++;
                }
                $typeName='';
                switch($item['point_type']){
                    case 1:
                        $typeName ='消费送积分';
                        break;
                    case 2:
                        $typeName ='签到送积分';
                        break;
                    case 3:
                        $typeName ='分享送积分';
                        break;
                    case 4:
                        $typeName ='积分抵现';
                        break;
                    case 5:
                        $typeName ='系统赠送积分';
                        break;
                    case 6:
                        $typeName='退还积分';
                        break;
                    case 7:
                        $typeName='领取会员卡赠送积分';
                        break;
                    case 8:
                        if ($item['is_add'] == 1){
                            $typeName='大转盘赠送积分';
                        }else{
                            $typeName='大转盘消耗积分';
                        }
                        break;
                    case 9:
                        $typeName='砸金蛋赠送积分';
                        break;
                    case 10:
                        $typeName='充值送积分';
                        break;
                    case 11:
                        if ($item['is_add'] == 1){
                            $typeName='刮刮卡赠送积分';
                        }else{
                            $typeName='刮刮卡消耗积分';
                        }
                        break;
                    default :
                        $typeName='其他类型';
                        break;
                }
                $item['type_name']=$typeName;
                if($item['is_add']==1)
                {
                    $item['score']='+'.$item['score'];
                }
                else
                {
                    $item['score']='-'.$item['score'];
                }
                unset($item['point_type']);
                unset($item['id']);
                //add by jonzhang 2018-05-04 备注为null进行处理
                $item['remark']=$item['remark']??'';
                $pointData[]=$item;
            }
        }

        $returnData['data'] = $pointData;
        return $returnData;
    }
    /**
     * todo 统计用户分享店铺数
     * @param $data
     * @return array
     * @author jonzhang
     * @date 2017-05-31
     */
    public function selectNum($data)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='查询数据为空';
            return $returnData;
        }
        //此处wheres不是系统自带的 是重写的
        $result=$this->model->wheres($data)->count();
        if($result===false)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='统计数据失败';
            return $returnData;
        }
        $returnData['data']=$result;
        return $returnData;
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * todo 通过数组id来查询积分记录信息
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-07-13
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new PointRecordRedis();

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
     * todo 查询积分记录数据信息[分页]
     * @param $data
     * @return array
     * @author jonzhang
     * @date 2017-07-13
     */
    public function getListByConditionWithPage($data=[],$orderBy='',$order='',$pageSize=0)
    {
        /* 查询条件数组 */
        $where = [];

        /* 参数转换为查询条件数组 */
        if ($data) {
            foreach ($data as $key => $value) {
                switch ( $key ) {
                    //店铺id
                    case 'wid':
                        $where['wid'] = $value;
                        break;
                    //会员id
                    case 'mid':
                        $where['mid'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
    }


    /**
     * 批量更新积分
     * @param $ids
     * @param $data
     * @return bool
     * @author 张永辉 2018年9月18日
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redis = new PointRecordRedis();
            return $redis->batchUpdate($ids,$data);
        }else{
            return false;
        }
    }

    /**
     * 返回积分类型
     * @param $point_type
     * @param $is_add
     * @return string
     * @author 何书哲 2018年11月09日
     */
    public function getPointType($point_type, $is_add) {
        switch($point_type){
            case 1:
                $typeName ='消费送积分';
                break;
            case 2:
                $typeName ='签到送积分';
                break;
            case 3:
                $typeName ='分享送积分';
                break;
            case 4:
                $typeName ='积分抵现';
                break;
            case 5:
                $typeName ='系统赠送积分';
                break;
            case 6:
                $typeName='退还积分';
                break;
            case 7:
                $typeName='领取会员卡赠送积分';
                break;
            case 8:
                if ($is_add == 1){
                    $typeName='大转盘赠送积分';
                }else{
                    $typeName='大转盘消耗积分';
                }
                break;
            case 9:
                $typeName='砸金蛋赠送积分';
                break;
            case 10:
                $typeName='充值送积分';
                break;
            case 11:
                if ($is_add == 1){
                    $typeName='刮刮卡赠送积分';
                }else{
                    $typeName='刮刮卡消耗积分';
                }
                break;
            default :
                $typeName='其他类型';
                break;
        }
        return $typeName;
    }




}
