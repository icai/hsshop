<?php

namespace App\Module\BaiduApp;

use OrderService;
use Log;
use PaymentService;

/**
 * 百度支付
 * @author 许立 2018年10月10日
 */
class PaymentModule
{
    /**
     * 获取百度小程序支付数据
     * @param int $orderId 订单id
     * @return array
     * @author 许立 2018年10月11日
     */
    public function getPayOrderInfo($orderId)
    {
        // 订单详情
        $order = OrderService::init()->model->find($orderId);
        $order || xcxerror('订单不存在');
        $order = $order->toArray();

        // 跳转百度收银台支付必带参数之一，是百度收银台的财务结算凭证，与账号绑定的结算协议一一对应，每笔交易将结算到dealId对应的协议主体。
        $dealId = '23423423';

        // 百度电商开放平台appKey,用以表示应用身份的唯一ID，在应用审核通过后进行分配，一经分配后不会发生更改，来唯一确定一个应用。
        $appKey = 'dfdfKK';

        // 支付金额 单位：分
        $totalAmount = intval(strval($order['pay_price'] * 100));

        // 商户平台自己记录的订单ID，当支付状态发生变化时，会通过此订单ID通知商户。
        $tpOrderId = $order['id'];

        // 订单名称
        $dealTitle = '百度小程序支付订单';

        // 对appKey+dealId+tpOrderId进行RSA加密后的密文，防止订单被伪造。
        //$rsaSign = $this->_getRsaSign($dealId, $appKey, $tpOrderId);
        $rsaSign = 'xxxxxtestxxx';

        // 订单详细信息，需要是一个可解析为JSON Object的字符串。
        $bizInfo = [
            'tpData' => [
                'appKey' => $appKey,
                'dealId' => $dealId,
                'tpOrderId' => $tpOrderId,
                'rsaSign' => $rsaSign,
                'totalAmount' => $totalAmount,
                'payResultUrl' => '', // 支付结果页跳转地址。支付完成（成功或失败）后，会跳转至该url @todo 是否需要
                'returnData' => '' // 支付完成（成功或失败）后，需要原样回传给支付结果页的参数 @todo 是否需要
            ]
        ];
        $bizInfo = json_encode($bizInfo);

        // 返回数据组装
        $return = [
            'dealId' => $dealId,
            'appKey' => $appKey,
            'totalAmount' => $totalAmount,
            'tpOrderId' => $tpOrderId,
            'dealTitle' => $dealTitle,
            'rsaSign' => $rsaSign,
            'bizInfo' => $bizInfo
        ];

        return $return;
    }

    /**
     * 百度支付回调处理
     * @param array $postDataArray 百度服务器post的数据
     * @author 许立 2018年10月12日
     */
    public function payNotify($postDataArray)
    {
        // 接收参数
        if (!$postDataArray) {
            Log::info('[百度支付回调]未接收到任何参数');
            return;
        }

        // 订单详情
        $order = OrderService::init()->model->find($postDataArray['tpOrderId']);
        if (!$order) {
            Log::info('[百度支付回调]订单不存在');
            return;
        }

        // 校验签名
        // todo 获取配置表里的公钥字段
        $rsaPublicKeyStr = '';
        if (!NuomiRsaSign::checkSignWithRsa($postDataArray, $rsaPublicKeyStr)) {
            Log::info('[百度支付回调]校验签名失败');
            return;
        }

        // todo 将回调信息写入数据库

        // 调用支付成功后续操作
        PaymentService::paySuccessSubsequent($postDataArray['tpOrderId'], 2000, $postDataArray);
    }

    /**
     * 对appKey+dealId+tpOrderId进行RSA加密后的密文
     * @param string $dealId 交易id
     * @param string $appKey 第三方应用在百度电商开放平台的唯一标识
     * @param string $tpOrderId 第三方订单ID
     * @return string
     * @author 许立 2018年10月12日
     */
    private function _getRsaSign($dealId, $appKey, $tpOrderId)
    {
        // 第一部分：从公私钥文件路径中读取出公私钥文件内容

        // 第二部分：生成签名
        $requestParamsArr = array(
            'dealId' => $dealId,
            'appKey' => $appKey,
            'tpOrderId' => $tpOrderId
        );
        $rsaSign = NuomiRsaSign::genSignWithRsa($requestParamsArr, NuomiRsaSign::RSA_PRIVATE_KEY);

        // todo 第三部分：校验签名 DEMO 这步是否必须？
        /*$requestParamsArr['sign'] = $rsaSign;
        $checkSignRes = NuomiRsaSign::checkSignWithRsa($requestParamsArr, $rsaPublicKeyStr);
        print_r($checkSignRes); # true :签名校验成功，false：签名校验失败*/

        return $rsaSign;
    }
}