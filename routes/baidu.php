<?php

/**
 * 百度小程序路由
 * @author 许立 2018年10月10日
 */

Route::group(['namespace' =>'BaiduApp','prefix' => 'baiduapp','middleware' => ['shop']], function()
{
    Route::match(['get','post'],'/getPayOrderInfo','PaymentController@getPayOrderInfo'); // 支付宝支付获取拼接参数
});

Route::group(['namespace' =>'BaiduApp','prefix' => 'baidu'], function()
{
    Route::match(['get','post'],'/payNotify','PaymentController@payNotify'); // 支付回调接口
    Route::match(['get','post'],'/refundNotify','PaymentController@refundNotify'); // 退款回调接口
    Route::match(['get','post'],'/refundAudit','PaymentController@refundAudit'); // 退款审核接口

    Route::match(['get','post'],'/login','AuthorizeController@login'); //用登录code换取session_key
    Route::match(['get','post'],'/getUserInfo','AuthorizeController@getUserInfo');
    Route::match(['get','post'],'/checkToken','AuthorizeController@checkToken'); //检查token是否过期
});
