<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/14
 * Time: 19:32
 */

namespace App\Module;

use App\Events\OrderPayedEvent;
use App\Jobs\CheckGrantCard;
use App\Jobs\ImportOrderLogistics;
use App\Jobs\Distribution;
use App\Jobs\sendCodeKeyProduct;
use App\Jobs\SendPayedOrderLog;
use App\Jobs\SendSMS;
use App\Jobs\SendTakeAway;
use App\Lib\Redis\CouponLogRedis;
use App\S\Market\CouponLogService;
use OrderService;
use OrderLogService;
use DB;
use Log;
use App\S\WXXCX\WXXCXPaymentLogService;
use WXXCXCache;
use PaymentService;
use App\Lib\BLogger;
use Event;
use App\S\Fee\SelfOrderService;
use App\S\Fee\SelfOrderDetailService;
use QrCode;
use App\S\WXXCX\WXXCXConfigService;

class XCXPaymentModule
{
    /**
     * todo 小程序支付
     * @param $conf
     * @return array
     * @author jonzhang
     * @date 2017-08-15
     */
    public function pay($conf)
    {
        $result=['errCode'=>0,'errMsg'=>'','data'=>[]];
        try
        {
            $errMsg='';
            //小程序ID
            if(empty($conf['appid']))
            {
                $errMsg.='appid为空';
            }
            if(empty($conf['openid']))
            {
                $errMsg.='openid为空';
            }//支付密钥
            if(empty($conf['pay_key']))
            {
                $errMsg.='pay_key为空';
            }//商户号
            if(empty($conf['mch_id']))
            {
                $errMsg.='mch_id为空';
            }//商品订单号 必填
            if(empty($conf['out_trade_no']))
            {
                $errMsg.='out_trade_no为空';
            }//商品描述 必填
            if(empty($conf['body']))
            {
                $errMsg.='body为空';
            } //支付金额 此处为分
            if(empty($conf['total_fee']))
            {
                $errMsg.='total_fee为空';
            } //回调地址 必填
            if(empty($conf['call_back_url']))
            {
                $errMsg.='call_back_url为空';
            }
            if(strlen($errMsg)>0)
            {
                $result['errCode']=-1;
                $result['errMsg']=$errMsg;
                return $result;
            }
            //统一下单接口
            return $this->xcxApp($conf);
        }
        catch(\Exception $ex)
        {
            $result['errCode']=-2;
            $result['errMsg']=$ex->getMessage();
            return $result;
        }
    }

    /**
     * todo 微信小程序接口
     * @param $conf
     * @return array
     * @author jonzhang
     * @date 2017-08-15
     */
    private function xcxApp($conf)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        //BLogger::getLogger('info')->info('conf:'.json_encode($conf));
        //统一下单接口
        $result = $this->unifiedorder($conf);
        //BLogger::getLogger('info')->info('pay result:'.json_encode($result));
        $result['return_code']  = $result['return_code'] ?? '';
        $result['result_code']  = $result['result_code'] ?? '';
        $result['return_msg']   = $result['return_msg'] ?? '未知错误';
        $result['err_code_des'] = $result['err_code_des'] ?? '未知错误';

        if ( $result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS' )
        {
            #TODO 将$result['prepay_id'] 存入数据库 模板消息使用
            if (strpos($conf['out_trade_no'], 'balance') === false) {
                try {
                    $re = OrderService::init()->where(['id' => $result['id'] ])->updateD(['prepay_id'=>$result['prepay_id']],false);
                }
                catch (\Exception $exception){
                    \Log::info('预付款id更新失败：'.$exception->getMessage());
                }
            }

            $parameters = [
                'appId' =>$conf['appid'], //小程序ID
                'timeStamp' => '' . time() . '', //时间戳
                'nonceStr' => $this->createNoncestr(), //随机串
                'package' => 'prepay_id=' . $result['prepay_id'], //数据包
                'signType' => 'MD5'//签名方式
            ];
            //签名
            $parameters['paySign'] = $this->getSign($parameters,$conf['pay_key']);
            $returnData['data']=$parameters;
            return $returnData;
        }
        else if ( $result['return_code'] === 'FAIL' )
        {
            //return_code 返回状态码
            $returnData['errCode']=-10000;
            $returnData['errMsg']='状态码问题:'.$result['return_msg'];
            return $returnData;
        }
        else if ( $result['result_code'] === 'FAIL' )
        {
            //result_code 业务结果
            $returnData['errCode']=-10001;
            $returnData['errMsg']='业务结果问题:'.$result['err_code_des'];
            return $returnData;
        }
        else
        {
            //result_code 业务结果
            $returnData['errCode']=-10002;
            $returnData['errMsg']='通信失败';
            return $returnData;
        }
    }

