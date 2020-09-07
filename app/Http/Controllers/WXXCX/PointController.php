<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/10/11
 * Time: 9:18
 */

namespace App\Http\Controllers\WXXCX;
use App\Http\Controllers\Controller;
use App\Jobs\SendTplMsg;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use Illuminate\Http\Request;
use MemberService;
use PointRecordService;
use SharePointRuleService;
use SignRecordService;
use SignService;
use MallModule;
use WeixinService;
use CommonModule;
use App\Module\OrderModule;
use App\Module\PointModule;
use App\S\Weixin\ShopService;

class PointController extends Controller
{
    /**
     * todo 记录签到信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-10-12
     * @update 何书哲 2018年10月18日 签到成功消息通知
     */
    public function addSignRecord(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        //接收参数
        $token= $request->input('token');
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        $signTemplateData=SignService::getRow($wid);
        //签到功能没有开启
        if($signTemplateData['errCode']==0&&!empty($signTemplateData['data']))
        {
            //0表示该店铺还没有开启积分功能
            if($signTemplateData['data']['is_on']==0)
            {
                $returnData['code']=1;
                $returnData['hint']='该店铺没有开启签到功能';
                return $returnData;
            }
        }
        $result=SignRecordService::addSignRecord($wid,$mid);
        if($result['errCode']==0)
        {
            $returnData['list']=$result['data'];
            (new MessagePushModule($wid, MessagesPushService::SignSuccess, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['mid'=>$mid,'wid'=>$wid], $userInfo[3]??0);
        }
        else
        {
            $returnData['code']=$result['errCode'];
            $returnData['hint']=$result['errMsg'];
        }
        return $returnData;
    }

    /**
     * todo 分享送积分
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-10-11
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function addShareRecord(Request $request,ShopService $shopService)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>0];
        //接收参数
        $token= $request->input('token');
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        //$pointStatusData=WeixinService::selectPointStatus(['id'=>$wid]);
        $pointStatusData = $shopService->getRowById($wid);
        if(!empty($pointStatusData))
        {
            //0表示该店铺还没有开启积分功能
            if($pointStatusData['is_point']==0)
            {
                $returnData['code']=1;
                $returnData['hint']='分享成功';
                return $returnData;
            }
        }
        //is_on为1表示分享送积分开启
        $data=['wid'=>$wid,'is_on'=>1,'mid'=>$mid];
        $result=SharePointRuleService::addSharePointRecord($data);
        if($result['errCode']==0)
        {
            $returnData['list']=$result['data'];
        }
        else
        {
            $returnData['code']=$result['errCode'];
            $returnData['hint']=$result['errMsg'];
        }
        return $returnData;
    }

    /**
     * todo 移动端查询积分变更
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-10-11
     */
    public function selectPointRecord(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        //接收参数
        $token= $request->input('token');
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        $data=['mid'=>$mid,'wid'=>$wid];
        $result=PointRecordService::getMemberPointRecord($data,1);
        if($result['errCode']==0)
        {
            $returnData['list']=$result['data'];
            $returnData['totalScore']=$result['totalScore'];
            $returnData['currentPage']=$result['currentPage'];
            $returnData['pageSize']=$result['pageSize'];
            $returnData['total']=$result['total'];
        }
        else
        {
            $returnData['code']=$result['errCode'];
            $returnData['hint']=$result['errMsg'];
        }
        return $returnData;
    }

    /**
     * todo 查询签到模板数据
     * @param Request $request
     * @author jonzhang
     * @date 2017-10-11
     */
    public  function selectSignTemplateData(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','signTemplateData'=>[],'signData'=>['isSign'=>0,'signDay'=>0],'userData'=>[]];
        //接收参数
        $token= $request->input('token');
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        //签到模板数据
        $signTemplateData=SignService::getRow($wid);
        if($signTemplateData['errCode']==0&&!empty($signTemplateData['data']))
        {
            if(!empty($signTemplateData['data']['template_data']))
            {
                $signTemplate=MallModule::processTemplateData($wid,$signTemplateData['data']['template_data']);
                $signTemplate=json_decode($signTemplate,true);
                $signProcessTemplate=[];
                foreach($signTemplate as $signItem)
                {
                    //模板数组中找到签到模块和签到规则
                    if ($signItem['type'] == 'sign'&&!empty($signItem['signList']))
                    {
                        //排序，把积分规则，按照signDay来升序排列
                        if(count($signItem['signList'])>1)
                        {
                            array_multisort(array_column($signItem['signList'], 'signDay'), SORT_ASC, $signItem['signList']);
                        }
                    }
                    $signProcessTemplate[]=$signItem;
                }
                if(!empty($signProcessTemplate))
                {
                    $signProcessTemplate=json_encode($signProcessTemplate);
                }
                else
                {
                    $signProcessTemplate='';
                }
                $signTemplateData['data']['template_data']=$signProcessTemplate;
            }
            $returnData['signTemplateData']=$signTemplateData['data'];
        }
        //会员信息
        $userData = MemberService::getRowById($mid);
        $returnData['userData'] = $userData;

        //会员是否签到
        $signData=SignRecordService::getUserSign(['mid'=>$mid,'wid'=>$wid]);
        if($signData['errCode']==0&&!empty($signData['data']))
        {
            $returnData['signData'] = $signData['data'];
        }
        return $returnData;
    }

    /**
     * todo 显示签到规则
     * @param Request $request
     * @author jonzhang
     * @date 2017-10-11
     */
    public  function  selectSignRule(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>['activityName'=>'','activityInfo'=>'','shareText'=>'','signList'=>[]]];
        $token= $request->input('token');
        //判断token
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        $wid=CommonModule::getWidByToken($token);
        if(empty($wid))
        {
            $returnData['code']=-1;
            $returnData['hint']='缓存中存放的店铺id有问题';
            return $returnData;
        }
        $signData=SignService::getRow($wid);
        if($signData['errCode']==0&&!empty($signData['data']))
        {
            $signTemplateData=$signData['data']['template_data'];
            $signTemplateData = json_decode($signTemplateData, true);
            //转化数组失败
            if (empty($signTemplateData)) {
                $returnData['code'] = -2;
                $returnData['hint'] = '签到规则数据有问题';
                return $returnData;
            }
            foreach ($signTemplateData as $signItem) {
                //模板数组中找到签到模块和签到规则
                if ($signItem['type'] == 'sign')
                {
                    $returnData['list']['activityName']=$signItem['activityName'];
                    $returnData['list']['activityInfo']=$signItem['activityInfo'];
                    $returnData['list']['shareText']=$signItem['shareText'];
                    if(!empty($signItem['signList']))
                    {
                        //排序，把积分规则，按照signDay来升序排列
                        if(count($signItem['signList'])>1)
                        {
                            array_multisort(array_column($signItem['signList'], 'signDay'), SORT_ASC, $signItem['signList']);
                        }
                        $returnData['list']['signList'] = $signItem['signList'];
                    }
                    break;
                }
            }
        }
        return $returnData;
    }

