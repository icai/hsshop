<?php
/*
| 官网路由
|
*/
Route::group(['namespace' => 'Home', 'prefix' => 'home', 'middleware' => ['home']], function() {
    Route::match(['get','post'],'/index/reserve', 'IndexController@reserve'); // 预约订购
    Route::get('/index/microshop', 'IndexController@microshop'); // 微商城系统
    Route::get('/index/distribution', 'IndexController@distribution'); // 分销
    Route::get('/index/information/{category?}/{id?}', 'IndexController@information'); // 资讯
    Route::get('/index/detail/{id?}/{type?}', 'IndexController@detail'); // 资讯x详情
     Route::get('/index/newsDetail/{id?}/{type?}', 'IndexController@newsDetail'); // 资讯x详情
    Route::get('/index/helpDetail/{id}', 'IndexController@helpDetail'); // 帮助中心详情
    Route::get('/index/{type?}/shop', 'IndexController@shop'); //行业案例
    Route::get('/index/categoryShop', 'IndexController@categoryShop'); //行业案例
    Route::match(['get','post'],'/index/caseDetails', 'IndexController@caseDetails'); //行业案例详情
    Route::get('/index/applet', 'IndexController@applet');  //小程序
    Route::get('/index/customization', 'IndexController@customization');  //定制APP
    Route::get('/index/microMarketing', 'IndexController@microMarketing');  //微营销总裁班
    Route::get('/index/about', 'IndexController@about');  //关于我们
    Route::get('/index/growth', 'IndexController@growth');  //发展历程
    Route::get('/index/culture', 'IndexController@culture');  //企业文化
    Route::get('/index/recruit', 'IndexController@recruit');  //招贤纳士
    Route::get('/index/contactUs', 'IndexController@contactUs');  //联系我们
    Route::get('/index/productServiec', 'IndexController@productServiec');  //产品服务	
    Route::post('/index/shopApi', 'IndexController@shopApi'); //案例展示
    Route::post('/index/createQrcode', 'IndexController@createQrcode'); //生成二维码
    Route::get('/index/honor','IndexController@honor'); //资质荣誉

    Route::get('/index/helps', 'IndexController@helps'); // 帮助中心--首页  修改 吴晓平
    Route::get('/index/news', 'IndexController@newList'); // 资讯中心重构  修改 吴晓平
    Route::get('/index/helpList', 'IndexController@helpList'); // 帮助中心-问题词汇  吴晓平
    Route::get('/index/selfServe', 'IndexController@selfServe'); // 自助服务 吴晓平


	Route::get('/index/appCustomize', 'IndexController@appCustomize');  //定制APP
	Route::get('/index/develop','IndexController@develop'); //发展历程
	Route::get('/index/corporateCulture','IndexController@corporateCulture'); //企业文化 
	Route::get('/index/recruit','IndexController@recruit'); //招贤纳士
	Route::get('/index/contactUs','IndexController@contactUs'); //联系我们
    Route::get('/index/serviceFir','IndexController@serviceFir'); //我要服务 分销系统
    Route::get('/index/serviceSec','IndexController@serviceSec'); //我要服务 APP定制
    Route::get('/index/serviceThi','IndexController@serviceThi'); //我要服务 微信小程序
    Route::get('/index/serviceFou','IndexController@serviceFou'); //我要服务 微营销总裁班
    Route::get('/index/serviceFif','IndexController@serviceFif'); //我要服务 微信商城
//    Route::get('/index/help','IndexController@help'); //我要服务 微信商城
    Route::match(['get','post'],'/index/searchXCX', 'IndexController@searchXCX'); // 搜索小程序

    Route::match(['get','post'],'/index/putQuestion','IndexController@putQuestion'); //反馈提交未解决问题原因 

    Route::get('/weixin/getWeixinSecretKey','IndexController@getWeixinSecretKey');//获取微信公众号密钥

    /****营销应用 add by 吴晓平 添加官网营销应用相关路由***/
    Route::get('/index/appRecommen/{id?}', 'IndexController@appRecommen');  //应用推荐
    Route::get('/index/manageChannel', 'IndexController@manageChannel');  //经营渠道
    Route::get('/index/manageChannel/detail/{id}', 'IndexController@manageChannelDetail');  //经营渠道详情
    Route::get('/index/salesDiscount', 'IndexController@salesDiscount');  //促销折扣
    Route::get('/index/salesDiscount/detail/{id}', 'IndexController@salesDiscountDetail');  //促销折扣详情
    Route::get('/index/salesTools', 'IndexController@salesTools');  //促销工具
    Route::get('/index/salesTools/detail/{id}', 'IndexController@salesToolsDetail');  //促销工具详情
    Route::get('/index/memberTicket', 'IndexController@memberTicket');  //会员卡券
    Route::get('/index/memberTicket/detail/{id}', 'IndexController@memberTicketDetail');  //会员卡券详情
    Route::get('/index/extension', 'IndexController@extension'); //推广工具
    Route::get('/index/extension/detail/{id}', 'IndexController@extensionDetail'); //推广工具详情

    Route::get('/index/AppDownLoad','IndexController@AppDownLoad');  //app下载页面
    Route::get('/index/downLoadDetail','IndexController@downLoadDetail');  //app下载详情页

    // 吴晓平 2018年11月21日 案例
    Route::get('/category/list','CaseController@categoryList');
    Route::get('/category/case/list/{id?}/{type?}','CaseController@caseList');

});

Route::group(['namespace' => 'Home', 'prefix' => '', 'middleware' => ['home']], function() {
    Route::get('/', 'IndexController@index');  //首页
    Route::get('/siteMap', 'IndexController@siteMap');  //网站地图

     Route::match(['get','post'],'/upload/cdnImg','IndexController@uploadCdnIamge');
});