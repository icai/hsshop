<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/11
 * Time: 19:59
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Lib\BLogger;
use App\Module\PayModule;
use App\Lib\AliPay\AliPayEntrance;
use App\S\Fee\SelfOrderService;
use App\S\Fee\SelfOrderDetailService;

class AliPayController extends  Controller
{
    /***
     * 去支付
     * @param Request $request
     * @param SelfOrderService $selfOrderService
     * @return array
     * @author 张国军 2018年07月12日
     */
    public function waitPay(Request $request,SelfOrderService $selfOrderService,SelfOrderDetailService $selfOrderDetailService)
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
        $selfOrderData=$selfOrderService->getListByCondition(['id'=>$orderId,'current_status'=>0]);
        if($selfOrderData['errCode']==0&&empty($selfOrderData['data']))
        {
            $returnData["errCode"]=-1002;
            $returnData["errMsg"]="订单不存在";
            return $returnData;
        }
        else if($selfOrderData['errCode']<0)
        {
            return $selfOrderData;
        }

        if(isset($selfOrderData['data'][0]['status'])&&$selfOrderData['data'][0]['status']!=0)
        {
            $returnData["errCode"]=-1003;
            $returnData["errMsg"]="该订单不是待支付订单，不能够进行支付";
            return $returnData;
        }
        $payAmount=$selfOrderData['data'][0]['pay_amount']??0;
        if(empty($payAmount))
        {
            $returnData["errCode"]=-1004;
            $returnData["errMsg"]="订单金额为0";
            return $returnData;
        }
        $orderNo=$selfOrderData['data'][0]['order_no']??0;
        if(empty($orderNo))
        {
            $returnData["errCode"]=-1004;
            $returnData["errMsg"]="订单号为空";
            return $returnData;
        }
        $selfOrderDetailData=$selfOrderDetailService->getListByCondition(['self_order_id'=>$orderId]);
        $title="-";
        if($selfOrderDetailData['errCode']==0&&!empty($selfOrderDetailData['data']))
        {
            $title=$selfOrderDetailData['data'][0]['product_name']??'-';
            if(isset($selfOrderDetailData['data'][0]['product_version_no'])&&$selfOrderDetailData['data'][0]['product_version_no']==1)
            {
                $title.="(基础版)";
            }
            else if(isset($selfOrderDetailData['data'][0]['product_version_no'])&&$selfOrderDetailData['data'][0]['product_version_no']==2)
            {
                $title.="(高级版)";
            }
            else if(isset($selfOrderDetailData['data'][0]['product_version_no'])&&$selfOrderDetailData['data'][0]['product_version_no']==3)
            {
                $title.="(至尊版)";
            }
        }
        return (new AliPayEntrance())->payOrder($orderNo,$payAmount,$title);
    }
    /*
     * todo 支付宝异步支付结果
     * @author 张国军 2018年07月12日
     */
    public function webNotify(PayModule $payModule)
    {
        try
        {
            $payModule->aliPayWebNotify();
        }
        catch(\Exception $ex)
        {
            BLogger::getLogger('error')->error('支付宝支付结果异步通知出现错误,'.$ex->getMessage());
        }
    }
    /*
     * todo 支付宝同步支付结果
     * @author 张国军 2018年07月12日
     */
    public function webReturn(Request $request,SelfOrderService $selfOrderService)
    {
        $orderNo=$request->input('out_trade_no');
        $orderData=$selfOrderService->getListByCondition(['order_no'=>$orderNo]);
        BLogger::getLogger('info')->info('webReturn',$orderData);
        if($orderData['errCode']==0&&!empty($orderData['data']))
        {
            $orderId=$orderData['data'][0]['id'];
            $url='/merchants/capital/fee/order/pay/finish?state=1&oId=' . $orderId;
            //BLogger::getLogger('info')->info('webReturn url'.$url);
            return redirect($url);
        }
        else
        {
            echo "failure";
        }
    }
}