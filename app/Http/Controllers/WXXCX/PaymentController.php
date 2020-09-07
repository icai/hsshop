<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/14
 * Time: 15:23
 */

namespace App\Http\Controllers\WXXCX;
use App\Http\Controllers\Controller;
use App\Jobs\sendCodeKeyProduct;
use Illuminate\Http\Request;
use WXXCXCache;
use App\S\WXXCX\WXXCXConfigService;
use XCXPaymentModule;
use OrderService;
use CommonModule;
use MemberCardService;
use WeixinService;
use PaymentService;
use App\S\BalanceLogService;
use App\Module\PointModule;
use App\S\Member\MemberService;
use App\S\Order\OrderZitiService;
use App\S\Foundation\RegionService;
use App\S\Weixin\ShopService;

class PaymentController extends Controller
{

    /**
     * todo 待支付信息
     * @param Request $request
     * @param WXXCXConfigService $WXXCXConfigService
     * @return array
     * @author jonzhang
     * @date 2017-08-16
     * @update 张永辉 2018年7月12 通过小程序配置id来获取小程序配置信息，来处理支付
     */
    public function waitPay(Request $request,WXXCXConfigService $WXXCXConfigService)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token=$request->input('token');
        $orderNo=$request->input('order_no');
        if(empty($token))
        {
            $returnData['code']=-10;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo))
        {
            $returnData['code']=-11;
            $returnData['hint']='没有传递订单号';
            return $returnData;
        }
        //判断token是否过期
        $xcxUser=WXXCXCache::get($token,'3rd_session');
        if(!$xcxUser)
        {
            $returnData['code']=40004;
            $returnData['hint']='登录超时';
            return $returnData;
        }
        $userInfo=explode(',',$xcxUser);
        $xcxid = CommonModule::getXcxConfigIdByToken($token);

        //查询小程序支付配置信息
        $xcxConfig=$WXXCXConfigService->getRowById($xcxid);
        if($xcxConfig['errCode']!=0)
        {
            $returnData['code']=$xcxConfig['errCode'];
            $returnData['hint']=$xcxConfig['errMsg'];
            return $returnData;
        }
        else if($xcxConfig['errCode']==0&&empty($xcxConfig['data']))
        {
            $returnData['code']=-13;
            $returnData['hint']='没有配置小程序appid信息';
            return $returnData;
        }
        if(empty($xcxConfig['data']['app_id'])||empty($xcxConfig['data']['merchant_no'])||empty($xcxConfig['data']['app_pay_secret']))
        {
            $returnData['code']=-14;
            $returnData['hint']='支付配置信息不全';
            return $returnData;
        }

        /*2018.05.16拉入黑名单客户支付过滤（支付失败）add by wuxiaoping begin*/
        $mid = $userInfo[2];
        $memberInfo = (new MemberService())->getRowById($mid);
        if (isset($memberInfo['is_pull_black']) && $memberInfo['is_pull_black']) {
            $returnData['code'] = 40040;
            $returnData['hint'] = '黑名单客户';
            $returnData['list'] = $orderNo;
            return $returnData;
        }
        /*end*/

        //2018年03月14日 余额充值
        if (strpos($orderNo, 'balance') !== false) {
            $balanceLogService = new BalanceLogService();
            $order = $balanceLogService->getRowById(str_replace('balance', '', $orderNo));
            if (empty($order)) {
                $returnData['code']=-15;
                $returnData['hint']='没有查询到要支付的订单信息';
                return $returnData;
            }
            if ($order['status'] == 1) {
                $returnData['code']=-16;
                $returnData['hint']='该订单不能够进行支付';
                return $returnData;
            }

            $balanceLogService->updateDataByid($order['id'],['xcx_config_id'=>$xcxid]);

            $orderAmount = $order['money'];

            $orderInfo=['orderNo'=>$orderNo,'orderAmout'=>$orderAmount,'merchantName'=>$xcxConfig['data']['merchant_name']];
            $config  = $this->_buildConfig($xcxConfig, $userInfo[0], $orderNo, $orderAmount/100);
            $result  = XCXPaymentModule::pay($config);
            if($result['errCode']==0 && !empty($result['data']))
            {
                $returnData['list']['payConfig']=$result['data'];
                $returnData['list']['orderDetail']=$orderInfo;
                return $returnData;
            } else {
                $returnData['code']=$result['errCode'];
                $returnData['hint']=$result['errMsg'];
                return $returnData;
            }
            return;
        }
        //=================

        $orderList=OrderService::init('wid',$userInfo[1])->where([1=>1,'id'=>$orderNo])->getList(false);
        if(empty($orderList[0]['data']))
        {
            $returnData['code']=-15;
            $returnData['hint']='没有查询到要支付的订单信息';
            return $returnData;
        }
        if($orderList[0]['data'][0]['pay_way']!=0||$orderList[0]['data'][0]['status']!=0)
        {
            $returnData['code']=-16;
            $returnData['hint']='该订单不能够进行支付';
            return $returnData;
        }
        //更新订单支付时小程序配置信息
        OrderService::init('wid',$userInfo[1])->where(['id'=>$orderNo])->update(['xcx_config_id'=>$xcxid],false);
        //订单支付金额
        $orderAmount=$orderList[0]['data'][0]['pay_price'];
        //0元订单不走支付
        if($orderAmount<=0)
        {
            $orderData = $orderAmount=$orderList[0]['data'][0];
            if (XCXPaymentModule::payForFree($orderNo))
            {
                $returnData['code']=40013;
                $returnData['hint']='支付成功';
                $returnData['list'] = $orderData;
                return $returnData;
            } else
            {
                xcxerror('操作失败');
            }
        }
        //余额支付
        if ($request->input('payment') == 3) {
            if (PaymentService::pay($orderNo, 3) === true) {
                $returnData['code']=40013;
                $returnData['hint']='支付成功';
                $returnData['list'] = $orderNo;
                return $returnData;
            } else {
                xcxerror('操作失败');
            }
        }

        $orderInfo=['orderNo'=>$orderNo,'orderAmout'=>intval(strval($orderAmount*100)),'merchantName'=>$xcxConfig['data']['merchant_name']];
        $config = $this->_buildConfig($xcxConfig, $userInfo[0], $orderNo, $orderAmount);
        $result=XCXPaymentModule::pay($config);
        if($result['errCode']==0&&!empty($result['data']))
        {
            $returnData['list']['payConfig']=$result['data'];
            $returnData['list']['orderDetail']=$orderInfo;
            return $returnData;
        }
        else
        {
            $returnData['code']=$result['errCode'];
            $returnData['hint']=$result['errMsg'];
            return $returnData;
        }
    }

    /**
     * 余额支付
     * @param $xcxConfig
     * @param $openid
     * @param $orderNo
     * @param $orderAmount
     * @return mixed
     */
    private function _buildConfig($xcxConfig, $openid, $orderNo, $orderAmount)
    {
        //appid
        $config['appid']    =   $xcxConfig['data']['app_id'];
        //openid
        $config['openid']   =   $openid;
        //支付密钥
        $config['pay_key']  =   $xcxConfig['data']['app_pay_secret'];
        //商户号
        $config['mch_id']   =   $xcxConfig['data']['merchant_no'];
        //订单号
        $config['out_trade_no']=$orderNo;
        //商品描述
        $body='body';
        if(!empty($xcxConfig['data']['principal_name']))
        {
            $body=$xcxConfig['data']['principal_name'].'-商品';
        }
        $config['body']=$body;
        //订单金额 此处为分
        $config['total_fee']=intval(strval($orderAmount*100));
        //回调URL
        $config['call_back_url']=config('app.url') . 'xcx/payment/payNotify';

        //add by jonzhang 支付密钥不一致，删除原来的支付密钥 2018-05-03
        if(WXXCXCache::get($config['appid'],'xcx_pay_check'))
        {
            if(WXXCXCache::get($config['appid'],'xcx_pay_check')!=$config['pay_key'])
            {
                WXXCXCache::delete($config['appid'],'xcx_pay_check');
            }
        }
        //小程序appid对应的支付密钥
        if(!WXXCXCache::get($config['appid'],'xcx_pay_check'))
        {
            WXXCXCache::set($config['appid'], $config['pay_key'], 'xcx_pay_check');
        }
        return $config;
    }

    /**
     * todo 小程序支付回调
     */
    public function payNotify()
    {
        XCXPaymentModule::xcxPayNotify();
    }

    /**
     * todo 0元支付成功跳转页面
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-08-23
     * @update 梅杰 2018年8月9日 增加卡密商品发货
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function paySuccess(Request $request,ShopService $shopService)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $isShowPoint = 0;//默认不显示 积分
        $token=$request->input('token');
        $orderNo=$request->input('orderNo');
        if(empty($token))
        {
            $returnData['code']=-10;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo))
        {
            $returnData['code']=-11;
            $returnData['hint']='没有传递订单号';
            return $returnData;
        }
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
        if (strpos($orderNo, 'balance') !== false) {
            $balanceLogService = new BalanceLogService();
            $orderData = $balanceLogService->getRowById(str_replace('balance', '', $orderNo));
            if ($orderData['pay_way'] == 3) {
                $orderData['pay_way'] = 1;
            }
            $type = 1;
        } else {
            $orderData =OrderService::init('wid',$wid)->getInfo($orderNo);    
            $type = 2;
            $isGivePoint = (new PointModule())->isGivePoint($orderData['wid']);
            if ($isGivePoint['data'] == 1) {
                $isShowPoint = 1;
            }
            //add by wuxiaoping 2018.06.06 自提订单返回自提信息
            if (isset($orderData['is_hexiao']) && $orderData['is_hexiao'] == 1) {
                $shopUrl = 'pages/main/pages/order/hexiaoConfirm/hexiaoConfirm?oid='.$orderData['id'];
                $qrcodeData = CommonModule::qrCode($wid, $shopUrl,1);
                $orderData['qrcode'] = $qrcodeData;
                $where['wid'] = $wid;
                $where['oid'] = $orderNo;
                $where['mid'] = $mid;
                $zitiData = (new OrderZitiService())->getDataByCondition($where);
                if ($zitiData) {
                    $temp = [$zitiData['orderZiti']['province_id'],$zitiData['orderZiti']['city_id'],$zitiData['orderZiti']['area_id']];
                    $regionService = new RegionService();
                    $region = $regionService->getListByIdWithoutDel($temp);

                    $tmpAddr = [];
                    foreach ($region as $val){
                        $tmpAddr[$val['id']] = $val['title'];
                    }
                    $zitiData['orderZiti']['province_title'] = $tmpAddr[$zitiData['orderZiti']['province_id']];
                    $zitiData['orderZiti']['city_title']     = $tmpAddr[$zitiData['orderZiti']['city_id']];
                    $zitiData['orderZiti']['area_title']     = $tmpAddr[$zitiData['orderZiti']['area_id']];
                    $orderData['ziti'] = $zitiData;
                }
                
            }
        }
        if ($orderData['status'] == 1 && $orderData['type'] == 12) {
            dispatch((new sendCodeKeyProduct( $orderData['wid'],$orderData['mid'],$orderData['id']))->onQueue('sendCdKey'));
        }

        //$shop = WeixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $shop = $shopService->getRowById($wid);
        #todo  发送支付成功消息提醒

        $returnData['list']=[
            'title' => '支付成功',
            'order' => $orderData,
            'adver_logo'      => $shop['adver_logo'],
            'type' => $type,
            'isShowPoint' => $isShowPoint
        ];
        return $returnData;
    }

    /**
     * todo 0元支付失败跳转页面
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-08-23
     */
    public function payFail(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token=$request->input('token');
        $orderNo=$request->input('orderNo');
        if(empty($token))
        {
            $returnData['code']=-10;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo))
        {
            $returnData['code']=-11;
            $returnData['hint']='没有传递订单号';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $wid=CommonModule::getWidByToken($token);
        if(empty($wid))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        if (strpos($orderNo, 'balance') !== false) {
            $balanceLogService = new BalanceLogService();
            $orderData = $balanceLogService->getRowById(str_replace('balance', '', $orderNo));
            if ($orderData['pay_way'] == 3) {
                $orderData['pay_way'] = 1;
            }
            $type = 1;
        } else {
            $orderData = OrderService::init('wid',$wid)->getInfo($orderNo);
            $type = 2;
        }
        
        $returnData['list']=[
            'title' => '支付失败',
            'order' => $orderData,
            'type' => $type
        ];
        return $returnData;
    }
}