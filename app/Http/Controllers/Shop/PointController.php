<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/24
 * Time: 9:46
 */

namespace App\Http\Controllers\shop;
use App\Http\Controllers\Controller;
use App\Module\OrderModule;
use App\Module\ProductModule;
use App\S\Member\MemberService;
use Illuminate\Http\Request;
use PointRecordService;
use SharePointRuleService;
use SignRecordService;
use SignService;
use MallModule;
use WeixinService as SWeixinService;
use App\S\PublicShareService;
use App\Module\PointModule;
use App\S\Weixin\ShopService;

class PointController extends Controller
{
    public function __construct(MemberService $memberService) {
        $this->memberService = $memberService;
    }

    /**
     * todo 移动端显示我的积分
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-24
     */
    public function point(Request $request)
    {
        $mid=session('mid');
        $wid=session('wid');
        if(empty($wid)||empty($mid))
        {
            error('登录超时');
        }
        $data=['mid'=>$mid,'wid'=>$wid];
        $pageHtml='';
        $pointData=[];
        $pointRecordData=PointRecordService::getMemberPointRecord($data);
        if($pointRecordData['errCode']==0&&!empty($pointRecordData['data']))
        {
            $pointData=$pointRecordData['data'];
        }
        
        return view(
            'shop.point.mypoint',
            [
                'title'      =>'我的积分',
                'point_data' =>$pointData,
                'page_html'  =>$pageHtml,
                'shareData'  => (new PublicShareService())->publicShareSet($wid)
            ]
        );
    }

    /**
     * todo 移动端签到
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-24
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function sign(Request $request,ShopService $shopService,$swid)
    {  
        /*add WuXiaoPing 2017.08.14 分享数据*/ 
        $signData     = SignService::getRow($swid);
        $shareData    = [];
        $share_title  = $share_desc = $share_img = '';
        if (isset($signData['data']['template_data']) && $signData['data']['template_data']) {
            $templateData = json_decode($signData['data']['template_data'],true);
            /*获取type==sign时，后台所填入的签到设置的值*/
            foreach($templateData as $val){
                if($val['type'] == 'sign'){
                    $share_title = !empty($val['shareTitle']) ? $val['shareTitle'] : '';
                    $share_desc  = !empty($val['shareDesc']) ? $val['shareDesc'] : $val['activityInfo'];
                    $share_img   = !empty($val['share_img']) ? imgUrl().$val['share_img'] : '';
                }
            }
        }
        //获取店铺名 logo等
        //$shop = SWeixinService::getStageShop($swid);
        $shop = $shopService->getRowById($swid);
        
