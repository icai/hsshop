<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'wechat/verify',
        'foundation/payment/wechatPayNotify',
        'wechat/getResponse',
        'wechat/*/receiveMsg',
        'xcx/*',
        'foundation/refund/wechatPayRefundNotify',
        'shop/order/upfile/*',
        'sellerapp/*',
        'aliapp/*',
        'merchants/myfile/notify',
        'staff/uedit',
        'merchants/kuaidi/kuaidiNotify',
        'aliapp/order/aliPayNotify',
        'merchants/fee/wechatPay/*',
        'merchants/fee/aliPay/*',
        'baidu/*',
    ];
}
