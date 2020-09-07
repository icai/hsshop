<?php
/**
 * Created by zhangyh.
 * User: 张永辉 [zhangyh_private@foxmail.com]
 * Date: 2019/9/26 14:58
 */

namespace App\Module\ByteDance;


use App\Model\AliappConfig;
use App\Model\ByteDanceConfig;
use App\Model\Member;
use App\Model\Order;
use App\Model\WeixinConfigSub;
use App\Module\AliApp\AliClientModule;
use App\Module\AliApp\AlipayTradeAppPayRequest;
use XCXPaymentModule;
use App\Services\Foundation\PaymentService;

class PayModule
{

    /**
     * @desc 订单对象
     *
     * @var object
     */
    public $order;


    /**
     * @desc 输入信息
     *
     * @var array
     */
    public $input;

    /**
     * @desc 获取支付
     * @return array
     * @throws \Exception
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 29 日
     */
    public function getPay()
    {
        $this->order = Order::with('orderDetail')->find($this->input['id']);
        if (!$this->order) {
            throw new \Exception('订单不存在');
        }
        if ($this->order->pay_price <= 0) {
            if (XCXPaymentModule::payForFree($this->order->id)) {
                xcxerror('支付成功', 40013, $this->order->id);
            } else {
                xcxerror('操作失败');
            }
        }
        if (!empty($this->input['payment']) && $this->input['payment'] == 3) {
            if (PaymentService::pay($this->order->id, 3) === true) {
                xcxerror('支付成功', 40013, $this->order->id);
            } else {
                xcxerror('操作失败');
            }
        }

        $aliInfo = $wechatInfo = '';
        try {
            $aliInfo = $this->getAliPay();
           // $wechatInfo = $this->getWechatPay();
        } catch (\Exception $exception) {
            $exception->getMessage();
        }
        $config = $this->getByteDancePayConf();
        $memberData = Member::select(['id', 'byte_openid'])->find($this->order->mid);
        if (!$config) {
            throw new \Exception('没有配置字节跳动支付');
        }
        $result = [
            'app_id'       => $config['pay_appid'],
            'merchant_id'  => $config['merchant_id'],
            'timestamp'    => strval(time()),
            'sign_type'    => 'MD5',
            'out_order_no' => strval($this->order->oid),
            'total_amount' => strval($this->order->pay_price * 100),
            'product_code' => 'pay',
            'payment_type' => 'direct',
            'trade_type'   => 'H5',
            'version'      => '2.0',
            'currency'     => 'CNY',
            'subject'      => $this->order->orderDetail()->first()->title,
            'body'         => $this->order->orderDetail()->first()->title,
            'uid'          => $memberData->byte_openid,
            'trade_time'   => strval(strtotime($this->order->created_at)),
            'valid_time'   => '3600',
            'notify_url'   => 'https://hsshop2.huisou.cn/aliapp/payment/aliPayNotify',
            'wx_type'      => 'MWEB',
            'wx_url'       => $wechatInfo,
            'alipay_url'   => $aliInfo,
        ];

        $result['sign'] = $this->getBytePaySign($result, $config['pay_secret']);
        $result['risk_info'] = json_encode(['id' => $_SERVER['REMOTE_ADDR']]);
        return $result;

    }

    /**
     * @desc 获取字节跳动数组
     * @param $parameters 签名数组
     * @param $secret 秘钥
     * @return string 签名
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 27 日
     */
    public function getBytePaySign($parameters, $secret)
    {
        foreach ($parameters as $key => $value) {
            if ($value) {
                $signParam[$key] = $value;
            }
        }
        ksort($signParam);
        $signParam = rtrim($this->formatBizQueryParaMap($signParam), '&');
        $signParam .= $secret;
        $signParam = md5($signParam);
        return $signParam;
    }