        $shareData['share_title'] = $share_title ? $share_title :$shop['shop_name'].'-'.'-签到页';
        $shareData['share_desc']  = $share_desc ? str_replace(PHP_EOL, '', $share_desc) : $shop['shop_name'].'-签到页'; //去掉换行符
        $shareData['share_img']   = $share_img ? $share_img : imgUrl().$shop['logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if(empty($share_title) && empty($share_desc) && empty($share_img)){
            $shareData = (new PublicShareService())->publicShareSet($swid);
        }
        return view('shop.point.sign',
            [
                'title'     => '签到',
                'wid'       => $swid,
                'shareData' => $shareData
            ]
        );
    }

    /**
     * todo 签到规则
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang
     * @date 2017-05-24
     * @update 何书哲 2018年7月31日 因页面报错，添加通用分享设置
     */
    public function signActivityRule(Request $request)
    {
        //何书哲 2018年7月31日 因页面报错，添加通用分享设置
        $wid = session('wid');
        $shareData = (new PublicShareService())->publicShareSet($wid);
        return view('shop.point.signActivityRule',
            [
                'title'       => '签到活动规则',
                'shareData'   => $shareData
            ]
        );
    }

    /**
     * todo 记录签到信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-05-27
     */
    public function addSignRecord(Request $request,$swid)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $mid=$request->input('mid')??session('mid');
        $wid =$swid??session('wid');
        if(empty($mid)||empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $signTemplateData=SignService::getRow($wid);
        //签到功能没有开启
        if($signTemplateData['errCode']==0&&!empty($signTemplateData['data']))
        {
            //0表示该店铺还没有开启积分功能
            if($signTemplateData['data']['is_on']==0)
            {
                $returnData['errCode']=1;
                $returnData['errMsg']='该店铺没有开启签到功能';
                return $returnData;
            }
        }
        return  SignRecordService::addSignRecord($wid,$mid);
    }

    /**
     * todo 分享送积分
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-01
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function addShareRecord(Request $request,ShopService $shopService,$swid)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $wid=$swid??session('wid');
        $mid=session('mid');
        if(empty($mid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='没有店铺id';
            return $returnData;
        }
        //$pointStatusData=SWeixinService::selectPointStatus(['id'=>$wid]);
        $pointStatusData = $shopService->getInfoByCondition(['id' => $wid]);
        if($pointStatusData['errCode']==0&&!empty($pointStatusData['data']))
        {
            //0表示该店铺还没有开启积分功能
            if($pointStatusData['data']['is_point']==0)
            {
                $returnData['errCode']=1;
                $returnData['errMsg']='分享成功';
                return $returnData;
            }
        }
        //is_on为1表示分享送积分开启
        $data=['wid'=>$wid,'is_on'=>1,'mid'=>$mid];
        return  SharePointRuleService::addSharePointRecord($data);
    }

    /**
     * todo 移动端查询积分变更
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-06
     */
    public function selectPointRecord(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $mid=$request->input('mid')??session('mid');
        $wid=$request->input('wid')??session('wid');
        if(empty($mid)||empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $data=['mid'=>$mid,'wid'=>$wid];
        return PointRecordService::getMemberPointRecord($data,1);
    }

    /**
     * todo 查询签到模板数据
     * @param Request $request
     * @author jonzhang
     * @date 2017-06-06
     */
    public  function selectSignTemplateData(Request $request,$swid)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','signTemplateData'=>[],'signData'=>['isSign'=>0,'signDay'=>0],'userData'=>[]];
        $wid=$swid??session('wid');
        $mid=$request->input('mid')??session('mid');
        if(empty($mid)||empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
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
        $userData = $this->memberService->getRowById($mid);
        $returnData['userData'] = $userData;

        //会员是否签到
        $signData=SignRecordService::getUserSign(['mid'=>$mid,'wid'=>$wid]);
        if($signData['errCode']==0&&!empty($signData['data'])) {
            $returnData['signData'] = $signData['data'];
        }
        ProductModule::addProductContentHost($returnData['signTemplateData']['template_data']??''); //add by zhangyh
        return $returnData;
    }

    /**
     * todo 显示签到规则
     * @param Request $request
     * @author jonzhang
     * @date 2017-06-06
     */
    public  function  selectSignRule(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>['activityName'=>'','activityInfo'=>'','shareText'=>'','signList'=>[]]];
        $mid=$request->input('wid')??session('wid');
        if(empty($mid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $signData=SignService::getRow($mid);
        if($signData['errCode']==0&&!empty($signData['data']))
        {
            $signTemplateData=$signData['data']['template_data'];
            $signTemplateData = json_decode($signTemplateData, true);
            //转化数组失败
            if (empty($signTemplateData)) {
                $returnData['errCode'] = -3;
                $returnData['errMsg'] = '签到规则数据有问题';
                return $returnData;
            }
            foreach ($signTemplateData as $signItem) {
                //模板数组中找到签到模块和签到规则
                if ($signItem['type'] == 'sign')
                {
                    $returnData['data']['activityName']=$signItem['activityName'];
                    $returnData['data']['activityInfo']=$signItem['activityInfo'];
                    $returnData['data']['shareText']=$signItem['shareText'];
                    if(!empty($signItem['signList']))
                    {
                        //排序，把积分规则，按照signDay来升序排列
                        if(count($signItem['signList'])>1)
                        {
                            array_multisort(array_column($signItem['signList'], 'signDay'), SORT_ASC, $signItem['signList']);
                        }
                        $returnData['data']['signList'] = $signItem['signList'];
                    }
                    break;
                }
            }
        }
        return $returnData;
    }

    /**
     * 通过金额获取对应可用的积分 Herry 20171117
     */
    public function showPoint(Request $request,ShopService $shopService)
    {
        $list = ['point'=>0,'amount'=>0];

        $amount = $request->input('amount');
        if(floatval($amount) <= 0)
        {
            error('金额必须大于0');
        }

        $point=0;
        $bonusPoints=0.00;
        //是否可使用积分 0表示不可用
        $isUsePoint = 0;
        $wid = session('wid');
        $mid = session('mid');

        //积分兑换货币
        //$storePointData = SWeixinService::selectPointStatus(['id' => $wid]);
        $storePointData = $shopService->getInfoByCondition(['id' => $wid]);
        if ($storePointData['errCode'] == 0 && !empty($storePointData['data']))
        {
            $isUsePoint = $storePointData['data']['is_point'];
        }
        //店铺开启使用积分
        if ($isUsePoint)
        {
            $myPoint = 0;
            //查询该用户拥有的积分
            $memberPointData = $this->memberService->getRowById($mid);
            if (!empty($memberPointData))
            {
                $myPoint = $memberPointData['score'];
            }
            //积分大于0
            if($myPoint>0)
            {
                $exchangeData = (new OrderModule())->getAmountByPoint($wid,$myPoint,$amount);
                if($exchangeData['errCode']==0&&!empty($exchangeData['data']))
                {
                    //用户可用的积分，金额
                    $point = $exchangeData['data']['point'];
                    $bonusPoints = $exchangeData['data']['amount'];
                }
                else
                {
                    error($exchangeData['errMsg']);
                }
            }
        }

        $list['point']=$point;
        $list['amount']=$bonusPoints;

        success('', '', $list);
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
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        //接收参数
        $wid=$request->input('wid')??0;
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
        $result=$pointModule->isGivePoint($wid);
        if($result['errCode']==0)
        {
            $returnData['list']=$result['data'];
        }
        return $returnData;
    }
}