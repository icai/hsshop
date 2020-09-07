<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/7/19
 * Time: 13:55
 */

Route::group(['namespace' =>'AliApp','prefix' => 'aliapp','middleware' => ['shop']], function()
{
    Route::match(['get','post'],'/payment/getPayOrderInfo','PaymentController@getPayOrderInfo');//何书哲 2018年7月26日 支付宝支付获取拼接参数


});

Route::group(['namespace' =>'AliApp','prefix' => 'aliapp'], function()
{
    Route::match(['get','post'],'/callBack','AuthorizationController@callBack');
    Route::match(['get','post'],'/AsynchronousNotification','AuthorizationController@AsynchronousNotification');
    Route::match(['get','post'],'/getUrl','AuthorizationController@getUrl');
    Route::match(['get','post'],'/authorization/login','AuthorizationController@login');

    Route::match(['get','post'],'/payment/aliPayNotify','PaymentController@aliPayNotify');//何书哲 2018年7月26日 支付宝支付回调

});
