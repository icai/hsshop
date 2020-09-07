<?php
/*
|--------------------------------------------------------------------------
| Wechat Routes
|--------------------------------------------------------------------------
|
| 微信路由
|
*/
Route::group(['namespace' => 'Wechat', 'prefix' => 'wechat'], function() {
    Route::match(['get', 'post'], '/verify', 'IndexController@index'); // 验证服务器地址的有效性
    Route::get('/getAccessToken/{wid}', 'IndexController@getAccessToken'); // 获取接口调用凭证
    Route::any('/getResponse','IndexController@getResponse'); //微信开放平台授权事件接收URL
    Route::match(['get','post'],'/{appId}/receiveMsg','IndexController@receiveMsg'); //公众号消息与事件接收<URL>
});

Route::group(['namespace' =>'Shop','prefix' => 'foundation' ], function() {
    
    Route::match(['post','get'],'/payment/wechatPayNotify','PaymentController@wechatPayNotify');
    Route::match(['post','get'],'/refund/wechatPayRefundNotify','RefundController@wechatPayRefundNotify');
});