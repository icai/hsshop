<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/10/20
 * Time: 15:48
 */

namespace App\Module;

use App\Events\OrderRefundEvent;
use App\Jobs\LossDistributeIncome;
use App\Module\AliApp\AliAppModule;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\BalanceLogService;
use App\S\WXXCX\WXXCXConfigService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Order\OrderRefundService;
use App\Services\OrderRefundMessageService;
use Illuminate\Support\Facades\Event;
use OrderService;
use DB;
use PaymentService;
use OrderCommon;
use WeixinService;
use Storage;
use OrderLogService;
use OrderDetailService;
use App\S\Weixin\ShopService;

class WeChatRefundModule
{
    //1、买家发起退款
    //2、商家同意退款
    //3、买家发货(退货订单填写？)
    //4、商家收货确认（退货订单签收）
    //5、退款（原路返回）
    //ps：退款时 有 余额支付 微信支付 支付宝 小程序

    //微信退款接口
    const WeChatRefundUrl = "https://api.mch.weixin.qq.com/secapi/pay/refund";
    //企业付款接口
    const WECHAT_PAY_URL = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";


    protected $wid;
    protected $oid;
    protected $pid;
    protected $prop_id;

    public function __construct($wid = 0 , $oid = 0, $pid = 0, $prop_id = 0)
    {
        $this->wid = $wid;
        $this->oid = $oid;
        $this->pid = $pid;
        $this->prop_id = $prop_id;
    }

