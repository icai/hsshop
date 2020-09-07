<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/3/15
 * Time: 19:45
 */

namespace App\Module;

use App\Lib\Redis\RedisClient;
use App\Lib\WXXCX\ThirdPlatform;
use App\S\Store\TemplateMarketService;
use App\S\Weixin\DeliveryConfigService;
use App\S\Weixin\DeliveryPrinterService;
use App\Services\Permission\WeixinRoleService;
use App\Services\Permission\WeixinUserService;
use App\Services\UserService;
use App\Services\WeixinService;
use ProductService;
use MicroPageService;
use Upyun\Config;
use Upyun\Upyun;
use WXXCXMicroPageService;
use MicroPageTypeService;
use StoreNavService;
use MemberHomeService;
use PermissionService;
use QrCode;
use Storage;
use App\S\Weixin\ShopService;

class StoreModule
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180315
     * @desc 创建店铺之后
     * @param $admin_role_id int 权限id
     * @param $shop_valid_days int 店铺有效体验天数
     * @param $is_permission_session bool 是否把权限存session里
     */
    function afterCreateShop($id,$input,$userId,$admin_role_id = 1,$shop_valid_days = 7,$is_permission_session = true)
    {
        ProductService::createDefaultGroups($id);
        $idArr = ProductService::createDefaultProducts($id);
        $data=[
            'wid'=>$id,'page_title'=>'店铺主页','is_show'=>1,'is_home'=>1,
            'page_template_info'=>'[{"showRight":true,"cardRight":4,"type":"goods","editing":"editing","listStyle":1,"cardStyle":1,"showSell":true,"btnStyle":1,"goodName":false,"goodInfo":false,"priceShow":true,"nodate":false,"products_id":'.json_encode($idArr).',"goods":[],"thGoods":[]}]'
        ];
        MicroPageService::insertData($data);

        $xcxData=[
            'wid'=>$id,'title'=>'店铺主页','is_home'=>1,
            'template_info'=>'[{"showRight":true,"cardRight":4,"type":"goods","editing":"editing","listStyle":1,"cardStyle":1,"showSell":true,"btnStyle":1,"goodName":false,"goodInfo":false,"priceShow":true,"nodate":false,"products_id":'.json_encode($idArr).',"goods":[],"thGoods":[]}]'
        ];
        //创建小程序默认店铺主页
        WXXCXMicroPageService::insertData($xcxData);

        //添加默认微页面分类
        $microPageTypeData=[];
        $pagetypeHostData1=['wid'=>$id,'title'=>'*最热分类','is_auto'=>1,'show_style'=>1,
                            'type_template_info'=>'[{"showRight":true,"cardRight":2,"type":"category","title":"*最热分类","editing":"editing","desc":"","firstChoose":0,"secondChoose":3,"showStyle":0,"pageList":[]}]'];
        $microPageTypeData[]=$pagetypeHostData1;
        $pagetypeHostData2=['wid'=>$id,'title'=>'*最新分类','is_auto'=>1,'show_style'=>1,
                            'type_template_info'=>'[{"showRight":true,"cardRight":2,"type":"category","title":"*最新分类","editing":"editing","desc":"","firstChoose":0,"secondChoose":3,"showStyle":0,"pageList":[]}]'
        ];
        $microPageTypeData[]=$pagetypeHostData2;
        $pagetypeOtherData=['wid'=>$id,'title'=>'*未分类','is_auto'=>1,'show_style'=>1,
                            'type_template_info'=>'[{"showRight":true,"cardRight":2,"type":"category","title":"*未分类","editing":"editing","desc":"","firstChoose":0,"secondChoose":3,"showStyle":0,"pageList":[]}]'
        ];
        $microPageTypeData[]=$pagetypeOtherData;
        foreach($microPageTypeData as $item)
        {
            MicroPageTypeService::insertData($item);
        }

        //添加默认店铺导航
        $sourceUrl=config('app.source_url');
        $navTemplate='{"menusType":1,"menu":[';
        $navTemplate.='{"title":"首页","linkUrl":"/shop/index/'.$id.'","linkUrlName":"首页","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/01.png","iconActive":"/static/images/01_on.png","dropDown":false},';
        $navTemplate.='{"title":"商品","linkUrl":"/shop/product/index/'.$id.'","linkUrlName":"商品","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/02.png","iconActive":"/static/images/02_on.png","dropDown":false},';
        $navTemplate.='{"title":"购物车","linkUrl":"/shop/cart/index/'.$id.'","linkUrlName":"购物车","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/03.png","iconActive":"/static/images/03_on.png","dropDown":false},';
        $navTemplate.='{"title":"我的","linkUrl":"/shop/member/index/'.$id.'","linkUrlName":"我的","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/04.png","iconActive":"/static/images/04_on.png","dropDown":false}';
        $navTemplate.='],"bgColor":"#ffffff","title":"微信公众号自定义菜单样式"}';

        $storeNavData=[
            'wid'=>$id,'is_used'=>1,'apply_page'=>'[1,2,3,4,5]',
            'nav_template_info'=>$navTemplate
        ];
        $result=StoreNavService::insertData($storeNavData);

        //添加会员主页 add by jonzhang
        #begin
        //获取店铺名称
        $storeName=$input['shop_name'];
        $homeInfo='[';
        $homeInfo.='{"showRight":true,"cardRight":1,"type":"member","title":"会员主页","editing":"editing","thumbnail":"mctsource/images/personal.png","levelShow":true,"showCredit":false},';
        $homeInfo.='{"showRight":false,"cardRight":7,"type":"store","editing":"","id":'.$id.',"store_name":"'.$storeName.'","url":"'.config('app.url').'shop/index/'.$id.'"}';
        $homeInfo.=']';
        $memberHome=[
            'wid'=>$id,
            'home_name'=>'会员主页',
            'custom_info'=>$homeInfo
        ];
        MemberHomeService::insertData($memberHome);


        $weixinUserData = [
            'wid'       => $id,
            'uid'       => $userId,
            'oper_id'  => $userId,
            'role_id'  => 1
        ];
        $weixinUserService = new WeixinUserService();
        $weixinRoleService = new WeixinRoleService();
        $weixinUserService->init()->add($weixinUserData,false);
        /*添加店铺角色*/
        $weixinRoleData =[
            'wid'               => $id,
            'admin_role_id'    => $admin_role_id,
            'end_time'          => date('Y-m-d H:i:s',strtotime('+' . $shop_valid_days . ' day'))// xiugai  fuguowei  体验时间改为7天
        ];
        $weixinRoleService->init()->add($weixinRoleData,false);

        if ($is_permission_session) {
            PermissionService::addPermissionToRedis();
        }

        //定义存放模板的变量
        $templateMarketData=[];
        $templateData=(new TemplateMarketService())->getRowById(13);
        if($templateData['errCode']==0&&!empty($templateData['data']))
        {
            $defaultTemplateData=$templateData['data']['template_data'];
            $defaultTemplateData=json_decode($defaultTemplateData,true);//dd($defaultTemplateData);
            //模板数据中 添加默认的商品
            if(!empty($defaultTemplateData)) {
                foreach ($defaultTemplateData as $item)
                {
                    $item['products_id'] = [];
                    if ($item['type'] == 'goods') {
                        $item['products_id'] = $idArr;
                    }
                    $templateMarketData[] = $item;
                }
            }
        }
        //更改默认的店铺主页信息
        if(!empty($templateMarketData))
        {
            $microPageData['page_template_info'] =json_encode($templateMarketData);
            $microPageData['template_id']=13;
            $resultValue=MicroPageService::selectIDByCondition(['wid' => $id, 'is_home' => 1]);
            if($resultValue['errCode']==0&&!empty($resultValue['data']))
            {
                MicroPageService::updateData($resultValue['data']['id'],$microPageData);
            }
        }

    }

    /**
     * @auth hsz
     * @date 2018/3/16 15:46
     * @desc 处理店铺列表数据
     * @param $data
     * @return mixed
     */
    function dealListShop(&$data){
        foreach ($data as $key => &$value){
            $value['show_status'] = 1;
            $value['weixinConfigSub'] = is_null($value['weixinConfigSub']) ? 0 : 1;
            if($value['logo']){
                $value['logo'] = $value['logo'];
            }else{
                $value['logo'] = 'hsshop/image/static/huisouyun_120.png';
            }
            $weixinRoleData = (new WeixinRoleService())
                ->init()
                ->where(['wid'=>$value['id']])
                ->select(['end_time'])
                ->getList(false)[0]['data'];
            if($weixinRoleData[0]){
                if(strtotime($weixinRoleData[0]['end_time']) < time()){
                    $value['is_overdue'] = 1; //已打烊
                }else{
                    if(strtotime($weixinRoleData[0]['end_time']) > strtotime('+7 days')){//有效期大于15天时，说明是续费店铺,店铺显示为绿色
                        $value['show_status'] = 2;
                    }
                    $value['is_overdue'] = 0; //未打烊
                    $value['limited'] = explode(' ', $weixinRoleData[0]['end_time'])[0]; //有效期至
                }
            }
        }
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180319
     * @desc
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function createShopQRCode($wid)
    {
        //$result = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $weixinData = $shopService->getRowById($wid);
        if (empty($weixinData)){
            return false;
        }
        //$weixinData = $result['data'];
        if ($weixinData['shop_qrcode']){
            return $weixinData['shop_qrcode'];
        }

        if ($weixinData['logo']){
            $weixinLog = public_path($weixinData['logo']);
        }else{
            $weixinLog = public_path('hsshop/image/static/huisouyun_120.png');
        }
        $qrCodePath = 'hsshop/image/qrcodes/shopqrcode/'.$wid.'_'.date('YmdHis').rand(10000,99999).'.png';
        $shopPath = config('app.url').'shop/index/'.$wid;
        $img =  QrCode::format('png')->size(400)->margin(0.5)->errorCorrection('H')->merge($weixinLog,.2,true)->encoding('UTF-8')->generate($shopPath);
        $bytes = Storage::put(
            $qrCodePath,
            $img
        );

        if ($bytes){
            //(new WeixinService())->updateData($wid,['shop_qrcode'=>$qrCodePath]);
            $shopService->update($wid,['shop_qrcode' => $qrCodePath]);
            return $qrCodePath;
        }else{
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180509
     * @desc 写入弹框信息
     */
    public function setRedisFrameInfo($wid)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $key = $this->_getFrameInfoKey($wid);
        $redisClient->SET($key,1);
        $timeOut = 86400;
        $redisClient->EXPIRE($key, $timeOut);
        return true;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $wid
     * @return string
     */
    private function _getFrameInfoKey($wid){
        return  'shop:Advertisement:info:'.$wid;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $wid
     */
    public function getRedisFrameInfo($wid){
        $redisClient = (new RedisClient())->getRedisClient();
        $res = $redisClient->get($this->_getFrameInfoKey($wid));
        if ($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据手机号创建店铺
     * @param $phone int 手机号
     * @param $admin_role_id int 权限id
     * @param $shop_valid_days int 店铺有效体验天数
     */
    public function createShop($phone, $admin_role_id, $shop_valid_days)
    {
        $return = [
            'err_code' => 0,
            'err_msg' => ''
        ];

        //客户详情
        $user = (new UserService())->init()->where(['mphone' => $phone])->getInfo();
        if (empty($user)) {
            return [
                'err_code' => 1,
                'err_msg' => '客户不存在'
            ];
        }

        //判断店铺数
        //$shop_service = new WeixinService();
        //$count = $shop_service->init()->wheres(['uid'=>$user['id']])->count();
        $shopService = new ShopService();
        $count = $shopService->getShopsCount($user['id']);
        if ($count > 0) {
            return [
                'err_code' => 1,
                'err_msg' => '超过可建立店铺数量'
            ];
        }

        //创建店铺
        $data = [
            'uid' => $user['id'],
            'shop_name' => $user['mphone'],
            'company_name' => $user['mphone'],
            'province_id' => 15,
            'city_id'     => 1213,
            'area_id'     => 2963,
        ];
        $wid = $shopService->add($data);
        (new StoreModule())->afterCreateShop($wid, $data, $user['id'], $admin_role_id, $shop_valid_days, false);

        return $return;
    }


    /**
     *  获取小程序相关二维码
     * @param $wid 店铺id
     * @param $scene 参数
     * @param $page 二维码路径
     * @author 张永辉 2018年7月2日
     */
    public function getQrCode($wid,$scene,$page,$configId)
    {
        $result = ['errCode'=>0,'errMsg'=>''];
        $bucket = 'huisoucn';
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $client = new Upyun($config);
        $path = '/wscphp/res/hsshop/qrcode/'.$wid.'/'.md5($page.$scene.$configId).'.png';
        $res = $client->has($path);
        if ($res){
            $result['data'] = config('app.source_video_url').trim($path,'/');
            return $result;
        }
        $thirdPlatform = new ThirdPlatform();
        $data = $thirdPlatform->createQrCode($wid,$scene,$page,$configId);
        if ( $data['errCode'] != 0 ) {
            $result['errCode'] = -105;
            $result['errMsg'] = '二维码获取失败:'.$data['errMsg'];
            return $result;
        }
        $img = base64_decode($data['data']);
        $res = $client->write($path, $img);
        $result['data'] = config('app.source_video_url').trim($path,'/');
        return $result;
    }

    /**
     * 检查店铺是否可以接外卖订单
     * @param $wid 店铺id
     * @param $orderData 订单数据
     * @return array
     * @author 何书哲 2018年11月15日
     */
    public function checkIfSendTakeAway($wid, $orderData) {
        $return = ['errCode'=>1, 'errMsg'=>''];

        if ($orderData['is_takeaway'] == 0) {
            $return['errMsg'] = '[外卖] 店铺id：'.$wid.' 订单: '.$orderData['id'].' 不是外卖订单';
            return $return;
        }

        if ($orderData['groups_id'] > 0 && $orderData['groups_status'] != 2) {
            $return['errMsg'] = '[外卖] 店铺id：'.$wid.' 订单: '.$orderData['id'].' 未成团';
            return $return;
        }

        $configService = new DeliveryConfigService();
        $configData = $configService->getRowByWhere(['wid'=>$wid]);
        if (empty($configData) || $configData['is_on'] == 0) {
            $return['errMsg'] = '[外卖] 店铺id：'.$wid.' 未开启外卖按钮';
            return $return;
        }

        $printerService = new DeliveryPrinterService();
        $priterData = $printerService->getRowByWhere(['wid'=>$wid, 'is_on'=>1]);
        if (empty($priterData)) {
            $return['errCode'] = 2;
            $return['errMsg'] = '[外卖] 店铺id：'.$wid.' 不存在已连接的小票打印机';
            return $return;
        }

        //处理时间问题
//        if (!$this->checkIfReceiveTakeAway($orderData['created_at'], $configData['work_days'], $configData['delivery_times'])) {
//            $return['errCode'] = 2;
//            $return['errMsg'] = '[外卖] 店铺id：'.$wid.' 没有设置匹配的外卖时间段';
//            return $return;
//        }

        $return['errCode'] = 0;
        return $return;
    }

    /**
     * 检查是否可以提交订单(针对迈外店铺)
     * @param $wid 店铺id
     * @return array
     * @author 何书哲 2018年11月22日
     */
    public function checkIfSubmitOrder($wid) {
        $return = ['errCode'=>1, 'errMsg'=>''];

        $configData = $this->getWidTakeAway($wid);
        if (empty($configData)) {
            $return['errCode'] = 0;
            return $return;
        }

        $printerService = new DeliveryPrinterService();
        $priterData = $printerService->getRowByWhere(['wid'=>$wid, 'is_on'=>1]);
        if (empty($priterData)) {
            $return['errMsg'] = '打印机未连接成功，请及时联系商家';
            return $return;
        }

        $result = (new TakeAwayModule($wid))->queryPrinterStatus();
        $result = json_decode($result, true);
        if ($result['responseCode'] != 0) {
            $return['errMsg'] = '打印机连接故障，请及时联系商家';
            return $return;
        }

        if (!$this->checkIfReceiveTakeAway(date('Y-m-d H:i:s'), $configData['work_days'], $configData['delivery_times'])) {
            $return['errMsg'] = '对不起，不在商家营业时间范围内';
            return $return;
        }

        $return['errCode'] = 0;
        return $return;
    }

    /**
     * 检查创建订单的时间是否在店铺设置的外卖时间段
     * @param $created_at 订单创建时间
     * @param $work_days 工作日
     * @param $delivery_times 外卖时间段
     * @return bool
     * @author 何书哲 2018年11月15日
     */
    public function checkIfReceiveTakeAway($created_at, $work_days, $delivery_times) {
        $days_arr = ['1'=>[1,7], '2'=>[2,1], '3'=>[3,2], '4'=>[4,3], '5'=>[5,4], '6'=>[6,5], '7'=>[7,1]];
        $created_at = strtotime($created_at);
        $work_days = explode(',', $work_days);
        $delivery_times = json_decode($delivery_times, true);
        $may_days = $days_arr[$this->_getWeekTime($created_at)];

        foreach ($may_days as $key => $may_day) {
            if (!in_array($may_day, $work_days)) {
                continue;
            }
            foreach ($delivery_times as $delivery_time) {
                $startTime = explode(' ', $delivery_time['startTime']);
                $endTime = explode(' ', $delivery_time['endTime']);
                $startTime = isset($startTime[1]) ? date('Y-m-d', ($key == 0 ? $created_at+24*3600 : $created_at)).' '.$startTime[1] : date('Y-m-d', ($key == 0 ? $created_at : $created_at-24*3600)).' '.$startTime[0];
                $endTime = isset($endTime[1]) ? date('Y-m-d', ($key == 0 ? $created_at+24*3600 : $created_at)).' '.$endTime[1] : date('Y-m-d', ($key == 0 ? $created_at : $created_at-24*3600)).' '.$endTime[0];
                $startTime = strtotime($startTime);
                $endTime = strtotime($endTime);
                if (($startTime <= $endTime && $created_at >= $startTime && $created_at <= $endTime)
                || ($startTime > $endTime && $created_at <= $startTime && $created_at >= $endTime)) {
                    return true;
                }
            }
        }

        return false;
    }



    /**
     * 时间戳返回星期 星期一到星期天返回1-7
     * @param $timestamps
     * @return integer
     * @author 何书哲 2018年11月15日
     */
    private function _getWeekTime($timestamps) {
        $day = date("w", $timestamps);
        return $day ? intval($day) : 7;
    }

    /**
     * 获取外卖店铺开启信息
     * @param $wid 店铺id
     * @return array
     * @author 何书哲 2018年11月16日
     */
    public function getWidTakeAway($wid) {
        return (new DeliveryConfigService())->getRowByWhere(['wid'=>$wid, 'is_on'=>1]);
    }

    /**
     * 获取外卖店铺配置信息
     * @param $where 条件数组
     * @return array
     * @author 何书哲 2018年11月20日
     */
    public function getDeliveryConfig($where=[]) {
        $configData = ['work_days'=>'[]', 'delivery_times'=>'[]', 'is_on'=>'0', 'unpay_min'=>'0', 'delivery_hour'=>'0'];
        $deliveryConfigData = (new DeliveryConfigService())->getRowByWhere($where);
        $deliveryConfigData && $deliveryConfigData['work_days'] = json_encode(explode(',', $deliveryConfigData['work_days']));
        return $deliveryConfigData ? $deliveryConfigData : $configData;
    }

}