    /**
     * todo 统一下单接口
     * @param $conf
     * @return mixed
     * @author jonzhang
     * @date 2017-08-15
     */
    private function unifiedorder($conf)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

        //为兼容订单改价 需要加随机数 Herry 20180516
        $out_trade_no_rand = $conf['out_trade_no'].'_'.rand();
        $parameters = [
            'appid' => $conf['appid'], //小程序ID
            'mch_id' => $conf['mch_id'], //商户号
            'nonce_str' => $this->createNoncestr(), //随机字符串
            'body' => $conf['body'],//商品描述
            'out_trade_no'=> $out_trade_no_rand,//商户订单号
            'total_fee' => $conf['total_fee'],//总金额 单位 分
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //终端IP
            'notify_url' =>$conf['call_back_url'], //通知地址  确保外网能正常访问
            'openid' => $conf['openid'], //用户id
            'trade_type' => 'JSAPI'//交易类型
        ];
        //余额支付用============陈文豪
        if (strpos($conf['out_trade_no'], 'balance') !== false) {
            $balanceId = str_replace('balance', '', $conf['out_trade_no']);
            $parameters['attach'] = $conf['out_trade_no'].'#';
            $parameters['out_trade_no'] = $balanceId;
        }
        //=======
        //BLogger::getLogger('info')->info('pay parameter:'.json_encode($parameters));
        //统一下单签名
        $parameters['sign'] = $this->getSign($parameters,$conf['pay_key']);
        $xmlData = $this->arrayToXml($parameters);
        $return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));

        $return['id'] = $conf['out_trade_no'];
        return $return;
    }

    /**
     * todo 发送api请求
     * @param $xml
     * @param $url
     * @param int $second
     * @return mixed
     * @author jonzhang
     * @date 2017-08-15
     */
    private static function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        set_time_limit(0);

        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data)
        {
            curl_close($ch);
            return $data;
        }
        else
        {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new \Exception("curl出错，错误码:$error");
        }
    }

    /**
     * todo 把要发送到微信支付接口的数组数据转化为xml
     * @param $arr
     * @return string
     * @author jonzhang
     * @date 2017-08-15
     */
    private function arrayToXml($arr,$level = 1)
    {
        $s = $level == 1 ? "<xml>" : '';
        foreach($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if(!is_array($value)) {
                $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->arrayToXml($value, $level + 1)."</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</xml>" : $s;
    }

    /**
     * todo 把微信小程序支付接口返回的xml转化为数组
     * @param $xml
     * @return mixed
     * @author jonzhang
     * @date 2017-08-15
     */
    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $val = json_decode(json_encode($xmlstring), true);

        return $val;
    }

    /**
     * todo 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     * @author jonzhang
     * @date 2017-08-15
     */
    private function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++)
        {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * todo 生成签名
     * @param $Obj
     * @return string
     * @author jonzhang
     * @date 2017-08-15
     */
    public function getSign($obj,$payKey)
    {
        foreach ($obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String . "&key=" . $payKey;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }

    /**
     * todo 格式化参数，签名过程需要使用
     * @param $paraMap
     * @param $urlencode
     * @return bool|string
     * @author jonzhang
     * @date 2017-08-15
     */
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if ($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar="";
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * todo 小程序支付回调
     * @author jonzhang
     * @date 2017-08-15
     * 何书哲 2018年7月4日 添加小程序订单导入快递管家
     * @update 许立 2018年11月09日 异常回调数据记录日志
     */
    public function xcxPayNotify()
    {
        $postXml = isset($GLOBALS["HTTP_RAW_POST_DATA"])? $GLOBALS['HTTP_RAW_POST_DATA'] : ''; //拿到小程序回调回来的信息判断支付成功没

        if(empty($postXml)){
            $postXml = file_get_contents('php://input');
        }

        if(!empty($postXml))
        {
            try {
                $exception = false;
                $data= $this->xmlToArray($postXml);
            } catch (\Exception $e) {
                $exception = true;
                Log::info('小程序支付回调数据异常: ' . $e->getMessage());
            }
            if (!isset($data['return_code'])) {
                $exception = true;
                Log::info('小程序支付回调数据没有return_code等字段');
            }
            if ($exception) {
                echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[回调数据字段异常]]></return_msg></xml>';
                exit;
            }
            //$data['return_code'] == 'SUCCESS'表示签名成功

            //余额支付
            $orderIds = 0;
            if (isset($data['attach'])) {
                $attach = explode('#',$data['attach']);
                $orderIds = explode(',',$attach[0]);
            }
            //BLogger::getLogger('info')->info('支付回调信息:',$data);

            //为兼容订单改价 需要加随机数 Herry 20180516
            //获取真实订单ID
            $out_trade_no_real = explode('_', $data['out_trade_no'])[0];
            
            if(!empty($data['return_code'])&&$data['return_code'] == 'SUCCESS')
            {
                //获取服务器返回的数据
                $payLog['order_no'] = $out_trade_no_real;//订单单号
                $payLog['open_id'] = $data['openid'];//付款人openID
                $payLog['total_fee'] = $data['total_fee'];//付款金额
                $payLog['transaction_no'] = $data['transaction_id'];//微信支付流水号
                $payLog['pay_time'] = $data['time_end'];//支付完成时间
                $payLog['result_code'] = $data['result_code'];//支付结果
                $payLog['app_id'] = $data['appid'];//appid
                $payLog['sign'] = $data['sign'];//sign
                //支付失败错误信息
                if(!empty($data['result_code'])&&$data['result_code'] == 'FAIL')
                {
                    $payLog['err_code']=$data['err_code'];
                    $payLog['err_code_des']=$data['err_code_des'];
                }
                $wxxcxPaymentLog=new WXXCXPaymentLogService();
                $wxxcxPaymentLog->insertData($payLog);
            }
            // 判断签名是否正确  判断支付状态
            if (($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS'))
            {
                //微信服务器返回的签名sign
                $data_sign = $data['sign'];
                //sign不参与签名算法
                unset($data['sign']);
                //获取appid对应的支付key
                $payKey=WXXCXCache::get($data['appid'],'xcx_pay_check');
                if(empty($payKey))
                {
                    $xcxConfigData=(new WXXCXConfigService())->getListByCondition(['current_status'=>0,'app_id'=>$data['appid']]);
                    if($xcxConfigData['errCode']==0&&!empty($xcxConfigData['data']))
                    {
                        $payKey=$xcxConfigData['data'][0]['app_pay_secret']??0;
                        if(empty($payKey))
                        {
                            BLogger::getLogger('error')->info('支付回调验证信息时，支付密钥为空 ');
                        }
                    }
                }
                $sign = $this->getSign($data,$payKey);
                //验证签名是否相等
                if($data_sign==$sign)
                {
                    //何书哲 2018年7月4日 添加小程序订单导入快递管家
                    try{
                        $orderData = OrderService::init()->where(['id'=>$out_trade_no_real])->getInfo($out_trade_no_real);
                        $orderData && dispatch((new ImportOrderLogistics($orderData['wid'], $out_trade_no_real))->onQueue('ImportOrderLogistics'));
                    }catch (\Exception $e)
                    {
                        \Log::info('小程序订单id:'.$out_trade_no_real.'导入快递管家失败');
                    }
                    //更新数据
                    $status = false;
                    $i = 0;
                    while ($i < 3 && !$status)
                    {
                        //余额支付
                        if (strpos($orderIds[0], 'balance') !== false) {
                            $balanceId = str_replace('balance', '', $orderIds[0]);
                            PaymentService::balanceSuccessSubsequent($balanceId, 3, $data);
                        } else {
                            $status = $this->ProcessDataForOrder($out_trade_no_real, 10, $data);
                        }
                        
                        $i++;
                    }
                    $result = true;
                }
                else
                {
                    $result = false;
                }
            }
            else
            {
                $result = false;
            }
            // 返回状态给微信服务器
            if ($result) {
                Log::info('小程序支付回调处理成功[trade_id: ' . $out_trade_no_real . ']');
                $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            } else {
                $str = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
            }
            echo $str;
        }
    }

    /**
     * @param $orderId
     * @param int $payWay
     * @param array $orderData
     * @return bool
	 * @update 梅杰 20180709 发送模板消息时根据指定小程序发送
     * @update 何书哲 2018年7月4日 订单打单导入任务队列
     * @update 梅杰 2018年8月9日 增加开米商品自动发货
     * @update 张永辉 2018年8月23日 按规则发放会员卡队列
     * @update 梅杰 2018年10月18日 付款成功消息提醒
     * @update 何书哲 2018年11月15日 外卖订单导入第三方
     */
    private function ProcessDataForOrder($orderId, $payWay = 0, $orderData = [])
    {
        //事务开始 处理数据库逻辑
        $mid = 0;
        DB::beginTransaction();
        try
        {
            //转化为数组
            if (!is_array($orderId)) {
                $orderId = [intval($orderId)];
            }

            //订单状态改为待发货
            $saveData = [];
            $saveData['status'] = 1;

            //修改支付方式
            $saveData['pay_way'] = $payWay;

            //交易流水号
            if ($payWay == 1 || $payWay == 10)
            {
                //微信支付
                $saveData['serial_id'] = $orderData['transaction_id'];
                $saveData['cash_fee'] = $orderData['cash_fee']/100;
            } //暂无使用
            elseif ($payWay == 2) {
                //支付宝支付
                $saveData['serial_id'] = $orderData['trade_no'];
            }
            //BLogger::getLogger('info')->info('$orderId:',$orderId);
            //BLogger::getLogger('info')->info('$saveData:',$saveData);
            //获取订单数据
            $orderList= OrderService::init()->model->wheres(['id'=>['in',$orderId]])->get()->toArray();
            //改变订单状态 代发货状态
            if (!(OrderService::init('wid', $orderList[0]['wid'])->where(['id' => ['in', $orderId]])->updateD($saveData,false))) {
                throw new \Exception('修改订单状态失败');
            }

            //订单操作记录新增数据
            foreach ($orderList as $k => $v) {
                if (1 === $v['status'] || 7=== $v['status']) {
                    return true;
                }
                $mid=$v['mid'];
                //新增数据
                $insertData = [
                    'oid' => $v['id'],
                    'wid' => $v['wid'],
                    'mid' => $v['mid'],
                    'action' => 2,
                    'remark' => '订单id: ' . $v['id'] . ', 订单编号: ' . $v['oid'] . ', 付款方式: ' . $payWay
                ];
                $logId = OrderLogService::init()->addD($insertData, false);

                if (!$logId) {
                    throw new \Exception('增加订单操作记录失败');
                }
                $insertData['id'] = $logId;
                //暂时新增订单操作记录 后面更新redis使用
                $redisOrder = OrderService::init('wid', $v['wid'])->getInfo($v['id']);
                if (isset($redisOrder['orderLog'])) {
                    $orderList[$k]['orderLog'] = $redisOrder['orderLog'];
                }
                $orderList[$k]['orderLog'][] = $insertData;
                //团购订单进行 add by zhangyh
                if (isset($v['groups_id'])&&$v['groups_id'] != 0){
                    $groups = new GroupsRuleModule();
                    $groups->afterOrder($v);
                }
                $job = (new Distribution($v))->onQueue('Distribution');
                dispatch($job);
                //add end

                //短信通知 add MayJay
//                dispatch((new SendSMS($v['wid'],$v['oid']))->onQueue('SendSMS'));
                //何书哲 2018年7月4日 订单打单导入任务队列
                dispatch((new ImportOrderLogistics($v['wid'],$v['id']))->onQueue('ImportOrderLogistics'));
                //卡密商品自动发货
                if ($v['type'] == 12) {
                    dispatch((new sendCodeKeyProduct( $v['wid'],$v['mid'],$v['id']))->onQueue('sendCdKey'));
                }
                //按规则发放会员卡队列
                dispatch((new CheckGrantCard($v['mid'],$v['wid']))->onQueue('CheckGrantCard'));

                //何书哲 2018年11月15日 外卖订单导入第三方
                dispatch(new SendTakeAway($v['id']));

            }

            //优惠券标记为已使用
            $couponLogService = new CouponLogService();
            foreach ($orderId as $id) {
                $couponLog = $couponLogService->getRowByOid($id);
                if ($couponLog) {
                    $updateResult = $couponLogService->model->where('id', $couponLog['id'])->update(['status' => 2]);
                    if (!$updateResult) {
                        throw new \Exception('修改优惠券状态失败');
                    }
                }
            }

            //事务提交 处理redis逻辑
            DB::commit();

            //改变订单状态 代发货状态
            foreach ($orderId as $id) {
                if (!(OrderService::init('wid', $orderList[0]['wid'])->updateR($id, $saveData, false))) {
                    //return myerror('修改订单状态失败');
                    Log::info('小程序支付后续 redis 修改订单状态失败');
                }
                Event::fire(new OrderPayedEvent($id));
            }

            //订单操作记录新增数据
            foreach ($orderList as $k => $v) {
                if (!(OrderService::init('wid', $orderList[0]['wid'])->updateR($v['id'], ['orderLog' => json_encode($v['orderLog'])], false))) {
                    //return myerror('订单操作记录新增数据失败');
                    Log::info('小程序支付后续 redis 订单操作记录新增数据失败');
                }
            }

            //优惠券标记为已使用
            $couponLogRedis = new CouponLogRedis();
            foreach ($orderId as $id) {
                $couponLog = $couponLogService->getRowByOid($id);
                if ($couponLog) {
                    $couponLogRedis->updateRow(['id' => $couponLog['id'], 'status' => 2]);
                }
            }

        }
        catch (\Exception $e)
        {
            Log::info($e->getMessage());
            //事务回滚
            DB::rollBack();
            return false;
        }
        //关联商品大转盘参加次数增加
        try{
            foreach ($orderId as $oid){
                (new WheelModule())->addTime($oid,$mid);
            }
        }catch (\Exception $e){
            \Log::info('大转盘关联商品添加次数更新错误'.$e->getMessage());
        }

        return true;
    }

    /**
     * todo 0元订单 不进行支付 直接更改订单状态
     * @param $orderNo 订单编号
     * @author jonzhang
     * @date 2017-08-17
     */
    public function payForFree($orderNo)
    {
        if ($this->ProcessDataForOrder($orderNo,7))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /***
     * todo 拼凑微信扫描支付所需要的数据
     * @param $orderId 订单编号
     * @return array
     * @author 张国军 2018年07月12日
     */
    public function processNativeData($orderId=0)
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
        if(empty($orderId))
        {
            $returnData["errCode"]=-101;
            $returnData["errMsg"]="订单编号不能为空";
            return $returnData;
        }
        //查询订单状态
        $selfOrderData=(new SelfOrderService())->getListByCondition(['id'=>$orderId,'current_status'=>0]);
        if($selfOrderData['errCode']==0&&empty($selfOrderData['data']))
        {
            $returnData["errCode"]=-102;
            $returnData["errMsg"]="订单不存在";
            return $returnData;
        }
        else if($selfOrderData['errCode']<0)
        {
            return $selfOrderData;
        }


        if(isset($selfOrderData['data'][0]['status'])&&$selfOrderData['data'][0]['status']!=0)
        {
            $returnData["errCode"]=-103;
            $returnData["errMsg"]="该订单不是待支付订单，不能够进行支付";
            return $returnData;
        }
        $payAmount=$selfOrderData['data'][0]['pay_amount']??0;
        if(empty($payAmount))
        {
            $returnData["errCode"]=-104;
            $returnData["errMsg"]="订单金额为0";
            return $returnData;
        }

        if(empty($selfOrderData['data'][0]['order_no']))
        {
            $returnData["errCode"]=-105;
            $returnData["errMsg"]="订单号不能为空";
            return $returnData;
        }

        $body="会搜云商城-商品";
        $selfOrderDetailData=(new SelfOrderDetailService())->getListByCondition(['self_order_id'=>$orderId,'current_status'=>0]);
        if($selfOrderDetailData['errCode']==0&&!empty($selfOrderDetailData['data']))
        {
            $body=$selfOrderDetailData['data'][0]['product_name']??'-';
            if($selfOrderDetailData['data'][0]['product_version_no']==1)
            {
                $body.="(基础版)";
            }
            else if($selfOrderDetailData['data'][0]['product_version_no']==2)
            {
                $body.="(高级版)";
            }
            else if($selfOrderDetailData['data'][0]['product_version_no']==3)
            {
                $body.="(至尊版)";
            }

        }
        $data['orderNo']=$selfOrderData['data'][0]['order_no'];
        $data['totalFee']=intval($payAmount);
        $data['appId']=config('wechat.native_app_id');
        $data['mchId']=config('wechat.native_mch_id');
        $data['payKey']=config('wechat.native_pay_key');
        $data['body']=$body;
        $data['callBackUrl']=config('app.url') . 'merchants/fee/wechatPay/webNotify';
        if(WXXCXCache::get($data['appId'],'native_pay_check'))
        {
            if(WXXCXCache::get($data['appId'],'native_pay_check')!=$data['payKey'])
            {
                WXXCXCache::delete($data['appId'],'native_pay_check');
            }
        }
        //微信扫码支付appid对应的支付密钥
        if(!WXXCXCache::get($data['appId'],'native_pay_check'))
        {
            WXXCXCache::set($data['appId'],$data['payKey'], 'native_pay_check');
        }
        $result= $this->nativePay($data);
        if (isset($result['data']['return_code'])&&$result['data']['return_code']=='SUCCESS'&&isset($result['data']['result_code'])&&$result['data']['result_code']=='SUCCESS'&&isset($result['data']['code_url']))
        {
                //svg字符串返回到前端
                $returnData['data']=QrCode::size(200)->generate($result['data']['code_url']);
                return $returnData;
        }
        else
        {
            if($result['errCode']<0)
            {
                return $result;
            }
            else
            {
                BLogger::getLogger('info')->info("微信扫码支付，接口返回值：", $result);
                $returnData["errCode"] = -105;
                $returnData["errMsg"] = "微信扫码支付URL错误";
                return $returnData;
            }
        }
    }

    /***
     * todo 微信扫码支付
     * @param array $data
     * @return array
     * @author 张国军 2018年07月12日
     */
    public function nativePay($data=[])
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
        $errMsg="";
        if(empty($data['orderNo']))
        {
            $errMsg.="订单号为空";
        }
        if(empty($data['appId']))
        {
            $errMsg.="appId为空";
        }
        if(empty($data['mchId']))
        {
            $errMsg.="商户号为空";
        }
        if(empty($data['body']))
        {
            $errMsg.="商品描述为空";
        }
        if(empty($data['totalFee']))
        {
            $errMsg.="金额为0";
        }
        if(empty($data['callBackUrl']))
        {
            $errMsg.="回调地址为空";
        }
        if(empty($data['payKey']))
        {
            $errMsg.="支付密钥为空";
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $randomOrderId= $data['orderNo'].'_'.mt_rand(1,99);
        $parameters = [
            'appid' => $data['appId'], //小程序ID
            'mch_id' => $data['mchId'], //商户号
            'nonce_str' => $this->createNoncestr(), //随机字符串
            'body' => $data['body'],//商品描述
            'out_trade_no'=> $randomOrderId,//商户订单号
            'total_fee' => $data['totalFee'],//总金额 单位 分
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //终端IP
            'notify_url' =>$data['callBackUrl'], //通知地址  确保外网能正常访问
            'trade_type' => 'NATIVE'//交易类型
        ];

        //BLogger::getLogger('info')->info('native pay parameter:',$parameters);
        //统一下单签名
        $parameters['sign'] = $this->getSign($parameters,$data['payKey']);
        $xmlData = $this->arrayToXml($parameters);
        $returnData['data'] = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
        return $returnData;
    }
}