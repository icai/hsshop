<?php

namespace App\Services\Foundation;


use App\Events\OrderPayedEvent;
use App\Jobs\CheckGrantCard;
use App\Jobs\Distribution;
use App\Jobs\ImportOrderLogistics;
use App\Jobs\sendCodeKeyProduct;
use App\Jobs\SendRechargeBalanceLog;
use App\Jobs\SendTakeAway;
use App\Lib\Redis\CouponLogRedis;
use App\Module\GroupsRuleModule;
use App\Module\MessagePushModule;
use App\Module\WeChatAuthModule;
use App\S\Market\CouponLogService;
use App\S\MarketTools\MessagesPushService;
use Event;
use App\Module\WheelModule;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;
use App\S\Wechat\ALiPayService;
use App\S\Wechat\WeChatShopConfService;
use DB;
use Log;
use MemberCardRecordService;
use OrderLogService;
use OrderService;
use RechargeLogService;
use RechargeService;
use WeixinService;
use App\S\BalanceLogService;
use App\S\BalanceRuleService;
use PointRecordService;

/**
 * 省市区
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年3月30日 15:19:44
 */
class PaymentService
{
    /**
     * 订单数据一致性和状态验证
     *
     * @param  array $orderList [订单数据]
     * @return boolean
     * @update 何书哲 2018年8月20日 0元订单不走支付（提交订单金额0状态待付款的订单）数据一致性和状态验证的"订单异常"bug修复
     */
    public function orderDatasVerify($orderList)
    {
        // 合并支付才需要验证数据一致性
        if (count($orderList) > 1) {
            $consistencyDatas = ['trade_id', 'pay_way', 'wid', 'mid', 'status'];
            foreach ($consistencyDatas as $value) {
                if (count(array_unique(array_column($orderList, $value))) > 1) {
                    return false;
                }
            }
        }
        //何书哲 2018年8月20日 0元订单不走支付（提交订单金额0状态待付款的订单）数据一致性和状态验证的"订单异常"bug修复
        return get_numeric($orderList[0]['status']) === 0 || $orderList[0]['pay_price'] == 0 && get_numeric($orderList[0]['status']) === 1;
    }

    /**
     * 根据订单自增id获取订单数据
     *
     * @param  integer|array $id [订单自增id ds_order.id 或 id数组]
     * @return void
     */
    public function getOrderList($id)
    {
        list($orderList) = OrderService::init('wid', session('wid'))->getList(false, is_array($id) ? $id : [$id]);

        if (count($orderList['data']) && $this->orderDatasVerify($orderList['data'])) {
            return $orderList['data'];
        }

        error('订单异常');
    }

    /**
     * 获取会员信息
     *
     * @param  integer $mid [会员id]
     * @return array
     */
    public function getMemberInfo($conf)
    {
        if ($conf['type'] == 1) {
            $data = (new MemberService())->getRowById(session('mid'));
        } else {
            $data = (new UnifiedMemberService())->getRowById(session('umid'));
        }
        return $data['openid'];
    }

