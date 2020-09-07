<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/12
 * Time: 18:45
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Module\XCXPaymentModule;
use App\Module\PayModule;
use App\Lib\BLogger;

class WechatPayController extends Controller
{
    /**
     * todo 微信扫描支付
     * @param Request $request
     * @param XCXPaymentModule $xcxPaymentModule
     * @return array
     * @author 张国军 2018年07月12日
     */
    public function waitPay(Request $request,XCXPaymentModule $xcxPaymentModule)
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>""];
        $orderId=$request->input('orderId');
        $orderId=intval($orderId);
        if(empty($orderId))
        {
            $returnData["errCode"]=-1001;
            $returnData["errMsg"]="orderId为空";
            return $returnData;
        }
        return $xcxPaymentModule->processNativeData($orderId);
    }

    /*
     * todo 微信扫码异步支付结果
     * @author 张国军 2018年07月12日
     */
    public function webNotify(PayModule $payModule)
    {
        $payModule->wechatWebNotify();
    }
}