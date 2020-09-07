<?php

namespace App\Http\Controllers\BaiduApp;

use App\Http\Controllers\Controller;
use App\Module\BaiduApp\PaymentModule;
use Illuminate\Http\Request;
use Log;

/**
 * 百度小程序
 * @author 许立 2018年10月10日
 */
class PaymentController extends Controller
{
    /**
     * 获取百度小程序支付数据
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年10月11日
     */
    public function getPayOrderInfo(Request $request, PaymentModule $paymentModule)
    {
        // 获取订单详情
        $orderId = (int)$request->input('orderId');
        $orderId <= 0 && xcxerror('订单id不合法');

        xcxsuccess('', $paymentModule->getPayOrderInfo($orderId));
    }

    public function payNotify()
    {
        Log::info('=========百度支付回调=========');
        Log::info($_POST);
    }

    public function refundNotify(PaymentModule $paymentModule)
    {
        Log::info('=========百度退款回调=========');
        Log::info($_POST);
        $paymentModule->payNotify($_POST);
    }

    public function refundAudit()
    {
        Log::info('=========百度退款审核=========');
        Log::info($_POST);
    }
}