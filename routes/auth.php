<?php
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| 权限路由
|
*/
Route::group(['namespace'=>'Auth','prefix'=>'auth'],function(){
	Route::match(['get','post'],'/login','AuthController@login');//登录
	Route::match(['get','post'],'/register','AuthController@registerUser');//注册
	Route::match(['get','post'],'/sendcode','AuthController@sendCode');//验证码
	Route::get('/loginout','AuthController@loginout');//退出登录
	Route::get('/forgetpsd','AuthController@forgetPsd');//忘记密码
	Route::get('/changepsd','AuthController@changePsd');//修改密码
	Route::match(['get','post'],'/set','AuthController@set');//用户设置
	Route::post('/myfile/upfile','AuthController@upFile'); //上传文件
	Route::match(['get','post'],'/forgetpassword/update','AuthController@updateForgetPsd');//忘记密码
	Route::match(['get','post'],'/changepassword/update','AuthController@updateChangePsd');//修改密码
	Route::match(['get','post'],'/set/update','AuthController@updateSet');//修改密码
    /*分享注册*/
    Route::match(['get','post'],'/share/register','AuthController@register'); //分享注册页面
    Route::get('/share/isRegister','AuthController@isRegister'); //分享注册页面验证是否已注册
    Route::get('/share/regSuccess','AuthController@regSuccess'); //注册成功页面
    Route::get('/share/getShareData','AuthController@getShareData'); //获取分享信息
    Route::get('/loginout/shop','AuthController@loginout');//移动端清楚session
});