<?php
namespace App\Lib\AliPay;

require_once('AopSdk.php');
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/11
 * Time: 17:05
 */
class AliPayEntrance
{
    /***
     * todo 支付宝支付
     * @param int $orderId
     * @param string $subject
     * @param string $body
     * @param int $total_amount
     * @param $expire
     * @return bool|string
     * @author 张国军 2018年07月11日
     */
    public function payOrder($orderNo=0,$totalAmount=0,$subject="", $expire="30m", $body="会搜云微商城")
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>""];
        try
        {
            $aop = new \AopClient();
            $aop->gatewayUrl = config('alipay.gatewayUrl');
            $aop->appId = config('alipay.app_id');
            $aop->rsaPrivateKey = config('alipay.private_key');
            $aop->format= 'json';//固定
            $aop->apiVersion = '1.0';
            $aop->postCharset = config('alipay.charset');
            $aop->signType = config('alipay.sign_type');
            $request = new \AlipayTradePagePayRequest();
            $errMsg="";
            if(empty($orderNo))
            {
                $errMsg.="订单编号为空";
            }
            if(empty($totalAmount))
            {
                $errMsg.="金额为空";
            }
            if(empty($subject))
            {
                $errMsg.="标题为空";
            }
            if(strlen($errMsg)>0)
            {
                $returnData['errCode']=-301;
                $returnData['errMsg']=$errMsg;
                return $returnData;
            }
            $content=[];
            //$content['body']=$body;
            //支付商品描述
            $content['subject'] =$subject;
            //商户网站唯一订单号
            $content['out_trade_no'] = $orderNo;
            //该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            //注：若为空，则默认为15d。
            $content['timeout_express'] = $expire;
            //订单总金额，单位为元
            $content['total_amount'] = sprintf('%.2f',$totalAmount/100);
            //销售产品码,固定值
            $content['product_code'] = "FAST_INSTANT_TRADE_PAY";
            $bizContent = json_encode($content);//$content是biz_content的值,将之转化成json字符串
            $notifyUrl=config('app.url')."merchants/fee/aliPay/webNotify";
            $returnUrl=config('app.url')."merchants/fee/aliPay/webReturn";
            $request->setReturnUrl($returnUrl);
            $request->setNotifyUrl($notifyUrl);
            $request->setBizContent($bizContent);
            $response = $aop->pageExecute($request);
            $returnData['data']=$response;
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-300;
            $returnData['errMsg']=$ex->getMessage();
            return $returnData;
        }
    }
}