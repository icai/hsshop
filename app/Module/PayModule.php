<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/11
 * Time: 16:38
 */

namespace App\Module;
use App\Lib\BLogger;
use App\S\Fee\SelfOrderDetailService;
use App\S\Fee\SelfOrderService;
use App\S\Fee\SelfPayLogService;
use App\Services\WeixinService;
use WXXCXCache;
use App\Services\Permission\WeixinRoleService;
use App\S\Fee\SelfFeeOperateLogService;
use App\S\Weixin\ShopService;

//支付验证使用到
require_once(dirname(dirname ( __FILE__ )).'/Lib/AliPay/AopSdk.php');

class PayModule
{
    /**
     * todo 支付宝支付结果异步通知
     * @author 张国军 2018年07月12日
     *
     */
    public function aliPayWebNotify()
    {
        $responseData= isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        if (empty($responseData))
        {
            //接收回调数据
            $responseData= file_get_contents('php://input');
            //BLogger::getLogger('info')->info('支付宝支付回调信息:',$responseData);
        }
        try
        {
            if ($responseData)
            {
                //把url里的参数解析到数组
                parse_str($responseData,$responseDataArr);
                BLogger::getLogger('info')->info('支付宝回调数据:',$responseDataArr);
                if($this->check($responseDataArr))
                {
                    if (isset($responseDataArr['trade_status']))
                    {
                        switch ($responseDataArr['trade_status'])
                        {
                            case 'TRADE_SUCCESS':
                                if ($this->processOrderForAli($responseDataArr))
                                {
                                    echo "success";
                                }
                                break;
                        }
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            BLogger::getLogger('error')->error("支付宝支付出现异常：".$ex->getMessage());
        }
    }

    /***
     * todo 检查支付宝回调数据是否正确
     * @param $arr
     * @return bool
     * @author 张国军 2018年07月19日
     */
    function check($arr)
    {
        $aop = new \AopClient();
        $publicKey=config('alipay.public_key');
        $aop->alipayrsaPublicKey = $publicKey;
        $signType=config('alipay.sign_type');
        $result = $aop->rsaCheckV1($arr,$publicKey,$signType);
        return $result;
    }

    /**
     *todo 更改订单状态
     * @param array $data
     * @return int
     * @author 张国军 2018年07月12日
     */
    private function processOrderForAli($data=[])
    {
        $result=false;
        if(!empty($data['trade_no'])&&!empty($data['out_trade_no']))
        {
            $updateData['transaction_no']=$data['trade_no'];
            //1表示支付成功
            $updateData['status']=1;
            //2表示支付宝支付方式
            $updateData['pay_way']=2;
            $orderData=(new SelfOrderService())->getListByCondition(['order_no'=>$data['out_trade_no'],'current_status'=>0]);
            if($orderData['errCode']==0&&!empty($orderData['data']))
            {
                $id = $orderData['data'][0]['id'];
                if (!empty($id))
                {
                    //更改订单状态
                    $returnOrderData = (new SelfOrderService())->updateData($id, $updateData);
                    if ($returnOrderData['errCode'] == 0) {
                        $this->updateStoreExpireTime($id);
                    }
                    //记录支付信息
                    (new SelfPayLogService())->insertData(["order_id" => $id, "trade_no" => $data['trade_no'],"sign"=>$data['sign'], "type" => 2, "return_data" => json_encode($data)]);
                    $result=true;
                }
            }

        }
        else
        {
            if(!empty($data))
            BLogger::getLogger('error')->error('支付完成更改订单状态:'.json_encode($data));
        }
        return $result;
    }

    /**
     * todo 支付宝支付结果异步通知
     * @author 张国军 2018年07月12日
     *
     */
    public function wechatWebNotify()
    {
        $postXml= isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        if (empty($postXml))
        {
            //接收回调数据
            $postXml= file_get_contents('php://input');
        }
        try
        {
            if($postXml)
            {
                $data=(new XCXPaymentModule())->xmlToArray($postXml);
                //$data['return_code'] == 'SUCCESS'表示签名成功
                BLogger::getLogger('info')->info('微信扫码支付回调信息:',$data);
                $result=$this->processOrderForWechat($data);
                // 返回状态给微信服务器
                if ($result) {
                    $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                } else {
                    $str = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
                }
                echo $str;
            }
        }
        catch(\Exception $ex)
        {
            BLogger::getLogger('error')->error("微信扫描支付出现异常：".$ex->getMessage());
        }
    }

    /***
     * todo 微信支付成功，更改订单信息
     * @param array $data
     * @return bool
     * @author 张国军 2018年07月13日
     */
    private function processOrderForWechat($data=[])
    {
        $result=false;
        if(isset($data['return_code'])&&$data['return_code'] == 'SUCCESS')
        {
            $orderNo = explode('_', $data['out_trade_no']);
            $orderData=(new SelfOrderService())->getListByCondition(['order_no'=>$orderNo[0],'current_status'=>0]);
            BLogger::getLogger('info')->info("微信扫码支付查询订单信息：",$orderData);
            if($orderData['errCode']==0&&!empty($orderData['data']))
            {
                //获取服务器返回的数据
                $payLog["order_id"] = $orderData['data'][0]['id'];//订单单号
                $payLog["trade_no"] = $data['transaction_id'];//微信支付流水号
                $payLog["sign"] = $data['sign'];//sign
                $payLog["type"] = 1;//微信支付
                $payLog["return_data"] = json_encode($data);//回调数据
                (new SelfPayLogService())->insertData($payLog);
                BLogger::getLogger('info')->info("微信扫码支付支付日志：",$payLog);
            }
        }
        // 判断签名是否正确  判断支付状态
        if ((isset($data['return_code'])&&$data['return_code'] == 'SUCCESS') && (isset($data['result_code'])&&$data['result_code'] == 'SUCCESS'))
        {
            BLogger::getLogger('info')->info("微信扫码支付支付成功",$data);
            $orderNo = explode('_', $data['out_trade_no']);
            //保存微信服务器返回的签名sign
            $data_sign = $data['sign'];
            //sign不参与签名算法
            unset($data['sign']);
            //获取appid对应的支付key
            $payKey=WXXCXCache::get($data['appid'],'native_pay_check');
            //如果缓存中的支付密钥不存在，则从配置中缓存
            if(empty($payKey))
            {
                $payKey=config('wechat.native_pay_key');
            }
            $sign = (new XCXPaymentModule())->getSign($data,$payKey);
            //验证签名是否相等
            if($data_sign==$sign)
            {
                $updateData['transaction_no']=$data['transaction_id'];
                //1表示支付成功
                $updateData['status']=1;
                //1表示微信支付方式
                $updateData['pay_way']=1;
                //更改订单状态
                $orderData=(new SelfOrderService())->getListByCondition(['order_no'=>$orderNo[0],'current_status'=>0]);
                if($orderData['errCode']==0&&!empty($orderData['data']))
                {
                    $id=$orderData['data'][0]['id'];
                    if(!empty($id))
                    {
                        $returnOrderData = (new SelfOrderService())->updateData($id, $updateData);
                        BLogger::getLogger('info')->info("微信扫码支付支付成功,更改订单状态",$returnOrderData);
                        if ($returnOrderData['errCode'] == 0)
                        {
                            if($this->updateStoreExpireTime($id))
                            {
                                $result = true;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /***
     * todo 更改店铺的过期时间
     * @param $orderId
     * @author 张国军 2018年07月13日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function  updateStoreExpireTime($orderId=0)
    {
        $expireTimeStatus=false;
        $orderData=(new SelfOrderService())->getListByCondition(['id'=>$orderId,'current_status'=>0]);
        BLogger::getLogger('info')->info("updateStoreExpireTime",$orderData);
        if($orderData['errCode']==0&&!empty($orderData['data']))
        {
            $wid=$orderData['data'][0]['wid'];
            $storeData=(new WeixinRoleService())->getListByCondition(['wid'=>$wid]);
            //BLogger::getLogger('info')->info("WeixinRoleService",$storeData);
            if($storeData['errCode']==0&&!empty($storeData['data']))
            {
                $endTime=$storeData['data'][0]['end_time'];
                $id=$storeData['data'][0]['id'];
                $wid=$storeData['data'][0]['wid'];
                $expireTime=date("Y-m-d",strtotime($endTime));
                $expireTime=strtotime($expireTime);
                $time=date("Y-m-d");
                $time=strtotime($time);
                //店铺还未过期
                $storeExpireTime=date("Y-m-d H:i:s",strtotime("+1 year",strtotime($endTime)));
                //店铺已经过期
                if($time>$expireTime)
                {
                    $storeExpireTime=date("Y-m-d H:i:s",strtotime("+1 year",time()));
                }//店铺今天过期
                else if($time==$expireTime)
                {
                    $storeExpireTime=date("Y-m-d",strtotime("+1 year",time()))." 23:59:59";
                }
                //更改店铺权限
                $admin_role_id=0;
                $orderDetailData = (new SelfOrderDetailService())->getListByCondition(['self_order_id'=>$orderId]);
                //BLogger::getLogger('info')->info("SelfOrderDetailService",$orderDetailData);
                if($orderDetailData['errCode']==0&&!empty($orderDetailData['data']))
                {
                    $product_version_no=$orderDetailData['data'][0]['product_version_no'];
                    switch ($product_version_no){
                        case 1:
                        case 2:
                        case 3:
                            $admin_role_id=$product_version_no;
                            break;
                        default:
                    }
                }
                //更改店铺的过期时间
                $weixinRoleData=(new WeixinRoleService())->updateData($id,['end_time'=>$storeExpireTime,'pay_fee_time'=>date('Y-m-d H:i:s',time()),'admin_role_id'=>$admin_role_id]);
                //更改店铺费用为付费
                //(new WeixinService())->init()->where(['id'=>$wid])->update(['is_fee'=>1],false);
                $shopService = new ShopService();
                $shopService->update($wid,['is_fee' => 1]);
                //记录日志
                $remark="续费时间为：".date('Y-m-d H:i:s',time())."，当前过期时间为：".$endTime."；店铺续费一年，店铺过期时间为：".$storeExpireTime."。";
                (new SelfFeeOperateLogService())->insertData(['order_id'=>$orderId,'wid'=>$wid,'remark'=>$remark]);
                if($weixinRoleData["errCode"]==0)
                {
                    $expireTimeStatus = true;
                    BLogger::getLogger('info')->info("微信扫码支付更改店铺的过期时间ok.");
                }
            }
        }
        return  $expireTimeStatus;
    }


}