<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Lib\Redis\Product;
use App\Module\MeetingGroupsRuleModule;
use App\Module\ProductModule;
use App\S\Member\MemberService;
use App\S\Wechat\WeChatShopConfService;
use App\Services\Foundation\QrCodeService;
use App\Services\Lib\JSSDK;
use App\Services\Wechat\ApiService;
use Illuminate\Http\Request;
use MallModule as IndexStoreService;
use WeixinService;
use MicroPageNoticeService as IndexMicroPageNoticeService;
use StoreNavService as IndexStoreNavService;
use Bi;
use  MicroPageService;
use App\S\Weixin\ShopService;
/**
 * 首页
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年3月21日 11:44:01
 */
class IndexController extends Controller {

    /** todo 移动端店铺首页
     * @param Request $request
     * @param StoreService $storeService
     * @param $wid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-03
     * @update 何书哲 2018年7月30日 分享字段添加isset，避免报错
     * @update 吴晓平 2018年09月月10 微信页分享内容优化
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function index(Request $request,ShopService $shopService,$wid) {
        if(empty($wid))
        {
            error('店铺id为空');
        }
        //$storeInfo=WeixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $storeInfo = $shopService->getRowById($wid);
        $store=['store_name'=>'','logo_url'=>config('app.source_url').'mctsource/images/m1logo.png'];
        if(!empty($storeInfo)) {
            $store['store_name'] = $storeInfo['shop_name'];
            if (!empty($storeInfo['logo'])) {
            $store['logo_url'] = imgUrl() . $storeInfo['logo'];
            }
        }
        //何书哲 2018年7月30日 分享字段添加isset，避免报错
        $shareData['share_title'] = isset($storeInfo['share_title']) ? $storeInfo['share_title'] : $store['store_name'];
        $shareData['share_desc']  = isset($storeInfo['share_desc']) ? str_replace(PHP_EOL, '', $storeInfo['share_desc']) :''; //去掉换行符
        $shareData['share_img']  = isset($storeInfo['share_logo']) ? imgUrl() .$storeInfo['share_logo'] : $store['logo_url'];
        //add by 吴晓平 2018.09.10 分享内容读取优化
        $micPageData = MicroPageService::getRowByCondition(['wid' => $wid,'is_home' => 1]);
        if ($micPageData['errCode'] == 0 && $micPageData['data'] && $micPageData['data']['share_title']) {
            $shareData['share_title'] = $micPageData['data']['share_title'];
            $shareData['share_desc']  = $micPageData['data']['share_desc'];
            $shareData['share_img']   = imgUrl().$micPageData['data']['share_img'];
        }
        if (in_array($wid,config('app.li_wid'))){

            $ruleModule = new MeetingGroupsRuleModule();

            $res = $ruleModule->isExistNoEndGroups(session('mid'),'',$wid);
            $isAutoFrame = 0;
            if ($res){
                $isFrame = $ruleModule->isFrame(session('mid'),$wid);
                if (!$isFrame){
                    $isAutoFrame = 1;
                }
            }
            return view('shop.groupsmeeting.shopIndex', [
                'wid'       => $wid,
                'store'     => $store,
                'shareData' => $shareData,
                'isAutoFrame' =>$isAutoFrame
            ]);
        } //end zhangyh

        return view('shop.index.index', [
            'wid'       => $wid,
            'store'     => $store,
            'shareData' => $shareData
        ]);
    }

    /***
     * todo 店铺主页json数据
     * @param Request $request
     * @param StoreService $storeService
     * @param $wid
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-24
     * @update 何书哲 2018年8月31日 店铺数据处理在processMobileData方法
     */
    public function indexStore(Request $request,$wid)
    {
        $filter=$request->input('filter')??true;

        $returnData = array('errCode' => 0, 'errMsg' => '', 'data' => []);
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为空';
            return $returnData;
        }
//
        //$return = IndexStoreService::processStoreData($wid,$filter);
//        if (isset($return['data']['pageid'])) {
//            Bi::micPageView($wid, session('mid'), $return['data']['pageid'], 1);
//        }
//        if (isset($return['data']['container'])) {
//            $return['data']['container'] = str_replace('videoreplace',   '<video width=\"100%\" height=\"280\" controls=\"controls\" poster=\"/ueditor/php/upload/image/20171103/1509689299520580.jpg\"><source src=\"https://upx.cdn.huisou.cn/wscphp/music/aa.mp4 \" type=\"video/mp4\"></video>', $return['data']['container']);
//        }
//        //add by zhangyh
//        $return['data']['container'] = ProductModule::addProductContentHost($return['data']['container']);
//        $return['data']['header'] = ProductModule::addProductContentHost($return['data']['header']);
//        $return['data']['info'] = ProductModule::addProductContentHost($return['data']['info']);
        //end
        return IndexStoreService::processMobileData($wid,1,$filter);
    }

    /**
     * todo 获取微信公众号的密钥
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-22
     */
    public function getWeixinSecretKey(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid = session('wid');
        $conf = D('WeixinPayment', 'wid', $wid)->getList(false);
        $conf = array_column($conf[0]['data'], null, 'payment');

        //微信分享
        if ( isset($conf[1]) ) {
            $conf   = json_decode($conf[1]['config'], true);
            $appId  = $conf['app_id'];
            $secret = $conf['app_secret'];
        }else{
            $appId  = config('app.public_auth_appid');
            $secret = config('app.public_auth_secret');
        }

        $url = $request->input('url');
        try
        {
            $jssdk = new JSSDK($appId, $secret);
            $signPackage = $jssdk->GetSignPackage($url);
            if (!empty($signPackage))
            {
                $returnData['data'] = $signPackage;
            }
            else
            {
                $returnData['errCode']=-2;
                $returnData['errMsg']='没有获取到微信api数据';
            }
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$ex->getMessage();
        }
        return $returnData;
    }

    /**
     * todo 获取微页面公共广告信息针对商品调用
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-07-19
     */
    public  function getMicroPageNotice(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$request->input('wid');
        $applyId=$request->input('apply_id')??3;
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为null';
            return $returnData;
        }
        $result=IndexMicroPageNoticeService::getRow($wid);
        if($result['errCode']==0&&!empty($result['data']))
        {
            $isOn=$result['data']['is_used'];
            if(!$isOn)
            {
                $returnData['errCode']=1;
                $returnData['errMsg']='公共广告未开启';
                return $returnData;
            }
            $location=$result['data']['apply_location'];
            if(empty($location)||$location=='[]')
            {
                $returnData['errCode']=2;
                $returnData['errMsg']='公共广告没有被应用';
                return $returnData;
            }
            $location=json_decode($location,true);
            if(empty($location))
            {
                $returnData['errCode']=-2;
                $returnData['errMsg']='数据转化出现问题';
                return $returnData;
            }
            if(in_array($applyId,$location))
            {
                $returnData['data']['position']=$result['data']['position'];
                $returnData['data']['noticeTemplateData']=$result['data']['notice_template_info'];
                if(!empty($returnData['data']['noticeTemplateData']))
                {
                    if($returnData['data']['noticeTemplateData']=='[]')
                    {
                        $returnData['data']['noticeTemplateData']=null;
                    }
                    else
                    {
                        $returnData['data']['noticeTemplateData']= IndexStoreService::processTemplateData($wid, $returnData['data']['noticeTemplateData']);
                    }
                }
                return $returnData;
            }
            else
            {
                $returnData['errCode']=3;
                $returnData['errMsg']='该店铺下的公共广告没有被商品应用';
                return $returnData;
            }
        }
        else
        {
            return $result;
        }
        return  $returnData;
    }

    /**
     * todo 查找店铺导航被应用的区域
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-08-28
     */
    public  function getStoreNav(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$request->input('wid');
        $applyId=$request->input('apply_id')??4;
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为null';
            return $returnData;
        }
        $result=IndexStoreNavService::getRow($wid);
        if($result['errCode']==0&&!empty($result['data']))
        {
            $isOn=$result['data']['is_used'];
            if(!$isOn)
            {
                $returnData['errCode']=1;
                $returnData['errMsg']='店铺导航未开启';
                return $returnData;
            }
            $location=$result['data']['apply_page'];
            if(empty($location)||$location=='[]')
            {
                $returnData['errCode']=2;
                $returnData['errMsg']='店铺导航没有应用区域';
                return $returnData;
            }
            $location=json_decode($location,true);
            if(empty($location))
            {
                $returnData['errCode']=-2;
                $returnData['errMsg']='数据转化出现问题';
                return $returnData;
            }
            if(in_array($applyId,$location))
            {
                $returnData['data']['nav_template_info']=$result['data']['nav_template_info'];
                if(!empty($returnData['data']['nav_template_info']))
                {
                    if($returnData['data']['nav_template_info']=='[]')
                    {
                        $returnData['data']['nav_template_info']=null;
                    }

                }
                return $returnData;
            }
            else
            {
                $returnData['errCode']=3;
                $returnData['errMsg']='该店铺下的店铺导航没有被商品分组应用';
                return $returnData;
            }
        }
        else
        {
            return $result;
        }
        return  $returnData;
    }

    //获取店铺公众号名称 mayjay 20171124
    public function getApiName()
    {
        $wid = session('wid');
        $apiService = new ApiService();
        $data = $apiService->getShopQrcode($wid);
        if($data){
            success('','',$data);
        }
        error();
    }

    /**
     * 判断是否关注
     * Author: MeiJay
     * @param ApiService $apiService
     * @param MemberService $memberService
     */
    public function isSubscribe(ApiService $apiService,MemberService $memberService)
    {
        $wid = session('wid');
        $mid = session('mid');
        $info = $memberService->getRowById($mid);
        if($info){
            $return =  $apiService->getUserInfo($wid,$info['openid']);
            if(!isset($return['errcode'])){
                success('','',['subscribe' =>$return['subscribe']]);
            }
        }
        error();
    }


    /**
     * Author: MeiJay
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function newKf(MemberService $memberService,ShopService $shopService)
    {
        $userId = session('mid');
        $shopId = session('wid');
        $sign = $shopId . $userId . 'huisou';
        $sign = md5($sign);
        //$storeInfo = WeixinService::init('id',$shopId)->where(['id'=>$shopId])->getInfo();
        $storeInfo = $shopService->getRowById($shopId);
        $shopLogo = imgUrl($storeInfo['logo']);
        $info = $memberService->getRowById($userId);
        $apiUrl = config('app.chat_url')."/#/kefu?userId=".$userId."&shopId=".$shopId."&username=".$info['truename']."&headurl=".$info['headimgurl']."&shopName=".$storeInfo['shop_name']."&shopLog=".$shopLogo."&sign=".$sign;
        return redirect($apiUrl);
    }


    /**
     * 清空缓存
     * @author 张永辉 2019年5月6日
     */
    public function clearCache(Request $request)
    {
        $request->session()->flush();
        echo '<script type="text/javascript">alert("缓存清除成功")</script>';
    }


}