    private static function __postXmlCurl($xml, $url, $useCert = false, $certPath= [], $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //如果有配置代理这里就设置代理
//        if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
//            && WxPayConfig::CURL_PROXY_PORT != 0){
//            curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
//            curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
//        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $certPath[0]);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $certPath[1]);
            }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);

            //不抛异常 日志记录
            \Log::info('退款curl出错：'.$error);
            return false;
        }
    }


    /**
     * 通过根据订单表的中小程序配置id获取小程序配置信息，默认为0时则取默认小程序的配置信息
     * @param $wid
     * @param $xcxConfigId
     * @return bool
     * @author: 梅杰 20180712
     */
    public function getMiniProConf($wid,$xcxConfigId = 0)
    {
        $xcxConfigData = $xcxConfigId == 0 ? (new WXXCXConfigService())->getRow($wid) : (new WXXCXConfigService())->getRowById($xcxConfigId);
        if ($xcxConfigData['errCode']== 0 && !empty($xcxConfigData['data'])) {
            $xcxConfigInfo = $xcxConfigData['data'];
            $conf['id'] = $xcxConfigInfo['id'];
            $conf['app_id'] = $xcxConfigInfo['app_id'];
            $conf['mch_id'] = $xcxConfigInfo['merchant_no'];
            $conf['mch_key'] = $xcxConfigInfo['app_pay_secret'];
            return $conf;
        }
        return false;
    }

    /**
     * 获取退款信息
     * @return array|bool
     * @author: 梅杰 20180712 增加返回订单的小程序配置id
     */
    public function getRefundData()
    {
        $orderData = OrderService::init('wid', $this->wid)->model->find($this->oid);
        if (!$orderData) {
            return false;
        }

        $orderData = $orderData->load('orderDetail')->toArray();

        //订单详情数量
        //$detailCount = count($orderData['orderDetail']);

        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $this->oid)->where(['oid' => $this->oid, 'pid' => $this->pid, 'prop_id' => $this->prop_id])->getInfo();
        //$hasRefundCount = $orderRefundService->init('oid', $this->oid)->model->where([ 'oid' => $this->oid])->whereIn('status',[4,8])->count();

        if (empty($orderData)) {
            return false;
        }
        //兼容之前未保存实际支付
        //退款申请的金额已经可能包含运费 不能再加一遍运费 20180124 Herry
        /*if ($hasRefundCount == $detailCount-1) {
            //订单中所有商品都退款完成 且最后一个商品还未发货 则退运费
            foreach ($orderData['orderDetail'] as $detail) {
                $where = [
                    'oid' => $detail['oid'],
                    'product_id' => $this->pid,
                    'product_prop_id' => $this->prop_id
                ];
                $orderDetailData = OrderDetailService::init()->model->wheres($where)->get()->toArray();
                $orderDetailData = $orderDetailData ? $orderDetailData[0] : [];
                if ($orderDetailData && $orderDetailData['is_delivery'] == 0 && $orderDetailData['oid'] == $this->oid && $orderDetailData['product_id'] == $this->pid && $orderDetailData['product_prop_id'] == $this->prop_id) {
                    $refund['amount'] = $refund['amount'] + $orderData['freight_price'];
                    break;
                }
            }
        }*/

        $refund['pay_price']    = $orderData['pay_price'];
        $refund['pay_way']      = $orderData['pay_way'];
        $refund['serial_id']    = $orderData['serial_id'];
        $refund['order_type']   = $orderData['type'];
        $refund['source']   = $orderData['source'];
        $refund['refund_status'] = $orderData['refund_status'];
        $refund['distribute_type'] = $orderData['distribute_type'];
        $refund['xcxConfigId']     = $orderData['xcx_config_id'];
        //获取最新退款金额 有可能退款申请修改过 Herry
        $refund_latest = (new RefundModule())->getLatestEditApply($refund);
        $refund['amount'] = $refund_latest['amount'];

        return $refund;
    }

    /**
     * 退款流程
     * @return array
     * @author 梅杰 2017年10月20日
     * @update 许立 2018年08月01日 增加支付宝退款
     */
    public function refund()
    {
        $refundData = $this->getRefundData();
        if($refundData == false){
            return [
                'code' => 'false',
                'code_des' =>'该订单不存在'
            ];
        }
        if ($refundData['distribute_type'] == 1){
            $job = (new LossDistributeIncome($this->oid))->delay(rand(1,5));
            dispatch($job);
        }

        /**
         * 根据对应支付方式调取不同的退款方法
         *
         * 支付方式：1微信支付；2支付宝支付；3储值余额支付；4货到付款/到店付款；5找人代付；6微信代销；7支付宝代销；8银行卡支付；9会员卡支付
         */
        switch ( $refundData['pay_way'] ) {
            case '1':
            case '10':
                $re =  $this->weChatRefund($this->wid,$refundData);
                break;
            case '2':
                $re = $this->_aliRefund($refundData);
                break;
            case '3':
                $re =  $this->balanceRefund($this->wid, $refundData);
                break;
            default:
                $re =  [
                    'code' => 'false',
                    'code_des' =>'该支付方式暂时不支持退款'
                ];
                break;
        }
        #todo 发送退款日志
        if ($re['code'] == 'SUCCESS') {
            Event::fire((new OrderRefundEvent($refundData['id'])));
        }
        return $re;
    }

    public function balanceRefund($wid, $refundData)
    {
        $return = [
            'code' => 'false',
            'code_des' => '退款成功'
        ];

        //如果退款到账 则返回 因为微信回调成功后还会回调 避免多次发送模板或多次报错
        if ($refundData['refund_status'] == 8) {
            return [
                'code' => 'false',
                'code_des' => '退款已经成功'
            ];
        }
        
        $money = $refundData['amount'];
        $mid = $refundData['mid'];

        $addStatus = (new MemberService())->operateMoney($mid,$money*100);
        if ($addStatus['errCode'] != 0 ) {
            $return['code_des'] = '退款失败';
            return $return;
        }

        $type = $money > 0 ? 1 : 2;
        $money = abs($money);

        $pay_way = 5;
        $status = 1;
        $msg   = '退款成功';
        $balanceLogService = new BalanceLogService();
        $balanceLogService->insertLog($wid, $mid, $money, $pay_way, $type , $status, $msg);

        //因为余额退款不需要回调 所以退款日志等操作都在这里执行 Herry 20171226
        //a.买家同意退款逻辑
        $refundModule = new RefundModule();
        $refundModule->_agreeRefund($wid, $refundData['oid'], $refundData['id']);

        //添加一条协商留言
        $data = [
            'mid' => $mid,
            'wid' => $wid,
            'refund_id' => $refundData['id'],
            'is_seller' => 1,
            'status' => 2,
            'content' => $money,
            'amount' => $money
        ];
        $refundMessageService = new OrderRefundMessageService();
        $refundMessageService->addMessage($data);

        //添加订单日志表记录
        $log = [
            'oid' => $refundData['oid'],
            'wid' => $wid,
            'mid' => $mid,
            'action' => 8,
            'remark' => '商家同意退款'
        ];
        OrderLogService::init()->add($log, false);
        OrderService::upOrderLog($refundData['oid'], $wid);

        //b.余额退款成功 改变退款状态
        $resultArr = $refundModule->success($refundData['oid'], $refundData['id'], '账户余额');

        $data = [
            'mid' => $mid,
            'wid' => $wid,
            'refund_id' => $refundData['id'],
            'is_seller' => 1,
            'status' => 7,
            'content' => '退款成功'
        ];
        $refundMessageService->addMessage($data);

        //c.判断退款是否包含运费
        $orderRefundService = new OrderRefundService();

        //一个订单中所有商品都退款完成 才改变订单状态（如3个商品 2个申请退款并成功 不关闭订单） Herry 20180104
        //todo 为什么取不到orderDetail 数据库 redis都有数据 其他方法中 getInfo能取到orderdetail
        //$orderData = OrderService::init('wid', $wid)->getInfo($oid);
        $orderData = OrderService::init('wid', $wid)->model->find($refundData['oid'])->load('orderDetail')->toArray();

        //一个订单中所有商品都退款成功 则最后一个退款完成的退款设置退款运费
        $hasRefundCount = $orderRefundService->init('oid', $refundData['oid'])->model->where([ 'oid' => $refundData['oid']])->whereIn('status',[4,8])->count();
        $detailCount = count($orderData['orderDetail']);
        if ($hasRefundCount == $detailCount) {
            $latestSuccessRefund = $orderRefundService->init('oid', $refundData['oid'])->model->where([ 'oid' => $refundData['oid']])->whereIn('status',[4,8])->orderBy('success_at', 'desc')->take(1)->get()->toArray();
            if ($latestSuccessRefund && $latestSuccessRefund[0]['id'] == $refundData['id']) {
                $orderRefundService->init('oid', $refundData['oid'])
                    ->where(['id' => $refundData['id']])
                    ->update(['return_freight' => $orderData['freight_price']], false);
            }
        }


        if ($resultArr['errCode'] == 0 || $resultArr['errCode'] == 1000) {
//            $param = [
//                'isGroup' => $isGroup,
//                'id'   => $isGroup ? $refundData['serial_id'] : $refundData['id'],
//                'amount' => $parameters['refund_fee']
//            ];
//            (new MessagePushModule($wid,MessagesPushService::OrderRefund))->sendMsg($param);

            return [
                'code' => 'SUCCESS',
                'code_des' =>$return['code_des']
            ];
        } else {
            $return['code_des'] = $resultArr['errMsg'];
            return $return;
        }
    }

    //申请微信退款
    //企业付款

    /**
     *
     * @update 张永辉 2018年6月29日 修改错误信息，明确提示是小程序错误还是公众号错误
     * @update 梅杰 20180720 根据配置小程序id 获取商户证书退款
     * @update 梅杰 20180725 修改小程序商户证书获取路径
     */
    public function weChatRefund($wid,$refundData, $isGroup = false)
    {
        $conf = (new WeChatAuthModule())->getConf($wid);
        if($refundData['pay_way'] == 10){
            $conf = $this->getMiniProConf($wid,$refundData['xcxConfigId']);
            $parameters['op_user_id']       = $refundData['op_user_id'] ?? $conf['mch_id']; //操作员帐号, 默认为商户号
            $xcxConfigId = $refundData['xcxConfigId'];
            $sslCertPath = $xcxConfigId == 0 ? "hsshop/cert/{$wid}_cert/mini_cert/apiclient_cert.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_$xcxConfigId/apiclient_cert.pem";
            $sslKeyPath = $xcxConfigId == 0 ? "hsshop/cert/{$wid}_cert/mini_cert/apiclient_key.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_$xcxConfigId/apiclient_key.pem"  ;

        }else{
            $sslCertPath = "hsshop/cert/{$wid}_cert/api_cert/apiclient_cert.pem";
            $sslKeyPath = "hsshop/cert/{$wid}_cert/api_cert/apiclient_key.pem";
        }
        if(!Storage::exists($sslCertPath) || !Storage::exists($sslKeyPath) ){
            if ($refundData['pay_way'] == 10){
                return [
                    'code' => 'false',
                    'code_des' =>'请先上传小程序商户证书'
                ];
            }else{
                return [
                    'code' => 'false',
                    'code_des' =>'请先上传公众号商户证书'
                ];
            }

        }

        //使用绝对路径 curl error 52问题 Herry
        $certPath = [public_path() . '/' . $sslCertPath, public_path() . '/' . $sslKeyPath];

        if(!$conf){
            return [
                'code' => 'false',
                'code_des' =>'获取配置信息失败'
            ];
        }
        // 公众账号ID 微信支付分配的公众账号ID（企业号corpid即为此appId）
        $parameters['appid']            = $conf['app_id'];
        // 商户号 微信支付分配的商户号
        $parameters['mch_id']           = $conf['mch_id'];
        // 随机字符串 长度要求在32位以内
        $parameters['nonce_str']        = PaymentService::createNoncestr();
        $parameters['transaction_id']   = $refundData['serial_id'];
        //拼团订单自动退款不走退款流程没有refundID 普通退款使用refundID_前缀 拼团退款使用productID_前缀 Herry
        $parameters['out_refund_no']    = $isGroup ? 'p_' . $refundData['id'] . '_' . time() : 'r_' . $refundData['id'] . '_' . time();
        $parameters['total_fee']        = intval(strval($refundData['pay_price'] * 100));
        $parameters['refund_fee']       = intval(strval($refundData['amount']* 100));
        //$parameters['refund_account']   = 'REFUND_SOURCE_RECHARGE_FUNDS';
        // 签名 通过签名算法计算得出的签名值
        $parameters['sign']             = PaymentService::getSign( $parameters, $conf );
        // 数组转xml
        $xml = PaymentService::arrayToXml($parameters);
        $re = self::__postXmlCurl($xml,self::WeChatRefundUrl,true,$certPath);

        if ($re == false) {
            return [
                'code' => 'false',
                'code_des' =>'退款curl出错'
            ];
        }

        // xml转数组
        $result = PaymentService::xmlToArray($re);
        $param = [
            'isGroup' => $isGroup,
            'id'   => $isGroup ? $refundData['serial_id'] : $refundData['id'],
            'amount' => $parameters['refund_fee']
        ];
        if ( $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            #todo 发送微信公众号模板消息
//            $refundData['pay_way'] == 1 && (new MessagePushModule($wid,MessagesPushService::OrderRefund))->sendMsg($param);
//            $refundData['pay_way'] == 10 && (new MessagePushModule($wid,MessagesPushService::OrderRefund,MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($param);
            return [
                'code' => $result['result_code'],
                'code_des' => '退款申请成功'
            ];
        }

        //订单已全额退款 返回SUCCESS Herry
        if (isset($result['err_code_des']) && $result['err_code_des'] == '订单已全额退款') {
            $code = 'SUCCESS';
//            #todo 发送微信公众号模板消息
//            $refundData['pay_way'] == 1 && (new MessagePushModule($wid,MessagesPushService::OrderRefund))->sendMsg($param);
//            $refundData['pay_way'] == 10 && (new MessagePushModule($wid,MessagesPushService::OrderRefund,MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($param);
        } else {
            $code = $result['err_code'] ?? $result['return_code'];
        }
        return [
            'code' => $code,
            'code_des' =>$result['err_code_des'] ?? $result['return_msg']
        ];
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180411
     * @desc企业付款功能
     * @param array $data
     * @param int $wid
     * @param $class
     * @return array|bool
     * @update 张永辉 2018年6月29日 修改错误信息，明确提示是小程序错误还是公众号错误
     * @update 张永辉 2018年7月4日 错误提示状态字段修改
     */
    public function mmpaymkttransfers($data = [],$class,$source)
    {
        $result = ['errCode'=>0,'errMsg'=>''];
        $wid = $data['wid'];
        if ($source == 0){
            $conf = (new WeChatAuthModule())->getConf($wid);
        }else{
            $conf = $this->getMiniProConf($wid);
        }

        if (!$conf){
            $result['errCode'] = -11;
            $result['errMsg'] = '配置信息为空';
            return $result;
        }
        //微信分配的账号ID（企业号corpid即为此appId）
        $parameters['mch_appid']            = $conf['app_id'];
        // 商户号 微信支付分配的商户号
        $parameters['mchid']               = $conf['mch_id'];
        // 随机字符串 长度要求在32位以内
        $parameters['nonce_str']            = PaymentService::createNoncestr();
        //商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
        $parameters['partner_trade_no']     = $data['order_num'];
        //商户appid下，某用户的openid
        $parameters['openid']               = $data['openid'];
        //NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
        $parameters['check_name']           = 'NO_CHECK';
        //收款用户真实姓名。如果check_name设置为FORCE_CHECK，则必填用户真实姓名
//        $parameters['re_user_name']         = '';
        //企业付款金额，单位为分 最少1块钱
        $parameters['amount']               = intval(strval($data['amount'])) ;
        //企业付款操作说明信息。必填。
        $parameters['desc']                 = $data['desc'] ?? '企业付款';
        //调用接口的机器Ip地址
        $parameters['spbill_create_ip']     = $_SERVER["REMOTE_ADDR"];
        // 签名 通过签名算法计算得出的签名值
        $parameters['sign']             = PaymentService::getSign( $parameters, $conf );
        // 数组转xml
        $xml = PaymentService::arrayToXml($parameters);
        // 以post方式提交xml到统一下单接口


        if($source == 0){
            $sslCertPath = "hsshop/cert/{$wid}_cert/api_cert/apiclient_cert.pem";
            $sslKeyPath = "hsshop/cert/{$wid}_cert/api_cert/apiclient_key.pem";
        }else{
            $sslCertPath = 'hsshop/cert/'.$wid.'_cert/mini_cert/apiclient_cert.pem';
            Storage::exists($sslCertPath) || $sslCertPath = 'hsshop/cert/'.$wid.'_cert/mini_cert_'.$conf['id'].'/apiclient_cert.pem';
            $sslKeyPath = 'hsshop/cert/'.$wid.'_cert/mini_cert/apiclient_key.pem';
            Storage::exists($sslKeyPath) || $sslKeyPath = 'hsshop/cert/'.$wid.'_cert/mini_cert_'.$conf['id'].'/apiclient_key.pem';
        }

        if(!Storage::exists($sslCertPath) || !Storage::exists($sslKeyPath) ){
            if ($source == 0){
                return [
                    'errCode' => 'false',
                    'errMsg' =>'请先上传公众号商户证书'
                ];
            }else{
                return [
                    'errCode' => 'false',
                    'errMsg' =>'请先上传小程序商户证书'
                ];
            }
        }
        $certPath = [public_path() . '/' . $sslCertPath, public_path() . '/' . $sslKeyPath];
        $re = self::__postXmlCurl($xml,self::WECHAT_PAY_URL,true,$certPath);
        // xml转数组
        $res = PaymentService::xmlToArray($re);
        if($res['result_code'] == 'SUCCESS'){
            if ($res['result_code'] == 'SUCCESS'){
                $res = $class->companyPayCallBack($res);
                return $result;
            }else{
                $result['errCode'] = $res['err_code'];
                $result['errMsg'] = $res['err_code_des'];
                return $result;
            }
        }else{
            $result['errCode'] = $res['return_code'];
            $result['errMsg'] = $res['err_code_des'];
            return $result;
        }


    }

    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function sendRefundTpl($refund = [])
    {
        try{
            $refund['out_trade_no'] = explode("_", $refund['out_trade_no']);
            $refund['out_trade_no'] =  $refund['out_trade_no'][0];
            $orderService = new \App\S\Order\OrderService();
            $orderData = $orderService->getRowByWhere(['id'=>$refund['out_trade_no']]);
            //$storeInfo = WeixinService::init()->getInfo($orderData['wid']);
            $shopService = new ShopService();
            $storeInfo = $shopService->getRowById($orderData['wid']);
            //获取接收方openid
            $memberService = new MemberService();
            $memberInfo = $memberService->model->select(['openid','xcx_openid'])->find($orderData['mid']);
            $memberInfo = $memberInfo->toArray();
            $data = [
                'wid'           => $orderData['wid'],
                'order_id'      => $orderData['id'],
                'oid'           => $orderData['oid'],
                'pid'           => $refund['productID'],
                'isGroupAutoRefund' => $refund['isGroupAutoRefund'],
                'groups_id'     => $orderData['groups_id'],
                //退款金额=申请退款金额-非充值代金券退款金额，退款金额<=申请退款金额(微信返回)
                'refund_fee'    =>  sprintf('%.2f',intval($refund['settlement_refund_fee'])/100) ,
                'refund_remark' => $refund['refund_remark'] ?? "退款成功",
            ];
            switch ($orderData['source'])
            {
                case 0://公众号
                    $data['open_id'] = $memberInfo['openid'];
                    $data['propID'] = $refund['propID'];
                    $this->wxOrderRefundTpl($data);
                    break;
                case 1:
                    //小程序
                    $data['xcx_open_id']    = $memberInfo['xcx_openid'];
                    //todo 拼团下单 form_id需要前端传过来 暂时使用prepay_id代替 不然退款服务消息收不到 Herry
                    $data['form_id']        = $orderData['prepay_id'];
                    $data['refund_shop']    = $storeInfo['shop_name'];
                    $data['refund_time']    = $refund['success_time'];
                    $this->miniProOrderRefundTpl($data);
                    break;
                default:
                    break;
            }
        }catch (\Exception $exception){
            \Log::info('退款模板发送失败：'.$exception->getMessage());
        }

    }

    //{{first.DATA}}
    //订单编号：{{keyword1.DATA}}
    //退款金额：{{keyword2.DATA}}
    //{{remark.DATA}}
    public function wxOrderRefundTpl($refund = [])
    {
        $data['touser'] = $refund['open_id'];

        if ($refund['isGroupAutoRefund'] == 1) {
            if ($refund['groups_id']) {
                $data['url']    = config('app.url').'/shop/order/groupsOrderDetail/'.$refund['order_id'].'/'.$refund['wid'];
            } else {
                $data['url']    = config('app.url').'/shop/order/detail/'.$refund['order_id'];
            }
        } else {
            $data['url']    = config('app.url').'/shop/order/refundDetailView/'.$refund['wid'].'/'.$refund['order_id'].'/'.$refund['pid'].'/'.$refund['propID'];
        }

        //获取物流信息
        $data['data']['keyword1']   = [
            'value' => $refund['oid'],
        ];
        $data['data']['keyword2']   = [
            'value' => $refund['refund_fee'],
        ];
        $data['data']['remark']     = [
            'value' => empty($refund['refund_remark']) ? $refund['refund_remark'] : "退款成功",
        ];
        $service = new WechatBakModule();
        $service->sendTplNotify($refund['wid'],$data,$service::REFUND_SUCCESS);
    }

    /**
     * 订单号{{keyword1.DATA}}
     * 退款商家{{keyword2.DATA}}
     * 退款金额{{keyword3.DATA}}
     * 退款时间{{keyword4.DATA}}
     * 备注{{keyword5.DATA}}
     */
    public function miniProOrderRefundTpl($refund = [])
    {
        //
        $data['touser'] = $refund['xcx_open_id'];
        $data['form_id'] = $refund['form_id'];

//        if ($refund['isGroupAutoRefund'] == 1) {
//            if ($refund['groups_id']) {
//                $data['page']    = '/pages/grouppurchase/orderDetail/orderDetail?oid='.$refund['order_id'].'&groups_id='.$refund['groups_id'];
//            } else {
//                $data['page']    = '/pages/order/orderDetail/orderDetail?oid='.$refund['order_id'];
//            }
//        } else {
//            $data['page'] = '/pages/member/refund/details/details?oid='.$refund['order_id'].'&pid='.$refund['pid'];
//        }

        $data['data']['keyword1'] = [
            'value' => $refund['oid'],
        ];
        $data['data']['keyword2'] = [
            'value' => $refund['refund_shop'],
        ];
        $data['data']['keyword3'] = [
            'value' => $refund['refund_fee'],
        ];
        $data['data']['keyword4'] = [
            'value' => $refund['refund_time'],
        ];
        $data['data']['keyword5'] = [
            'value' => empty($refund['refund_remark']) ? $refund['refund_remark'] : "退款成功"
        ];
        $service = new WXXCXSendTplService($refund['wid']);
        $re = $service->sendTplNotify($data,$service::REFUND_NOTIFY);
    }

    /**
     * 支付宝退款并更新订单状态
     * @param array $refund 退款记录
     * @return array
     * @author 许立 2018年08月01日
     */
    private function _aliRefund($refund)
    {
        // 支付宝退款
        $aliRefundResult = (new AliAppModule())->aliappOrderRefund($this->oid, $refund['amount']);
        if ($aliRefundResult['errCode'] == 0) {
            // 退款成功 处理退款记录和订单记录
            // 支付宝退款不需要回调 所以退款日志等操作都在这里执行
            // a.买家同意退款逻辑
            $refundModule = new RefundModule();
            $refundModule->_agreeRefund($this->wid, $this->oid, $refund['id']);

            //添加一条协商留言
            $data = [
                'mid' => $refund['mid'],
                'wid' => $this->wid,
                'refund_id' => $refund['id'],
                'is_seller' => 1,
                'status' => 2,
                'content' => $refund['amount'],
                'amount' => $refund['amount']
            ];
            $refundMessageService = new OrderRefundMessageService();
            $refundMessageService->addMessage($data);

            //添加订单日志表记录
            $log = [
                'oid' => $this->oid,
                'wid' => $this->wid,
                'mid' => $refund['mid'],
                'action' => 8,
                'remark' => '商家同意退款'
            ];
            OrderLogService::init()->add($log, false);
            OrderService::upOrderLog($this->oid, $this->wid);

            //b.余额退款成功 改变退款状态
            $resultArr = $refundModule->success($this->oid, $refund['id'], '支付宝余额');

            $data = [
                'mid' => $refund['mid'],
                'wid' => $this->wid,
                'refund_id' => $refund['id'],
                'is_seller' => 1,
                'status' => 7,
                'content' => '退款成功'
            ];
            $refundMessageService->addMessage($data);

            //c.判断退款是否包含运费
            $orderRefundService = new OrderRefundService();
            //一个订单中所有商品都退款完成 才改变订单状态（如3个商品 2个申请退款并成功 不关闭订单）
            //todo 为什么取不到orderDetail 数据库 redis都有数据 其他方法中 getInfo能取到orderdetail
            //$orderData = OrderService::init('wid', $wid)->getInfo($oid);
            $orderData = OrderService::init('wid', $this->wid)->model->find($this->oid)->load('orderDetail')->toArray();
            //一个订单中所有商品都退款成功 则最后一个退款完成的退款设置退款运费
            $hasRefundCount = $orderRefundService->init('oid', $this->oid)->model->where([ 'oid' => $this->oid])->whereIn('status',[4,8])->count();
            $detailCount = count($orderData['orderDetail']);
            if ($hasRefundCount == $detailCount) {
                $latestSuccessRefund = $orderRefundService->init('oid', $this->oid)->model->where([ 'oid' => $this->oid])->whereIn('status',[4,8])->orderBy('success_at', 'desc')->take(1)->get()->toArray();
                if ($latestSuccessRefund && $latestSuccessRefund[0]['id'] == $refund['id']) {
                    $orderRefundService->init('oid', $this->oid)
                        ->where(['id' => $refund['id']])
                        ->update(['return_freight' => $orderData['freight_price']], false);
                }
            }

            if ($resultArr['errCode'] == 0 || $resultArr['errCode'] == 1000) {
                return [
                    'code' => 'SUCCESS',
                    'code_des' => '支付宝退款成功'
                ];
            } else {
                return [
                    'code' => 'false',
                    'code_des' => $resultArr['errMsg']
                ];
            }
        } else {
            return [
                'code' => 'false',
                'code_des' => $aliRefundResult['errMsg']
            ];
        }
    }

}