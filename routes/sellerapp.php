<?php

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['namespace' => 'SellerApp'], function() {
    Route::match(['get','post'],'/sellerapp/base/index', 'BaseController@index');  //获取app基础信息
    Route::match(['get','post'],'/sellerapp/base/puEncr', 'BaseController@puEncr');  //公钥加密接口测试
    Route::match(['get','post'],'/sellerapp/base/puDecr', 'BaseController@puDecr');  //公钥加密接口测试
    Route::match(['get','post'],'/sellerapp/base/priEncr', 'BaseController@priEncr');  //密钥加密接口
    Route::match(['get','post'],'/sellerapp/base/priDEcr', 'BaseController@priDEcr');  //秘钥解密接口
    Route::match(['get','post'],'/sellerapp/auth/login', 'AuthController@login');  //登陆接口
    Route::match(['get','post'],'/sellerapp/auth/sendCode', 'AuthController@sendCode');  //发送验证码接口
    Route::match(['get','post'],'/sellerapp/auth/forgetPasswd', 'AuthController@forgetPasswd');  //忘记密码

    // @update 吴晓平 2019年12月30日 10:09:12 账号注销
    Route::match(['get','post'],'/account/auth/logoff', 'AuthController@setAccountLogOff');
});

Route::group(['namespace' => 'SellerApp','prefix' => 'sellerapp', 'middleware' => ['sellerapp']], function() {

    Route::match(['get','post'],'/auth/modifyPasswd', 'AuthController@modifyPasswd');  //修改密码
    Route::match(['get','post'],'/store/create', 'StoreController@create');  //创建店铺
    Route::match(['get','post'],'/base/getRegion', 'BaseController@getRegion');  //过去地址信息
    Route::match(['get','post'],'/store/index', 'StoreController@index');  //首页信息
    Route::match(['get','post'],'/store/storeInfo', 'StoreController@storeInfo');  //更多
    Route::match(['get','post'],'/order/orderList', 'OrderController@orderList');  //订单列表
    Route::match(['get','post'],'/order/getStaticList', 'OrderController@getStaticList');  //订单类型、订单状态静态数据
    Route::match(['get','post'],'/order/getOrderInfo', 'OrderController@getOrderInfo');  //获取订单信息（待付款）
    Route::match(['get','post'],'/order/changePrice', 'OrderController@changePrice');  //修改价格（待付款）
    Route::match(['get','post'],'/order/getSellerRemark', 'OrderController@getSellerRemark');  //获取订单备注
    Route::match(['get','post'],'/order/getCloseOrder', 'OrderController@getCloseOrder');  //获取关闭订单信息（待付款）
    Route::match(['get','post'],'/order/closeOrder', 'OrderController@closeOrder');  //关闭订单（待付款）
    Route::match(['get','post'],'/order/setSellerRemark', 'OrderController@setSellerRemark');  //设置订单卖家备注
    Route::match(['get','post'],'/order/delSellerRemark', 'OrderController@delSellerRemark');  //删除订单卖家备注
    Route::match(['get','post'],'/order/getDeliveryPackageInfo', 'OrderController@getDeliveryPackageInfo');  //获取订单已发货包裹信息
    Route::match(['get','post'],'/order/getRefundPackageInfo', 'OrderController@getRefundPackageInfo');  //获取订单已退货包裹信息
    Route::match(['get','post'],'/order/getDeliveryInfo', 'OrderController@getDeliveryInfo');  //发货获取订单信息
    Route::match(['get','post'],'/order/deliveryOrder', 'OrderController@deliveryOrder');  //发货
    Route::match(['get','post'],'/order/getExpress', 'OrderController@getExpress');  //获取物流公司接口
    Route::match(['get','post'],'/order/makeCompleteGroups', 'OrderController@makeCompleteGroups');  //使成团
    Route::match(['get','post'],'/order/getOrderDetail', 'OrderController@getOrderDetail');  //订单详情
    Route::match(['get','post'],'/order/getRefundOrder', 'OrderController@getRefundOrder');  //获取退款信息
    Route::match(['get','post'],'/order/getConsultList', 'OrderController@getConsultList');  //获取协商列表
    Route::match(['get','post'],'/order/refundAddMessage', 'OrderController@refundAddMessage');  //退款添加留言
    Route::match(['get','post'],'/order/refundDisagree', 'OrderController@refundDisagree');  //拒绝买家申请
    Route::match(['get','post'],'/order/refundAddress', 'OrderController@refundAddress');  //选择退货地址
    Route::match(['get','post'],'/order/setRefundAddress', 'OrderController@setRefundAddress');  //发送退货地址
    Route::match(['get','post'],'/order/refundAgree', 'OrderController@refundAgree');  //同意退款
    Route::match(['get','post'],'/order/changeSendOrderAddr', 'OrderController@changeSendOrderAddr');  // 修改收货地址
    Route::match(['get','post'],'/order/modifyLogistics', 'OrderController@modifyLogistics');  // 修改物流
    Route::match(['get','post'],'/team/index', 'TeamController@index');  //店铺列表
    Route::match(['get','post'],'/store/setStore', 'StoreController@setStore');  //选择店铺
    Route::match(['get','post'],'/store/shopShareInfo', 'StoreController@shopShareInfo');  //获取店铺二维码
    Route::match(['get','post'],'/store/setUserInfo', 'StoreController@setUserInfo');  //用户设置
    Route::match(['get','post'],'/store/upFile', 'StoreController@upFile');  //上传图片
    Route::match(['get','post'],'/product/index', 'ProductController@index');  //商品列表
    Route::match(['get','post'],'/product/detail', 'ProductController@detail');  //商品详情
    Route::match(['get','post'],'/product/getSkusByProductId', 'ProductController@getSkusByProductId');  //获取商品属性
    Route::match(['get','post'],'/auth/logout', 'AuthController@logout');  //登出
    Route::match(['get','post'],'/team/jpushList', 'TeamController@jpushList'); //推送消息列表
    Route::match(['get','post'],'/team/markJpushRead', 'TeamController@markJpushRead'); //标记推送消息为已读状态
    Route::match(['get','post'],'/team/delPushMsg', 'TeamController@delPushMsg'); //删除推送消息
    Route::match(['get','post'],'/product/getProductShare', 'ProductController@getProductShare');  //商品分享

    Route::match(['get','post'],'/statistics/shopStatistics', 'StatisticsController@shopStatistics');  //营收统计
    Route::match(['get','post'],'/statistics/shopOrderStatistics', 'StatisticsController@shopOrderStatistics');  //交易统计
    Route::match(['get','post'],'/statistics/shopPageStatistics', 'StatisticsController@shopPageStatistics');  //流量统计
    Route::match(['get','post'],'/statistics/getRankProductV', 'StatisticsController@getRankProductV');  //商品浏览量排名
    Route::match(['get','post'],'/statistics/getPage', 'StatisticsController@getPage');  //页面浏览量排名
    Route::match(['get','post'],'/statistics/getIncomeAndRefund', 'StatisticsController@getIncomeAndRefund');  //收入支出
    Route::match(['get','post'],'/statistics/memberStatistics', 'StatisticsController@memberStatistics');  //客户统计

    Route::match(['get','post'],'/team/test', 'TeamController@test');
});
