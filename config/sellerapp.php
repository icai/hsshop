<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/4/3
 * Time: 17:52
 * @update 安卓上传新版本
 */

return [
    'jpush_msg' => [
        '0' => '您的店铺试用期即将结束，请及时续费。',
        '1' => '您的买家发起退款，订单编号%s，订单金额￥%.2f，请您在10天内处理，过时系统将自动退款。请尽快登录%s操作。',
        '2' => '您有新的订单，订单编号%s，订单金额￥%.2f，请尽快登录%s进行发货。',
        '3' => '您有一条退货单，订单编号%s，订单金额￥%.2f，请尽快登录%s进行处理。',
        '4' => '由于您长时间未处理订单编号%s，订单金额￥%.2f，系统将于72小时后退款给买家；请尽快登录%s进行处理。'
    ],
    'jpush_type' => [
        '0' => '系统通知',
        '1' => '买家发起退款',
        '2' => '新订单提醒',
        '3' => '买家已退货',
        '4' => '退款临近超时'
    ],
    'detail_pic'=>[
        'gaijia' => 'hsshop/image/static/gaijia@3x.png',
        'guanbi' => 'hsshop/image/static/guanbi@3x.png',
        'beizhu' => 'hsshop/image/static/beizhu@3x.png',
        'wuliu' => 'hsshop/image/static/wuliu@3x.png',
        'shichengtuan' => 'hsshop/image/static/shichengtuan@3x.png',
        'fahuo' => 'hsshop/image/static/fahuo@3x.png'
    ],
    'jpush_pic' => [
        'xiaotongzhi' => 'hsshop/image/static/xiao-tongzhi@3x.png',
        'xiaodingdan' => 'hsshop/image/static/xiao-dingdan@3x.png'
    ],
    'exceptionUri' => [
        'sellerapp/product/detail',
        'sellerapp/product/getSkusByProductId',
        'sellerapp/statistics/shopStatistics',
        'sellerapp/statistics/shopOrderStatistics',
        'sellerapp/statistics/shopPageStatistics',
        'sellerapp/statistics/getRankProductV',
        'sellerapp/statistics/getPage',
        'sellerapp/statistics/getIncomeAndRefund',
        'sellerapp/statistics/memberStatistics'
    ],
    'android'   =>[
        '1.0.3' => 'https://audit.huisou.cn/',
        '1.0.4' => 'https://audit.huisou.cn/',
        '1.0.5' => 'https://audit.huisou.cn/',
        '1.0.6' => 'https://www.huisou.cn/',
        '1.1.0' => 'https://www.huisou.cn/',
        '1.1.1' => 'https://www.huisou.cn/',
        '1.1.2' => 'https://www.huisou.cn/',
        '1.1.3' => 'https://www.huisou.cn/',
        '1.1.4' => 'https://www.huisou.cn/',
        '1.1.5' => 'https://www.huisou.cn/',
        '1.1.6' => 'https://www.huisou.cn/',
        '1.1.7' => 'https://www.huisou.cn/',
        '1.1.8' => 'https://www.huisou.cn/',
        '1.1.9' => 'https://www.huisou.cn/',
        '1.2.0' => 'https://www.huisou.cn/',
        '1.2.1' => 'https://www.huisou.cn/',
        '1.2.2' => 'https://www.huisou.cn/',
        '1.2.3' => 'https://www.huisou.cn/',
        '1.2.4' => 'https://www.huisou.cn/',
    ],
    'ios'   =>[
        '1.0.0' => 'https://audit.huisou.cn/',
        '1.0.1' => 'https://audit.huisou.cn/',
        '1.0.2' => 'https://www.huisou.cn/',
        '1.0.3' => 'https://www.huisou.cn/',
        '1.0.4' => 'https://www.huisou.cn/',
        '1.0.5' => 'https://www.huisou.cn/',
        '1.0.6' => 'https://www.huisou.cn/',
        '1.0.7' => 'https://www.huisou.cn/',
        '1.0.8' => 'https://www.huisou.cn/',
        '1.0.9' => 'https://www.huisou.cn/',
    ],
    'version'=>[
        'android'   => env('ANDROID_CODE', '1.0.4'),
        'ios'       => env('IOS_CODE', '1.0.0'),
    ],
];