<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Weixin;
use App\Module\AliApp\AliClientModule;
use App\Module\DistributeModule;
use App\Module\MeetingGroupsRuleModule;
use App\S\Wechat\WeChatShopConfService;
use App\Module\WeChatAuthModule;
use App\Services\Order\OrderDetailService;
use Illuminate\Http\Request;
use Log;
use MemberCardService;
use OrderService;
use PaymentService;
use App\S\BalanceLogService;
use MemberService;
use App\S\PublicShareService;
use App\Module\PointModule;
use App\S\Order\OrderZitiService;
use App\S\Foundation\RegionService;
use WechatMessageService;
use App\Module\CommonModule;

/**
 * 支付
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月30日 15:10:06
 */
class PaymentController extends Controller
{
    /**
     * 订单支付
     * 
     * @param  integer $wid     [店铺id]
     * @param  integer $id      [订单id]
     * @param  integer $payment [支付方式：0未支付；1微信支付；2支付宝支付；3储值余额支付；4货到付款/到店付款；5找人代付；6领取赠品；7优惠兑换；8银行卡支付；9会员卡支付]
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->input('special','') ==  'groups'){
            (new MeetingGroupsRuleModule())->wechatPay($request->input('id'));
            exit();
        }
        $input = $request->input();
        /*用户拉黑支付直接跳转到失败页面 add by wuxiaoping 2018.05.15 begin*/
        $mid = session('mid');
        $memberInfo = MemberService::getRowById($mid);
        if (isset($memberInfo['is_pull_black']) && $memberInfo['is_pull_black']) {
            return redirect('/shop/pay/payFail/' . $input['id']);
        }
        /*end*/
        $wechatAuthModule = new WeChatAuthModule();
        $conf = $wechatAuthModule->getConf(session('wid'));
        if ((!config('wechat.is_open_deposit') && $conf['type'] == 2) || $conf['status'] != 1){
            if ($request->input('reqFrom') == 'wechat'){
                error('店铺未配置或开启微信支付');
            }
        }
        $result = $wechatAuthModule->isAuth(session('wid'),session('umid'));
        if ($result['success']){
            $shopAuth = $wechatAuthModule->shopAuth($result['data'],session('wid'));
            if ($shopAuth){
                return $shopAuth;
            }
        }

        //余额充值支付
        if (strpos($input['id'], 'balance') !== false) {
            $input['id'] = str_replace('balance', '', $input['id']);
            PaymentService::payBalance($input['id']);
            return;
        }

        //获取订单
        $order = OrderService::init('wid',session('wid'))->getInfo($input['id']);
        if (empty($order)) {
            return myerror('订单不存在');
        }