    /**
     * @desc 拼接签名数组
     * @param $arr 数组
     * @return string 拼接数组
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 27 日
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
     * @desc 获取微信支付信息
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 26 日
     */
    public function getWechatPay()
    {
        $paymentService = new PaymentService();
        $parameters['appid'] = 'wx8384b791cbce04a6';
        $parameters['mch_id'] = '1510813951';
        $parameters['nonce_str'] = $paymentService->createNoncestr();
        $parameters['body'] = mb_substr($this->order->orderDetail->first()->title, 0, 40, 'utf-8');

        $parameters['attach'] = $this->order->id . '#' . 2;
        $parameters['out_trade_no'] = $this->order->id . '_' . rand();

        $parameters['total_fee'] = intval(strval($this->order->pay_price * 100));
        $parameters['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $parameters['notify_url'] = config('app.url') . 'foundation/payment/wechatPayNotify';
        $parameters['trade_type'] = 'MWEB';
        $parameters['sign'] = $this->getWechatSign($parameters);

        $xml = $paymentService->arrayToXml($parameters);
        $result = $paymentService->postXmlCurl($xml, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        $result = $paymentService->xmlToArray($result);
        $result['return_code'] = $result['return_code'] ?? '';
        $result['result_code'] = $result['result_code'] ?? '';
        $result['return_msg'] = $result['return_msg'] ?? '未知错误';
        $result['err_code_des'] = $result['err_code_des'] ?? '未知错误';
        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            return $result['mweb_url'];
        } else {
            throw new \Exception('微信下单失败');
        }

    }

    /**
     * @desc 微信加密秘钥
     * @param $parameters 加密参数
     * @return string秘钥
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 29 日
     */
    public function getWechatSign($parameters)
    {
        /* 对参数按照key=value的格式，并按照参数名ASCII字典序排序如下 */
        foreach ($parameters as $key => $value) {
            $signParam[$key] = $value;
        }
        /* 按字典序排序参数 */
        ksort($signParam);
        $signParam = $this->formatBizQueryParaMap($signParam);
        /* 拼接API密钥 */
        $signParam .= 'key=' . 'huisou123huisou321yingsou3211353';
        /* MD5加密 */
        $signParam = md5($signParam);
        /* 所有字符转为大写 */
        $signParam = strtoupper($signParam);

        return $signParam;
    }


    /**
     * @desc 获取支付宝支付信息
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 26 日
     */
    public function getAliPay()
    {
        $notify_url = config('app.url') . 'aliapp/payment/aliPayNotify';
        $title = '';
        foreach ($this->order->orderDetail as $val) {
            $title .= $val->title;
        }
        $requestArr = [
            'body'            => $title,
            'subject'         => '支付宝订单支付',
            'out_trade_no'    => $this->order->id,
            'timeout_express' => '30m',
            'total_amount'    => floatval($this->order->pay_price),
            'product_code'    => 'QUICK_MSECURITY_PAY'
        ];

        $aliClientModule = new AliClientModule();
        $aliClientModule->appId = '2018072360816197';
        $aliClientModule->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAruHh6/TQzEsd8xOrWJykAc4yc5VxKLDNht/o5MOVuKg3HaTmwykC/WD9PStkIhiU4Q/PkHKY5RXVnSuZe0s7SsGyXYtc24clCP+Re7obRn7TE6CmprPppL9WsjzVcqzeTe1AKpWJcG0M8V0zKsI4gNV+9NYWPHA7rWv4M7ierOUNjx0vCQB4xbMoNmnKvaZnuxb25ooIhtPex1nnyKQUIFcvX86XB1tqyL3I0uga3epJGB6iQfbCnuamzoOlcuJDYY2b0SJgNmaCfpu8nxTEHXYKPFEm+wbm3H5CogDdQmmbos2L1zJ/99XNY7G/c7PTP7xdQ5dsDdsMDMm9jwgMGQIDAQAB';
        $aliClientModule->rsaPrivateKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCCPfnf1S4WFOSivuMdtdHoKZ+p/A3YnifWmME/h1k/eV1mU6vYrSj13lik/5v7eUkLhPdYnXWhfPC7yTbJdBlRmoKIpieT54YVOqHhiZcTi3bmg2ks1/GZEFbZ6a0eLXg1sTyRXW3PNr0ObHYYYi4IMd+skxPxbz61XDLFCYg+jJ04GLFYVC3BQJjEVKVFcA8k6J1+b3DMVrRUFpc3qcoHhHosuoS+sAxzah8fK24Bv0IIzPqoAbTQeKzPguGgRDx7NqNPuYUrcXC8fbzCOwtZRVeMeMv3/FX1gtAqZE5BXkLxUcWe79MycCiBVFRT7lbebQ7uASBXm4XqVJyMf9JdAgMBAAECggEAHmPPtkbL5iMh5kecPwZ54C42Lze9E1pz+ULTdLtkO8dZ39KOOpTNBfHHxVhPUTJPtPxlqquzEudQVMDyo7cPYVcoNjJu8bgINVPlCfdM5SaZq9fl5qzMluaVHWvFFVGOxxiABXtDcCJZos/0DmR6UTcx9darxJ4sh6znu8opnE5l1du7CJ3CoL4a8Z8xdgeqRusyDFE6UHFL/ls0dm2ALdFRNbyFG7+hYRZKHcWbV8ETuM3+6O6qbF4BskLs20srWSby5DNfTbC7uY8FtzNxHgHa0pz63lCs3iCR2VcHn1ME2DsW3YLx4UfT+y9uP8hCPN9GQyZ01dJ1dcviYOe4kQKBgQDBdEinyb10gpRPoSvS4hCHRsA1rm6TcXF5xTUNfw/G2q3+hrcl4VsLY7qRYWPkBFzJpSyoirfRlh9IBuICGAe4cb03q/wAFnJsNcwzmCr7qVZW7Qn5rSVS2vEjp+2L/KUaaiaI2OsxKR68f9u1y9pN9eUoVgCfGVLGqJMUqTA5twKBgQCsWcioDYmciu6EeKK1xkJDQRrLusnG5PrZvDp8GapOybUVrfPr4misI9jiHSO6g+gSCbulgF4fCsxVtmdu/zq7AiMJjYmhuXT7pg2fSrirSQdL0IUpjp08lAQdPlkC2TQIZfTPv1vDHMaWEznq8xw8ySqz3LGwg7ya9WVAsWNkiwKBgQCVU8lxmwwfD1ykSuilE9NmWHqt9UNtlLffIxbcoCPxf4OnYR2mo9m/ZO/yoJaWv7dP/6wFPW6+3X6v/oAe1aW//ivs+VjASJNya+SAPwmO0RvQZZC5pamV12Mj/tAiqpZXWXD9WVPS0sbjAl76aazNWO3WwOwh404+Aonl/OM46QKBgEvdeaX/z4NI5JULRRQeoSxZjCIBprAWOxV89YGLCpyDzWItoCFFGC4t2Vou2XtQdOb7wc2oI8YmSquwDvedAY0v85xQ4TR/Hi9neLeVfJRpIP0OXI9eZ3gy71ywBR5r3auUtZ587TeFgySsceIqAVQAePuTOeQpGOxc+KTxcH7hAoGBAJCmtOYa9PvrIlESNL3jYNx2sVEtt1gDhDTuwdd4rN9N85McV2JGjAtAVdeW3yPoTsOhIE+HrqSS+vFUSfZbdh16HRh1gyHBSfyIKSRqscgnAeM6+2hC/nA0ZEmGeUwaGvgA6qiRLXOBrSaSUZEpCXru2bF2GPz2X23WyJqvTEni';
        $requestParam = new AlipayTradeAppPayRequest();
        $requestParam->setNotifyUrl($notify_url);
        $requestParam->setBizContent(json_encode($requestArr));
        $response = $aliClientModule->sdkExecute($requestParam);
        return $response;
    }


    /**
     * @desc 获取支付宝配置信息
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 26 日
     */
    public function getAliPayConfig()
    {
        $res = AliappConfig::where('wid', $this->input['wid'])->first();
        if (!$res) {
            return [];
        }
        return $res->toArray();
    }

    /**
     * @desc 获取微信支付配置信息
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 26 日
     */
    public function getWechatPayConfig()
    {
        $res = WeixinConfigSub::where('wid', $this->input['wid'])->first();
        if (!$res) {
            return [];
        }
        return $res->toArray();
    }


    /**
     * @desc 输入
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 26 日
     * @param $input array 输入数据 包括 id，wid ，mid 等
     */
    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @desc 获取字节跳动
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 27 日
     */
    public function getByteDancePayConf()
    {
        $configData = ByteDanceConfig::where('wid', $this->input['wid'])->first();
        if (!$configData) {
            return [];
        }
        return $configData->toArray();
    }


}