    public function payBalance($id)
    {
        $balanceLogService = new BalanceLogService();
        $order = $balanceLogService->getRowById($id);
        if (empty($order)) {
            return myerror('订单不存在');
        }
        if ($order['status'] == 1) {
            return myerror('订单已完成');
        }
        $wid = $order['wid'];
        $conf = (new WeChatAuthModule())->getConf($wid);

        // 定义页面展示所需数据
        $order['id'] = 'balance' . $order['id'];
        $detail['tradeId'] = $order['trade_id'];  //todo
        $detail['payTotal'] = $order['money'] / 100;
        $detail['payee'] = $conf['payee'];
        $detail['id'] = $order['id'];

        /******* 统一下单(获取预支付交易会话标识) 开始 *******/
        // 公众账号ID 微信支付分配的公众账号ID（企业号corpid即为此appId）
        $parameters['appid'] = $conf['app_id'];
        // 商户号 微信支付分配的商户号
        $parameters['mch_id'] = $conf['mch_id'];
        // 随机字符串 长度要求在32位以内
        $parameters['nonce_str'] = $this->createNoncestr();
        // 商品描述 微信浏览器公众号支付此参数官方文档制定规则：商家名称-销售商品类目
        $parameters['body'] = mb_substr('测试文字', 0, 40, 'utf-8');
        // 附加数据 在查询API和支付通知中原样返回，可作为自定义参数使用
        $parameters['attach'] = $order['id'] . '#' . $conf['type'];
        // 商户订单号 商户系统内部订单号，要求32个字符内、且在同一个商户号下唯一
        $parameters['out_trade_no'] = $order['trade_id'] . '_' . rand(); //拼接随机数，防止修改订单总价后商户订单号重复
        // 标价金额 订单总金额，单位为分
        $parameters['total_fee'] = intval(strval($detail['payTotal'] * 100));
        // 终端IP APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP
        $parameters['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        // 交易起始时间 订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010
        // $parameters['time_start'] = '20170401202734';
        // 交易结束时间 注意：最短失效时间间隔必须大于5分钟 订单失效时间，格式为yyyyMMddHHmmss如2009年12月27日9点10分10秒表示为20091227091010
        // $parameters['time_expire'] = '20170401202734';
        // 通知地址 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
        $parameters['notify_url'] = config('app.url') . 'foundation/payment/wechatPayNotify';
        // 交易类型 取值如下：JSAPI，NATIVE，APP等
        $parameters['trade_type'] = 'JSAPI';
        // 买家微信openid
        $parameters['openid'] = $this->getMemberInfo($conf);
        // 签名 通过签名算法计算得出的签名值
        $parameters['sign'] = $this->getSign($parameters, $conf);
        // 数组转xml
        $xml = $this->arrayToXml($parameters);
        // 以post方式提交xml到统一下单接口
        $result = $this->postXmlCurl($xml, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        // xml转数组
        $result = $this->xmlToArray($result);

        $result['return_code'] = $result['return_code'] ?? '';
        $result['result_code'] = $result['result_code'] ?? '';
        $result['return_msg'] = $result['return_msg'] ?? '未知错误';
        $result['err_code_des'] = $result['err_code_des'] ?? '未知错误';

        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            $prepay_id = $result['prepay_id'];
        } elseif ($result['return_code'] === 'FAIL') {
            error($result['return_msg']);
        } elseif ($result['result_code'] === 'FAIL') {
            error($result['err_code_des']);
        } else {
            error('通信失败');
        }
        /******* 统一下单(获取预支付交易会话标识) 结束 *******/

        /******* 设置jsapi的参数 开始 *******/
        // 公众号id 商户注册具有支付权限的公众号成功后即可获得
        $jsApi['appId'] = $conf['app_id'];
        // 时间戳 当前的时间
        $jsApi['timeStamp'] = strval(time());
        // 随机字符串 不长于32位
        $jsApi['nonceStr'] = $this->createNoncestr();
        // 订单详情扩展字符串 统一下单接口返回的prepay_id参数值，提交格式如：prepay_id=***
        $jsApi['package'] = 'prepay_id=' . $prepay_id;
        // 签名方式 签名算法，暂支持MD5
        $jsApi['signType'] = 'MD5';
        // 签名 通过签名算法计算得出的签名值
        $jsApi['paySign'] = $this->getSign($jsApi, $conf);
        /* 数组转json */
        $jsApi = json_encode($jsApi);
        /******* 设置jsapi的参数 结束 *******/

        // 响应支付展示页面
        response()->view('foundation.payment.shopWechatPay', [
            'jsApi' => $jsApi,
            'detail' => $detail,
        ])->send();

        return true;
    }

    /**
     * 根据对应支付方式调取不同的支付方法
     *
     * @param  integer|array $id [订单自增id ds_order.id 或 id数组]
     * @return mixed
     */
    public function pay($id, $payment, $wid = 0)
    {
        $orderList = $this->getOrderList($id);
        /**
         * 根据对应支付方式调取不同的支付方法
         *
         * 支付方式：1微信支付；2支付宝支付；3储值余额支付；4货到付款/到店付款；5找人代付；6微信代销；7支付宝代销；8银行卡支付；9会员卡支付
         */
        switch ($payment) {
            case '1':
                $this->wechatPay($orderList);
                break;
            case '2':
                $this->alipay($orderList);
                break;
            case '3':
                return $this->cardPay($orderList);
                break;
            case '9':
                $this->cardPay($orderList, $wid);
                break;
            default:
                error('暂不支持该支付方式');
                break;
        }
    }

    /**
     * 0元订单支付
     * @param $id int 订单id
     */
    public function payForFree($id)
    {
        // 获取订单数据
        $orderList = $this->getOrderList($id);

        //支付
        if ($this->paySuccessSubsequent($id, 7)) {

            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取微信支付配置信息
     *
     * @param   integer $wid [店铺id]
     * @return  array
     */
    public function getWechatConf($wid)
    {
        $conf = (new WeChatShopConfService())->getConfigByWid($wid);
        if (!$conf || $conf['status'] == 0) {
            error('微信支付未开通');
        }
        $verify = ['payee', 'app_id', 'app_secret', 'mch_id', 'mch_key'];
        foreach ($verify as $value) {
            if (!isset($conf[$value]) || empty($conf[$value])) {
                error('微信支付配置错误');
            }
        }
        return $conf;
    }

    /**
     * 微信支付
     *
     * 响应一个待支付页面
     *
     * @return boolean
     */
    public function wechatPay($orderList)
    {
        // 获取微信支付配置信息
        $conf = (new WeChatAuthModule())->getConf($orderList[0]['wid']);

        // 定义页面展示所需数据
        $detail['tradeId'] = $orderList[0]['trade_id'];
        $detail['payTotal'] = array_sum(array_column($orderList, 'pay_price'));
        //Herry 处理金额精度问题
        $detail['payTotal'] = sprintf("%.2f", $detail['payTotal']);
        $detail['payee'] = $conf['payee'];
        $detail['id'] = $orderList[0]['id'];

        /******* 统一下单(获取预支付交易会话标识) 开始 *******/
        // 公众账号ID 微信支付分配的公众账号ID（企业号corpid即为此appId）
        $parameters['appid'] = $conf['app_id'];
        // 商户号 微信支付分配的商户号
        $parameters['mch_id'] = $conf['mch_id'];
        // 随机字符串 长度要求在32位以内
        $parameters['nonce_str'] = $this->createNoncestr();
        // 商品描述 微信浏览器公众号支付此参数官方文档制定规则：商家名称-销售商品类目
        $parameters['body'] = mb_substr($orderList[0]['orderDetail'][0]['title'], 0, 40, 'utf-8');
        // 附加数据 在查询API和支付通知中原样返回，可作为自定义参数使用
        $parameters['attach'] = implode(',', array_column($orderList, 'id')) . '#' . $conf['type'];
        // 商户订单号 商户系统内部订单号，要求32个字符内、且在同一个商户号下唯一
        $parameters['out_trade_no'] = $orderList[0]['id'] . '_' . rand(); //拼接随机数，防止修改订单总价后商户订单号重复
        // 标价金额 订单总金额，单位为分
        $parameters['total_fee'] = intval(strval($detail['payTotal'] * 100));
        // 终端IP APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP
        $parameters['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        // 交易起始时间 订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010
        // $parameters['time_start'] = '20170401202734';
        // 交易结束时间 注意：最短失效时间间隔必须大于5分钟 订单失效时间，格式为yyyyMMddHHmmss如2009年12月27日9点10分10秒表示为20091227091010
        // $parameters['time_expire'] = '20170401202734';
        // 通知地址 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
        $parameters['notify_url'] = config('app.url') . 'foundation/payment/wechatPayNotify';
        // 交易类型 取值如下：JSAPI，NATIVE，APP等
        $parameters['trade_type'] = 'JSAPI';
        // 买家微信openid
        $parameters['openid'] = $this->getMemberInfo($conf);
        // 签名 通过签名算法计算得出的签名值
        $parameters['sign'] = $this->getSign($parameters, $conf);
        // 数组转xml
        $xml = $this->arrayToXml($parameters);
        // 以post方式提交xml到统一下单接口
        $result = $this->postXmlCurl($xml, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        // xml转数组
        $result = $this->xmlToArray($result);

        $result['return_code'] = $result['return_code'] ?? '';
        $result['result_code'] = $result['result_code'] ?? '';
        $result['return_msg'] = $result['return_msg'] ?? '未知错误';
        $result['err_code_des'] = $result['err_code_des'] ?? '未知错误';

        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            $prepay_id = $result['prepay_id'];
        } elseif ($result['return_code'] === 'FAIL') {
            error($result['return_msg']);
        } elseif ($result['result_code'] === 'FAIL') {
            error($result['err_code_des']);
        } else {
            error('通信失败');
        }
        /******* 统一下单(获取预支付交易会话标识) 结束 *******/

        /******* 设置jsapi的参数 开始 *******/
        // 公众号id 商户注册具有支付权限的公众号成功后即可获得
        $jsApi['appId'] = $conf['app_id'];
        // 时间戳 当前的时间
        $jsApi['timeStamp'] = strval(time());
        // 随机字符串 不长于32位
        $jsApi['nonceStr'] = $this->createNoncestr();
        // 订单详情扩展字符串 统一下单接口返回的prepay_id参数值，提交格式如：prepay_id=***
        $jsApi['package'] = 'prepay_id=' . $prepay_id;
        // 签名方式 签名算法，暂支持MD5
        $jsApi['signType'] = 'MD5';
        // 签名 通过签名算法计算得出的签名值
        $jsApi['paySign'] = $this->getSign($jsApi, $conf);
        /* 数组转json */
        $jsApi = json_encode($jsApi);
        /******* 设置jsapi的参数 结束 *******/

        // 响应支付展示页面
        response()->view('foundation.payment.shopWechatPay', [
            'jsApi' => $jsApi,
            'detail' => $detail,
        ])->send();

        return true;
    }

    /**
     * [wechatPayNotify 微信支付异步回调]
     * @return [type] [description]
     * @update 许立 2018年11月09日 异常回调数据记录日志
     */
    public function wechatPayNotify()
    {
        $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : ''; //拿到微信回调回来的信息判断支付成功没

        if (empty($streamData)) {
            $streamData = file_get_contents('php://input');
        }

        if ($streamData != '') {
            try {
                $exception = false;
                $streamData = $this->xmlToArray($streamData);
            } catch (\Exception $e) {
                $exception = true;
                Log::info('微信支付回调数据异常: ' . $e->getMessage());
            }
            if (!isset($streamData['return_code'])) {
                $exception = true;
                Log::info('微信支付回调数据没有return_code等字段');
            }
            if ($exception) {
                echo 'fail';
                exit;
            }
            $orderIds = explode(',', $streamData['attach']);
            if ($streamData['return_code'] == 'SUCCESS' && $streamData['result_code'] == 'SUCCESS') { //支付成功
                try {
                    //支付成功，执行订单操作
                    $this->paySuccessSubsequent($orderIds, 1, $streamData);
                    Log::info('微信支付回调成功[订单ids: ' . $streamData['attach'] . ']');
                    echo 'success';

                } catch (\Exception $e) {
                    //支付失败，返回日志记录
                    Log::info($e->getMessage());
                    echo 'fail';
                }

            }
        } else {
            echo 'fail';
        }

    }

    public function wechatRechargeNotify()
    {

    }

    /**
     * 获取店铺支付宝配置信息
     *
     * @return array
     */
    public function getAlipayConf($wid)
    {
        $conf = (new ALiPayService())->getConfByWid($wid);
        if (empty($conf) || $conf['status'] == 0) {
            error('支付宝支付未开通');
        }
        $verify = ['payee', 'seller_id', 'partner', 'key'];
        foreach ($verify as $value) {
            if (!isset($conf[$value]) || empty($conf[$value])) {
                error('支付宝支付配置错误');
            }
        }
        return $conf;

    }

    /**
     * 支付宝支付
     *
     * 响应一个待支付页面
     *
     * @return boolean
     */
    public function alipay($orderList)
    {
        // 获取支付宝支付配置信息
        $conf = $this->getAlipayConf($orderList[0]['wid']);

        // 合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
        $parameters['partner'] = $conf['partner'];
        // 收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
        $parameters['seller_id'] = $conf['seller_id'];
        // MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
        $parameters['key'] = $conf['key'];
        // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $parameters['notify_url'] = config('app.url') . '/foundation/payment/alipayNotify/' . session('wid');
        // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $alipay_config['return_url'] = config('app.url') . '/foundation/payment/shopPaySuccess/' . session('wid');
    }

    /**
     * 支付宝支付异步通知
     *
     * @return [type] [description]
     */
    public function alipayNotify()
    {

    }

    /**
     * 支付宝充值异步通知
     *
     * @return [type] [description]
     */
    public function alipayRechargeNotify()
    {

    }

    /**
     * 会员卡支付
     * @param  array $orderList 订单详情
     * @return [type]   [description]
     * @author  吴晓平
     */
    public function cardPay($orderList)
    {
        //下单用户id
        $mid = $orderList[0]['mid'];
        $payTotal = array_sum(array_column($orderList, 'pay_price'));
        $payTotal *= 100;
        $wid = $orderList[0]['wid'];
        $data = (new MemberService())->getRowById($mid);
        if ($data['money'] < $payTotal) {
            Log::info('会员卡余额不足,请重新选择支付方式');
            return false;
        }
        try {
            DB::beginTransaction();
            $data = (new MemberService())->operateMoney($mid, '-' . $payTotal);

            if ($data['errCode'] != 0) {
                throw new \Exception('会员卡余额不足,请重新选择支付方式');
            }

            $BalanceLogService = new BalanceLogService();
            $BalanceLogService->insertLog($wid, $mid, $payTotal / 100, 3, 2, 1);
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            //事务回滚
            DB::rollBack();
            return false;
        }

        if ($this->paySuccessSubsequent($orderList[0]['id'], 3, ['cash_fee' => $orderList[0]['pay_price']]) === true) {
            return true;
        } else {
            return false;
        }
    }

    //@update 梅杰 增加卡密商品自动发货
    public function balanceSuccessSubsequent($balanceId, $payWay = 0, $orderData = [])
    {
        try {
            //事务开始 处理数据库逻辑
            DB::beginTransaction();

            //订单状态改为待发货
            $saveData = [];
            $saveData['status'] = 1;

            //修改支付方式
            $saveData['pay_way'] = $payWay;

            //交易流水号
            if ($payWay == 1) {
                //微信支付
                $saveData['serial_id'] = $orderData['transaction_id'];
            } elseif ($payWay == 2) {
                //支付宝支付
                $saveData['serial_id'] = $orderData['trade_no'];
            }
            $BalanceLogService = new BalanceLogService();
            $order = $BalanceLogService->getRowById(str_replace('balance', '', $balanceId));

            //余额充值发送到日志服务器
            $job = new SendRechargeBalanceLog($order['wid'], $order['mid'], $order['money'] / 100, $order['type'], $payWay, time());
            dispatch($job->onQueue('dsBalance'));

            if ($order['status'] == 1) {
                return true;
            }
            if ($order['type'] == 12) {
                dispatch((new sendCodeKeyProduct($order['wid'], $order['mid'], $order['id']))->onQueue('sendCdKey'));
            }
            //改变订单状态 代发货状态
            if (!($BalanceLogService->updateDataStatusOk($balanceId, $saveData))) {
                throw new \Exception('修改订单状态失败');
            }
            (new MemberService())->operateMoney($order['mid'], $order['money']);

            $score = (new BalanceRuleService())->checkMoneyAddScore($order['wid'], $order['money']);
            if ($score > 0) {
                $input = [
                    'wid' => $order['wid'],
                    'mid' => $order['mid'],
                    'point_type' => 10,
                    'is_add' => 1,
                    'score' => $score,
                ];
                (new MemberService())->incrementScore($order['mid'], $score);
                PointRecordService::insertData($input);
            }
            //增加
            //事务提交 处理redis逻辑
            DB::commit();
            Event::fire(new OrderPayedEvent($order['id']));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            //事务回滚
            DB::rollBack();
            return false;
        }
        return true;
    }

    /**
     * 支付成功后续操作
     *
     * 1、改变订单状态
     * 2、修改商品库存（或者规格库存）
     * 3、优惠券标记为已使用
     * 4、数据统计相关操作（商品购买数量统计、用户消费统计）
     * 5、发送通知（站内信、微信模版消息、短信）
     * 6、订单操作记录新增数据
     *
     * @param int | array $orderId 订单id
     * @param int $payWay 支付方式
     * @param array $orderData 支付回调信息数组
     * @return boolean
     * @update 何书哲 2018年6月27日 添加订单打单导入任务队列
     * @update 梅杰 2018年8月6日 增加卡密商品处理队列
     * @update 张永辉 2018年8月23日 添加发放会员卡到异步队列中
     * @update 何书哲 2018年11月15日 外卖订单支付成功添加到队列中
     */
    public function paySuccessSubsequent($orderId, $payWay = 0, $orderData = [])
    {
        $mid = 0;
        try {
            //事务开始 处理数据库逻辑
            DB::beginTransaction();

            //转化为数组
            if (!is_array($orderId)) {
                $orderId = [intval($orderId)];
            }

            //订单状态改为待发货
            $saveData = [];
            $saveData['status'] = 1;

            //修改支付方式
            $saveData['pay_way'] = $payWay;
            //设置是否是代销
            if (isset($orderData['attach'])) {
                $type = explode('#', $orderData['attach']);
                $type = $type[1] ?? 0;
                if ($type == 2) {
                    $saveData['is_deposit'] = 1;
                }
            }

            //交易流水号
            if ($payWay == 1) {
                //微信支付
                $saveData['serial_id'] = $orderData['transaction_id'];
                $saveData['cash_fee'] = $orderData['cash_fee'] / 100;
            } elseif ($payWay == 2) {
                //支付宝支付
                $saveData['serial_id'] = $orderData['trade_no'];
            } elseif ($payWay == 3) {
                $saveData['serial_id'] = 3;
                //add MayJay
                $saveData['cash_fee'] = $orderData['cash_fee'];
            }

            //获取订单数据
            list($orderList) = OrderService::init()->where([1 => 1])->getList(false, $orderId);
            $wid = $orderList['data'][0]['wid'];
            //改变订单状态 代发货状态
            if (!(OrderService::init('wid', $orderList['data'][0]['wid'])->where(['id' => ['in', $orderId]])->updateD($saveData, false))) {
                throw new \Exception('修改订单状态失败');
            }


            //订单操作记录新增数据
            foreach ($orderList['data'] as $k => $v) {
                if (1 === $v['status'] || 7 === $v['status']) {
                    return true;
                }
                $mid = $v['mid'];
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
                //暂时保存新增订单操作记录 后面更新redis使用
                $redisOrder = OrderService::init('wid', $v['wid'])->where([])->getInfo($v['id']);
                if (isset($redisOrder['orderLog'])) {
                    $orderList['data'][$k]['orderLog'] = $redisOrder['orderLog'];
                }
                $orderList['data'][$k]['orderLog'][] = $insertData;
                //团购订单进行 add by zhangyh
                if ($v['groups_id'] != 0) {
                    $groups = new GroupsRuleModule();
                    $groups->afterOrder($v);
                    //add MayJay 团订单支付成功
                    (new MessagePushModule($wid, MessagesPushService::ActivityGroup))->sendMsg(['oid' => $v['id'], 'group_type' => 'new_group']);
                }
                $job = (new Distribution($v))->onQueue('Distribution');
                dispatch($job);
                //add end
                //如果是含有卡密密商品自动发货
                if ($v['type'] == 12) {
                    dispatch((new sendCodeKeyProduct($v['wid'], $v['mid'], $v['id']))->onQueue('sendCdKey'));
                }
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
                if (!(OrderService::init('wid', $orderList['data'][0]['wid'])->updateR($id, $saveData, false))) {
                    //return myerror('修改订单状态失败');
                    Log::info('[PaymentService]支付后续 redis 修改订单状态失败');
                }
            }

            //订单操作记录新增数据
            foreach ($orderList['data'] as $k => $v) {
                if (!(OrderService::init('wid', $orderList['data'][0]['wid'])->updateR($v['id'], ['orderLog' => json_encode($v['orderLog'])], false))) {
                    //return myerror('订单操作记录新增数据失败');
                    Log::info('[PaymentService]支付后续 redis 订单操作记录新增数据失败');
                }
            }
            //优惠券标记为已使用
            $couponLogRedis = new CouponLogRedis();
            foreach ($orderId as $id) {
                $couponLog = $couponLogService->getRowByOid($id);
                if ($couponLog) {
                    $couponLogRedis->updateRow(['id' => $couponLog['id'], 'status' => 2]);
                    Log::info('[PaymentService]支付后续 redis 修改优惠券状态失败');
                }
                //何书哲 2018年6月27日 订单打单导入任务队列
                dispatch((new ImportOrderLogistics($wid, $id))->onQueue('ImportOrderLogistics'));

                //何书哲 2018年11月15日 外卖订单导入第三方
                dispatch((new SendTakeAway($id)));
            }
            //检测发放会员卡
            dispatch((new CheckGrantCard($mid, $wid))->onQueue('CheckGrantCard'));


        } catch (\Exception $e) {
            Log::info($e->getMessage());
            //事务回滚
            DB::rollBack();
            return false;
        }

        //关联商品大转盘参加次数增加
        try {
            foreach ($orderId as $oid) {
                (new WheelModule())->addTime($oid, $mid);
                Event::fire(new OrderPayedEvent($oid));
            }
        } catch (\Exception $e) {
            \Log::info('大转盘关联商品添加次数更新错误' . $e->getMessage());
        }
        return true;
    }


    /**
     * 充值成功后续操作
     *
     * 1、改变订单状态
     * 2、增加用户余额（或者会员卡余额）
     * 3、充值记录新增数据
     * 4、发送通知（站内信、微信模版消息、短信）
     * 5、数据统计相关操作（如果有的话）
     *
     * @return [type] [description]
     */
    public function rechargeSuccessSubsequent($recharge_sn)
    {
        try {
            //事务开始 处理数据库逻辑
            DB::beginTransaction();

            //获取充值订单
            $wid = session('wid');
            $recharge = RechargeService::init('wid', $wid)->where(['recharge_sn' => $recharge_sn])->getInfo();
            if (empty($recharge)) {
                throw new \Exception('充值订单不存在');
            }

            //改变充值订单状态
            if (!(RechargeService::init('wid', $wid)->where(['id' => $recharge['id']])->updateD(['status' => 1], false))) {
                throw new \Exception('更新充值订单失败');
            }

            //充值记录新增数据
            $insertData = [
                'recharge_id' => $recharge['id'],
                'wid' => $wid,
                'mid' => $recharge['mid'],
                'remark' => '充值订单id: ' . $recharge['id'] . ', 充值编号: ' . $recharge_sn . ', 付款方式: ' . $recharge['pay_way']
            ];
            $logId = RechargeLogService::init()->addD($insertData, false);
            if (!$logId) {
                throw new \Exception('增加充值订单操作记录失败');
            }
            $insertData['id'] = $logId;
            //暂时保存新增订单操作记录 后面更新redis使用
            $recharge['rechargeLog'][] = $insertData;

            //增加会员卡余额
            $where = [
                'mid' => $recharge['mid'],
                'card_id' => $recharge['card_id']
            ];
            $cardRecord = MemberCardRecordService::init('wid', $wid)->where($where)->getInfo();
            if (!$cardRecord) {
                throw new \Exception('会员卡领取记录不存在');
            }
            if (!(MemberCardRecordService::init('wid', $wid)->incrementD($cardRecord['id'], 'balance', false, $cardRecord['money']))) {
                throw new \Exception('会员卡充值金额增加失败');
            }

            //事务提交 处理redis逻辑
            DB::commit();

            //改变充值订单状态 订单操作记录新增数据
            $updateData = [
                'status' => 1,
                'rechargeLog' => json_encode($recharge['rechargeLog'])
            ];
            if (!(RechargeService::init('wid', $wid)->updateR($recharge['id'], $updateData, false))) {
                throw new \Exception('更新充值订单失败');
            }

            //增加会员卡余额
            if (!(MemberCardRecordService::init('wid', $wid)->incrementR($cardRecord['id'], 'balance', false, $cardRecord['money']))) {
                throw new \Exception('会员卡充值金额增加失败');
            }

        } catch (\Exception $e) {
            //事务回滚
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 计算分销利润
     *
     * @return [type] [description]
     */
    public function calculateCommission()
    {

    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param  xml $xml [请求参数]
     * @param  String $url [请求地址]
     * @param  Integer $second [超时时间]
     * @return mixed
     */
    public function postXmlCurl($xml, $url, $second = 30)
    {
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        // 运行curl
        $data = curl_exec($ch);
        // 返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo 'curl出错，错误码:' . $error;
            curl_close($ch);
            return false;
        }
    }

    /**
     * xml转array
     *
     * @param  [Xml]   $xml [要转成数组的xml]
     * @return [Array]      [转化后的数组]
     * @udpate 张永辉 2018年7月6日 禁止引用外部xml实体
     */
    public function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * array转xml
     *
     * @param  [Array] $arr [要转成xml的数组]
     * @return [Xml]        [转化后的xml]
     */
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 格式化参数，签名过程需要使用
     *
     * @param  [Array] $arr   [description]
     * @return [type]         [description]
     */
    public function formatBizQueryParaMap($arr)
    {
        $buff = '';
        foreach ($arr as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        return $buff;
    }

    /**
     * 生成32位随机字符串
     *
     * @return [String] [32位随机字符串]
     */
    public function createNoncestr()
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = '';
        for ($i = 0; $i < 32; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 生成签名
     *
     * @param  [Array] $parameters  [参数数组]
     * @param  [Array] $conf        [配置参数数组]
     * @return [String]             [签名字符串]
     */
    public function getSign($parameters, $conf)
    {
        /* 对参数按照key=value的格式，并按照参数名ASCII字典序排序如下 */
        foreach ($parameters as $key => $value) {
            $signParam[$key] = $value;
        }
        /* 按字典序排序参数 */
        ksort($signParam);
        $signParam = $this->formatBizQueryParaMap($signParam);
        /* 拼接API密钥 */
        $signParam .= 'key=' . $conf['mch_key'];
        /* MD5加密 */
        $signParam = md5($signParam);
        /* 所有字符转为大写 */
        $signParam = strtoupper($signParam);

        return $signParam;
    }

    /**
     * 格式化字符串
     *
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function trimString($value)
    {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }


    /**
     * 验证微信回调签名是否正确
     * @param $data 异步回调数据
     * @author 张永辉 2018年7月6日
     */
    public function checkSign($data, $id, $type = 'order')
    {

        if ($type == 'order') {
            $order = OrderService::init()->model->find($id);
            $wid = $order->wid;
        } elseif ($type == 'balance') {
            $BalanceLogService = new BalanceLogService();
            $order = $BalanceLogService->getRowById(str_replace('balance', '', $id));
            $wid = $order['wid'];
        }
        $conf = (new WeChatAuthModule())->getConf($wid);
        $sign = $this->makeSign($data, $conf);
        if ($data['sign'] == $sign) {
            return true;
        } else {
            \Log::info('订单回调签名错误,返回信息:');
            \Log::info($data);
            \Log::info($sign);
            return false;
        }
    }


    /**
     *  微信灰顶生成签名
     * @param $data 返回数据
     * @param $conf 店铺配置信息
     * @return string 签名
     * @author 张永辉 2018年7月6日
     */
    public function makeSign($data, $conf)
    {
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string = $this->ToUrlParams($data);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $conf['mch_key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $data 数组
     * @return string
     * @author 张永辉 2018年7月6日
     */
    public function toUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }


}
