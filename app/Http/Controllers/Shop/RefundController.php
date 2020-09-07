<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/10/10
 * Time: 10:17
 */

namespace App\Http\Controllers\shop;


use App\Module\RefundModule;
use App\Module\WeChatRefundModule;
use App\S\Wechat\WeChatShopConfService;
use App\S\WXXCX\WXXCXConfigService;
use App\Services\Order\OrderRefundService;

class RefundController
{

    protected function getMchKey($app_id)
    {
        $data = (new WeChatShopConfService())->getList(['app_id'=>$app_id]);
        if($data && $data[0]['mch_key']){
            return $data[0]['mch_key'];
        }
        $wxx = new WXXCXConfigService();
        $data = $wxx->getListByCondition(['app_id'=>$app_id]);
        if($data['errCode'] == 0 && $data['data']){
            $re = $data['data'][0];
            return $re['app_pay_secret'];
        }
        return false;
    }

    //解密
    // 解密步骤如下：
    //（1）对加密串A做base64解码，得到加密串B
    //（2）对商户key做md5，得到32位小写key* ( key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置 )
    //（3）用key*对加密串B做AES-256-ECB解密
    public function decrypt($encrypt,$appId)
    {
        $mch_key = $this->getMchKey($appId);
        if($mch_key){
            $keyMd5 = md5($mch_key);
            $decrypt = openssl_decrypt($encrypt,'aes-256-ecb',$keyMd5);
            return $this->xmlToArray($decrypt);
        }
        \Log::info('未获取到商户相应的商户密钥');
        return false;
    }

    private function arrayToXml( $arr ) {
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
            if ( is_numeric($val) ) {
                $xml .= "<".$key.">".$val."</".$key.">";
            } else {
                $xml .= "<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    private function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }



    /**
     *  微信退款回调
     * @update 梅杰 2018年10月19日 修改退款到账通知
     */
    public function wechatPayRefundNotify()
    {
        $postXml = isset($GLOBALS["HTTP_RAW_POST_DATA"])? $GLOBALS['HTTP_RAW_POST_DATA'] : ''; //拿到小程序回调回来的信息判断支付成功没
        if(empty($postXml)){
            $postXml = file_get_contents('php://input');
        }
        if(!empty($postXml))
        {
            $data= $this->xmlToArray($postXml);
            \Log::info($data);
            if(!empty($data['return_code']) && $data['return_code'] == 'SUCCESS')
            {
                //解密数据
                $decryptData = $this->decrypt($data['req_info'],$data['appid']);
                \Log::info($decryptData);
                if($decryptData && $decryptData['refund_status'] == 'SUCCESS'){
                    #todo订单的处理,消息模板通知发送
                    # $decryptData['out_refund_no']退款表id
                    # $decryptData['out_trade_no'] order表 id
                    //todo 是否有oid wid pid等字段返回
                    $orderID = explode('_', $decryptData['out_trade_no'])[0];
                    //拼团订单自动退款不走退款流程没有refundID 普通退款使用refundID_前缀 拼团退款使用productID_前缀 Herry
                    $outRefundTmp = explode('_', $decryptData['out_refund_no']);
                    $isNormal = 1;
                    $isGroupAutoRefund = 0;
                    $refundID = 0;
                    $productID = 0;
                    $propID = 0;
                    if ($outRefundTmp[0] == 'r') {
                        $refundID = $outRefundTmp[1];
                        $refundDetail = (new OrderRefundService())->init()->model->find($refundID);
                        if ($refundDetail) {
                            $productID = $refundDetail->pid;
                            $propID = $refundDetail->prop_id;
                        }
                    } elseif ($outRefundTmp[0] == 'p') {
                        $productID = $outRefundTmp[1];
                        $isGroupAutoRefund = 1;
                    } else {
                        $isNormal = 0;
                    }

                    if ($isNormal) {
                        $account = $decryptData['refund_recv_accout'];
                        $resultArr = (new RefundModule())->success($orderID, $refundID, $account);
                        if ($resultArr['errCode'] == 1000) {
                            //退款到账 后续回调忽略
                            $return = $this->arrayToXml(['return_code'=>'SUCCESS']);
                        } elseif ($resultArr['errCode'] > 0) {
                            \Log::info('###微信退款回调: ' . $resultArr['errMsg']);
                            $return = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[ERROR]]></return_msg></xml>";
                        } else {
                            //退款到账处理成功 发送消息模板
//                            $sendTplService = new WeChatRefundModule();
//                            $decryptData['productID'] = $productID;
//                            $decryptData['isGroupAutoRefund'] = $isGroupAutoRefund;
//                            $decryptData['propID'] = $propID;
//                            $sendTplService->sendRefundTpl($decryptData);
                            $return = $this->arrayToXml(['return_code'=>'SUCCESS']);
                        }
                    } else {
                        $return = $this->arrayToXml(['return_code'=>'SUCCESS']);
                    }
                }else{
                    $return = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[ERROR]]></return_msg></xml>";
                }
                echo $return;
            }
        }

    }



}