    /**
     * todo 通过金额获取对应可用的积分
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-10-17
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getPointByAmount(Request $request,ShopService $shopService)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>['point'=>0,'amount'=>0]];
        //接收参数
        $token= $request->input('token');
        //金额
        $amount=$request->input('amount');
        $amount=floatval($amount)??0;
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        if(empty($amount))
        {
            $returnData['code']=-103;
            $returnData['hint']='金额为0';
            return $returnData;
        }
        $point=0;
        $bonusPoints=0.00;
        if($amount>0)
        {
            //是否可使用积分 0表示不可用
            $isUsePoint = 0;
            //积分兑换货币
            //$storePointData = WeixinService::selectPointStatus(['id' => $wid]);
            $storePointData = $shopService->getRowById($wid);
            if (!empty($storePointData))
            {
                $isUsePoint = $storePointData['is_point'];
            }
            //店铺开启使用积分
            if ($isUsePoint)
            {
                $myPoint = 0;
                //查询该用户拥有的积分
                $memberPointData = MemberService::getRowById($mid);
                if (!empty($memberPointData))
                {
                    $myPoint = $memberPointData['score'];
                }
                //积分大于0
                if($myPoint>0)
                {
                    $orderModule=new OrderModule();
                    $exchangeData = $orderModule->getAmountByPoint($wid,$myPoint,$amount);
                    if($exchangeData['errCode']==0&&!empty($exchangeData['data']))
                    {
                        //用户可用的积分，金额
                        $point = $exchangeData['data']['point'];
                        $bonusPoints = $exchangeData['data']['amount'];
                    }
                    else
                    {
                        $returnData['code']=$exchangeData['errCode'];
                        $returnData['hint']=$exchangeData['errMsg'];
                        return $returnData;
                    }
                }
            }
        }
        $returnData['list']['point']=$point;
        $returnData['list']['amount']=$bonusPoints;
        return $returnData;
    }

    /***
     * todo 是否赠送积分
     * @param Request $request
     * @param PointModule $pointModule
     * @return array
     * @author jonzhang
     * @date 2018-04-10
     */
    public function isGivePoint(Request $request,PointModule $pointModule)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>0];
        //接收参数
        $token= $request->input('token');
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-1001;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        $result=$pointModule->isGivePoint($userInfo[1]);
        if($result['errCode']==0)
        {
            $returnData['list']=$result['data'];
        }
        return $returnData;
    }
}