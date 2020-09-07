<?php
/*
|--------------------------------------------------------------------------
| 小程序提交报名 Routes
|--------------------------------------------------------------------------
|
| 
|
*/
Route::group(['namespace' => 'Applet', 'prefix' => 'applet'], function() {
    Route::get('/index', 'AppletController@index'); //小程序提交报名
    Route::match(['get','post'],'/signUp','AppletController@signUp'); //报名表单提交
    Route::get('/weixin/getWeixinSecretKey','AppletController@getWeixinSecretKey');//获取微信公众号密钥

    Route::get('/ship', 'AppletController@kinShip');
    Route::match(['get','post'],'/invitation/index','AppletController@invitation'); //H5请柬
    Route::get('/invitation/showImg','AppletController@showImg'); //图片显示
    Route::get('/invitation/h5','AppletController@h5'); //图片显示
});

