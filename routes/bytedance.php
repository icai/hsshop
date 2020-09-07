<?php

/**
 * @desc 字节跳动小程序路由
 * @date 2019年9月23日08:50:51
 */
Route::group(['namespace' => 'ByteDance', 'prefix' => 'byte'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::match(['get', 'post'], '/login', 'AuthController@login'); // 登陆接口
    });

});

Route::group(['namespace' => 'ByteDance', 'prefix' => 'byte', 'middleware' => ['xcx','xcxAfter']], function () {
    Route::group(['prefix' => 'pay'], function () {
        Route::match(['get', 'post'], '/getPay', 'PayController@getPay'); // 获取支付信息接口
    });
});