        //支付
        if ($order['pay_price'] <= 0) {
            if (PaymentService::payForFree($input['id'])) {
                return redirect('/shop/pay/paySuccess/' . $input['id']);
            } else {
                return redirect('/shop/pay/payFail/' . $input['id']);
            }
        } else {
            if ($input['payment'] == 3) {
                if (PaymentService::pay($input['id'], $input['payment']) === true) {
                    return redirect('/shop/pay/paySuccess/' . $input['id']);
                } else {
                    return redirect('/shop/pay/payFail/' . $input['id']);
                }
            }
            PaymentService::pay($input['id'], $input['payment']);
        }
    }

    /**
     * 支付成功页面
     * 
     * @return view
     */
    public function paySuccess($id)
    {
        $result['data'] = [];
        $isShowPoint = 0;//默认不显示
        if (strpos($id, 'balance') !== false) {
            $balanceLogService = new BalanceLogService();
            $orderData = $balanceLogService->getRowById(str_replace('balance', '', $id));
            if ($orderData['pay_way'] == 3) {
                $orderData['pay_way'] = 1;
            }
            $type = 1;
        } else {
            $orderData = Order::find($id)->load('orderDetail')->toArray();
//            $result = MemberCardService::grant(session('mid'),session('wid'));
            if ($orderData['groups_id'] != 0){
                //获取订单详情
                $orderDetailData = (new OrderDetailService())->init()->model->where('oid',$orderData['id'])->get(['id','product_id'])->toArray();
                if ($orderDetailData[0]['product_id'] == 2845){
                    //return redirect('/shop/grouppurchase/ceoInfo?groups_id='.$orderData['groups_id']);
                    $url = config('app.url').'shop/grouppurchase/ceoInfo?groups_id='.$orderData['groups_id'];
                    echo "<script>window.location.href='".$url."'</script>";
                    exit();
                }
                echo "<script>window.location.href='".config('app.url').'/shop/grouppurchase/groupon/'.$orderData['groups_id'].'/'.session('wid').'?group_type=1'."'</script>";
                exit();
            }

            /*自提订单支付成功显示自提信息*/
            if ($orderData['is_hexiao'] == 1) {
                // 生成核销二维码
                $commonModule = new CommonModule(); 
                $shopUrl = config('app.url') . 'shop/order/hexiaoConfirm?wid='.$orderData['wid'].'&oid='.$orderData['id'];
                $url = $commonModule->qrCode($orderData['wid'], $shopUrl);
                $orderData['qrcode'] = $url;

                $where['oid'] = $orderData['id'];
                $where['wid'] = session('wid');
                $where['mid'] = session('mid');
                $zitiData = (new OrderZitiService())->getDataByCondition($where);
                if ($zitiData) {
                    $temp = [$zitiData['orderZiti']['province_id'],$zitiData['orderZiti']['city_id'],$zitiData['orderZiti']['area_id']];
                    $regionService = new RegionService();
                    $region = $regionService->getListById($temp);

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
            $type = 2;
            $isGivePoint = (new PointModule())->isGivePoint($orderData['wid']);
            if ($isGivePoint['data'] == 1) {
                $isShowPoint = 1;
            }
        }

        return view('shop.payment.paySuccess', [
            'title'       => '支付成功',
            'order'       => $orderData,
//            'card'        => $result['data'],
            'type'        => $type,
            'isShowPoint' => $isShowPoint,
            'shareData'   => (new PublicShareService())->publicShareSet(session('wid')),
        ]);
    }

    /**
     * 支付失败页面
     * 
     * @return view
     */
    public function payFail($id)
    {
        $orderData = [];
        if (strpos($id, 'balance') !== false) {
            $balanceLogService = new BalanceLogService();
            $orderData = $balanceLogService->getRowById(str_replace('balance', '', $id));
            if ($orderData['pay_way'] == 3) {
                $orderData['pay_way'] = 1;
            }
            $type = 1;
        } else {
            $orderData = OrderService::init('wid',session('wid'))->getInfo($id);
            $type = 2;
        }

        return view('shop.payment.payFail', [
            'title' => '支付失败',
            'order' => $orderData,
            'type'  => $type
        ]);
    }

    /**
     * [wechatPayNotify 微信支付异步回调]
     * @return void
     * @update 张永辉 2018年7月6日 微信回调签名验证码以及修复XML解析存在的安全问题
     * @update 许立 2018年12月10日 异常回调数据记录日志
     */
    public function wechatPayNotify()
    {
        $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : ''; //拿到微信回调回来的信息判断支付成功没  
  
        if(empty($streamData)){  
            $streamData = file_get_contents('php://input');  
        }  
        if($streamData!=''){  

            try {
                $exception = false;
                $streamData = PaymentService::xmlToArray($streamData);
            } catch (\Exception $e) {
                $exception = true;
                Log::info('微信支付回调数据异常: ' . $e->getMessage());
            }
            if (!isset($streamData['attach']) || !isset($streamData['return_code']) || !isset($streamData['result_code'])) {
                $exception = true;
                Log::info('微信支付回调数据字段异常');
            }
            if ($exception) {
                echo 'fail';
                exit;
            }

            $attach = explode('#',$streamData['attach']);
            $orderIds = explode(',',$attach[0]);
            if($streamData['return_code'] == 'SUCCESS' && $streamData['result_code'] == 'SUCCESS'){ //支付成功  
                try {  
                    if (strpos($orderIds[0], 'balance') !== false) {
                        $balanceId = str_replace('balance', '', $orderIds[0]);
                        $res = PaymentService::checkSign($streamData,$balanceId,$type='balance');
                        if (!$res){
                            \Log::info('会员卡充值回调验证失败');
                            echo 'fail';
                            exit();
                        }
                        PaymentService::balanceSuccessSubsequent($balanceId, 3, $streamData);
                    } else {
                        //支付成功，执行订单操作
                        $res = PaymentService::checkSign($streamData,$orderIds[0],$type='order');
                        if (!$res){
                            \Log::info('订单支付回调验证失败');
                            echo 'fail';
                            exit();
                        }
                        PaymentService::paySuccessSubsequent($orderIds, 1, $streamData);
                    }

                    echo 'success';
  
                } catch (\Exception $e) {  
                    //支付失败，返回日志记录 
                    Log::info($e->getMessage());
                    echo 'fail';           
                }  
                  
            }  
        }else{  
            echo 'fail';
        }  
    }

    /**
     * 充值成功页面
     * 
     * @return view
     */
    public function rechargeSuccess()
    {
        return view('shop.payment.rechargeSuccess', [
            'title' => '充值成功',
        ]);
    }

    /**
     * 充值失败页面
     * 
     * @return view
     */
    public function rechargeFail()
    {
        return view('shop.payment.rechargeFail', [
            'title' => '充值失败',
        ]);
    }



}
