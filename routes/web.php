<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::match(['get','post'],'/upfile', 'Merchants\MyFileController@upFile');
Route::match(['get','post'],'/addClassify', 'Merchants\MyFileController@addClassify');
Route::match(['get','post'],'/modifyClassify', 'Merchants\TestController@modifyClassify');
Route::match(['get','post'],'/delFile', 'Merchants\MyFileController@delFile');
Route::match(['get','post'],'/modifyFileName', 'Merchants\MyFileController@modifyFileName');
Route::match(['get','post'],'/modifyVedio', 'Merchants\MyFileController@modifyVedio');
Route::match(['get','post'],'/getUserFileByClassify', 'Merchants\MyFileController@getUserFileByClassify');
Route::match(['get','post'],'/delClassify', 'Merchants\MyFileController@delClassify');
Route::match(['get','post'],'/getPermission', 'Merchants\TestController@getPermission');
Route::match(['get','post'],'/getUserPermission', 'Merchants\PermissionController@getUserPermission');
Route::match(['get','post'],'/addAdmin', 'Merchants\PermissionController@addAdmin');
Route::match(['get','post'],'/getManager', 'Merchants\PermissionController@getManager');

Route::get('/microPage/indexPage/{wid}/{id}', 'Shop\MicroPageController@showIndexPage'); //详细
Route::match(['get','post'],'/microPage/index/{wid}/{id}/{type?}', 'Shop\MicroPageController@showPage');

/*
|2016年10月31日，微商城管理后台路由
|整个微商城后台管理路由
|
|
 */
Route::group(['namespace'=>'Hsadmin','prefix'=>'hsadm'],function(){
	Route::get('/','IndexController@index');
	Route::get('/test','IndexController@test');
});





