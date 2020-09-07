<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/7/26
 * Time: 9:30
 */

namespace App\Http\Controllers\AliApp;


use App\Http\Controllers\Controller;
use App\Module\AliApp\AliClientModule;
use App\Module\AliApp\AlipayTradeAppPayRequest;
use App\S\AliApp\AliappConfigService;
use App\S\AliApp\AliappNotifyService;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use PaymentService;
use WeixinService;
use Validator;
use DB;
use App\S\Weixin\ShopService;

class PaymentController extends Controller
{

    /**
     * 支付宝下单获取支付信息接口
     * @param Request $request 请求参数
     * @param OrderService $orderService 订单service
     * @author 张永辉 2018年7月26日
     */
    public function getPayOrderInfo(Request $request,OrderService $orderService)
    {
        $input = $request->input();
        $rule = Array(
            'id'          => 'required|integer',
        );
        $message = Array(
            'id.required'     => '订单id不能为空',
            'id.integer'     => '订单id必须是整数',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            xcxerror($validator->errors()->first());
        }
        $orderData = $orderService->init()->model->find($input['id']);
        if (!$orderData){
            xcxerror('订单不存在');
        }
        $orderData = $orderData->toArray();
        $notify_url = config('app.url').'aliapp/payment/aliPayNotify';
        $requestArr = array(
            'body' => '支付宝小程序',
            'subject' => '支付宝小程序订单支付',
            'out_trade_no' => $input['id'],
            'timeout_express' => '30m',
            'total_amount' => floatval($orderData['pay_price']),
            'product_code' => 'QUICK_MSECURITY_PAY'
        );
        //设置支付宝request信息
        $aliClientModule = new AliClientModule();
        //获取支付宝小程序配置id
        $aliappConfigId = session('aliappConfigId');
        if (is_null($aliappConfigId)) {
            xcxerror('支付宝小程序配置id不存在');
        }
        $aliappConfigData = (new AliappConfigService())->getRowById($aliappConfigId);
        if (!$aliappConfigData) {
            xcxerror('支付宝小程序配置信息不存在');
        }
        //设置appid和公钥
        !empty($aliappConfigData['auth_app_id']) && ($aliClientModule->appId = $aliappConfigData['auth_app_id']);
        !empty($aliappConfigData['ali_rsa_pub_key']) && ($aliClientModule->alipayrsaPublicKey = $aliappConfigData['ali_rsa_pub_key']);
        $requestParam = new AlipayTradeAppPayRequest();
        $requestParam->setNotifyUrl($notify_url);
        $requestParam->setBizContent(json_encode($requestArr));
        $response = $aliClientModule->sdkExecute($requestParam);
        //获取店铺信息
        //$storeData = WeixinService::init('id', $orderData['wid'])->where(['id'=>$orderData['wid']])->getInfo();
        $storeData = (new ShopService())->getRowById($orderData['wid']);
        $data = [
            'oid'       => $orderData['oid'],
            'payPrice'  => sprintf('%.2f', $orderData['pay_price']),
            'shopName'  => $storeData ? $storeData['shop_name'] : '',
            'orderStr'  => $response
        ];
        xcxsuccess('', $data);
    }

    /**
     * 支付宝支付回调
     * @param OrderService $orderService 订单service
     * @return null
     * @author 何书哲 2018年7月26日
     */
    public function aliPayNotify(OrderService $orderService) {
        $post = $_POST;
        $aliClientModule = new AliClientModule();
        //如果未接收到参数，写入日志
        if (!$post) {
            \Log::info('支付宝订单支付失败，回调未接收到任何参数');
            return;
        }
        //检查订单是否存在
        if (!$orderData = $orderService->init()->model->find($post['out_trade_no'])) {
            \Log::info('支付宝订单支付失败，订单不存在');
            \Log::info($post);
            return;
        }
        //如果交易状态不是TRADE_SUCCESS，则return
        if (empty($post['out_trade_no']) || $post['trade_status'] != 'TRADE_SUCCESS') {
            \Log::info('支付宝订单支付失败，trade_status值不是TRADE_SUCCESS');
            \Log::info($post);
            return;
        }
        //签名检验，设置公钥
        $aliappConfigData = (new AliappConfigService())->getRowByAppId($post['app_id']);
        !empty($aliappConfigData['ali_rsa_pub_key']) && ($aliClientModule->alipayrsaPublicKey = $aliappConfigData['ali_rsa_pub_key']);
        if (!$aliClientModule->rsaCheckV1($post, null, 'RSA2')) {
            \Log::info('支付宝订单支付失败，签名校验失败');
            \Log::info($post);
            return;
        }
        //将回调信息写入数据表
        $aliNotifyData = [
            'out_trade_no'   => $post['out_trade_no'] ?? '',
            'trade_no'       => $post['trade_no'] ?? '',
            'app_id'         => $post['app_id'] ?? '',
            'buyer_logon_id' => $post['buyer_logon_id'] ?? '',
            'seller_email'   => $post['seller_email'] ?? '',
            'total_amount'   => $post['total_amount'] ?? 0.00,
            'notify_json'    => json_encode($post),
            'notify_time'    => $post['notify_time'] ?? ''
        ];
        (new AliappNotifyService())->add($aliNotifyData);
        //调用支付成功回调
        PaymentService::paySuccessSubsequent($post['out_trade_no'], 2, $post);
    }

}