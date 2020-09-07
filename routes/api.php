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

Route::group(['namespace' => 'WebApi'], function() {
    Route::match(['post','get'],'/getUser', 'WebApiController@getUser'); // 获取用户信息 add by jonzhang
    Route::match(['post','get'],'/getStore', 'WebApiController@getStore'); //获取店铺信息 add by jonzhang
    Route::match(['post','get'],'/getChatData', 'WebApiController@getChatData');
    Route::match(['post','get'],'/getUserOrderData', 'WebApiController@getUserOrderData');
    /*start MayJay*/
    Route::get('/getNewOrderCount','WebApiController@getNewOrderCount'); //新订单消息数量
    Route::get('/getNewOrderNotification','WebApiController@getNewOrderNotification'); //新订单消息列表
    Route::post('/clearOrderNotification','WebApiController@clearOrderNotification'); //新订单消息清空
    Route::get('/getToken','WebApiController@getAccessToken'); //获取小程序token
    /*end*/
    Route::match(['post','get'],'/lastLoginTime', 'DataCenterController@updateLastLoginTime');

    Route::post('/customMessageUnread', 'WebApiController@customMessageUnread'); //客服消息未读
    Route::get('/contactUser', 'WebApiController@contactUser'); //用户消息
    Route::get('/customMessageReply', 'WebApiController@customMessageReply'); // 客服消息留言

});
//此路由仅仅用于测试
Route::group(['namespace' => 'WebApi'], function() {
    Route::get('/getTest', function () {
        return 'Hello,World';
    });
});
