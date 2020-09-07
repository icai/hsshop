<?php
/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
|
| 总后台路由
|
*/
Route::group(['middleware' => ['staff'], 'namespace' => 'Staff', 'prefix' => 'staff'], function () {
    /*登陆用户管理*/
    Route::match(['get', 'post'], '/addUser', 'LoginController@addUser'); //添加后台用户
    Route::post('/modifyUser', 'LoginController@modifyUser'); //修改后台用户
    Route::post('/delAccount', 'LoginController@delAccount'); //删除总后台管理员
    /*资讯管理*/
    Route::post('/addInformation', 'InformationController@addInformation'); //添加资讯，修改资讯
    Route::post('/addInfoType', 'InformationController@addInfoType'); //添加资讯分类
    Route::get('/showEditType', 'InformationController@showEditType'); //编辑资讯分类
    Route::get('/getInfoType', 'InformationController@getInfoType'); //获取资讯分类
    Route::get('/getInformation', 'InformationController@getInformation'); //根据条件获取资讯
    Route::match(['post', 'get'], '/delInfomation', 'InformationController@delInfomation'); //删除资讯
    Route::get('/getRecomment', 'InformationController@getRecomment'); //获取推荐列表
    Route::post('/addRecomment', 'InformationController@addRecomment'); //资讯推荐
    Route::get('/getRecommentInfo', 'InformationController@getRecommentInfo'); //根据推荐获取资讯
    Route::get('/editInformation', 'InformationController@editInformation'); //添加资讯修改资讯
    Route::get('/informationDetal', 'InformationController@informationDetal'); //添加资讯修改资讯
    Route::post('/delInfoType', 'InformationController@delInfoType'); //删除资讯分类
    Route::post('/delRecommend', 'InformationController@delRecommend'); //删除推荐
    Route::match(['get', 'post'], '/addRecommend', 'InformationController@addRecommend'); //添加修改推荐
    Route::match(['get', 'post'], '/saveInformationSort', 'InformationController@saveInformationSort'); //添加修改推荐


    /*企业管理*/
    Route::get('/BusinessCategory', 'BusinessManageController@BusinessCategory'); //获取企业分类
    Route::get('/BusinessManage/regionManage', 'BusinessManageController@regionManage'); //地址管理
    Route::get('/BusinessManage/hideRegion/{id}', 'BusinessManageController@hideRegion'); //隐藏地址
    Route::match(['post', 'get'], '/BusinessManage/addRegion', 'BusinessManageController@addRegion'); //添加地址
    Route::get('/index', 'BusinessManageController@index'); //首页
    Route::post('/addCategory', 'BusinessManageController@addCategory'); //添加企业分类
    Route::post('/getModifyInfo', 'BusinessManageController@getModifyInfo'); //获取修改分类
    Route::post('/delCategory', 'BusinessManageController@delCategory'); //删除企业分类
    Route::match(['get', 'post'], '/getShop/{uid?}', 'BusinessManageController@getShop'); //店铺搜索展示
    Route::get('/getShopCode', 'BusinessManageController@getShopCode'); //店铺二维码小程序码
    Route::post('/ignoreShop', 'BusinessManageController@ignoreShop'); //忽略店铺
    Route::post('/batchIgnore', 'BusinessManageController@batchIgnore'); //忽略全部店铺
    Route::match(['get', 'post'], '/shopExport', 'BusinessManageController@shopExport'); //导出店铺 Herry
    Route::post('/modifyShop', 'BusinessManageController@modifyShop'); //修改店铺
    Route::post('/recommend', 'BusinessManageController@recommend'); //店铺推荐取消推荐
    Route::get('/getTemplate', 'BusinessManageController@getTemplate'); //获取店铺模板
    Route::post('/delTemplate/{id}', 'BusinessManageController@delTemplate'); //删除店铺模板
    Route::match(['get', 'post'], '/addTemplate', 'BusinessManageController@addTemplate'); //添加店铺模板
    Route::get('/showEditShop', 'BusinessManageController@showEditShop'); //修改店铺
    Route::post('/delShop', 'BusinessManageController@delShop'); //删除店铺
    Route::match(['get', 'post'], '/uploadFile', 'BusinessManageController@uploadFile');
    Route::match(['get', 'post'], '/registerUser', 'CustomerController@registerUser'); //添加后台用户
    Route::match(['get', 'post'], '/userlist', 'CustomerController@userlist'); //添加后台用户
    Route::get('/BusinessManage/customer', 'BusinessManageController@customer'); //店铺访客管理
    Route::get('/BusinessManage/changeMobile', 'BusinessManageController@changeMobile'); //修改手机号码
    Route::match(['get', 'post'], '/BusinessManage/cleanDistribute', 'BusinessManageController@cleanDistribute'); //清空分销关系
    Route::post('/BusinessManage/export', 'BusinessManageController@export');
    Route::match(['get', 'post'], '/BusinessManage/affiche', 'BusinessManageController@affiche'); // 公告
    Route::match(['get', 'post'], '/userModify', 'CustomerController@userModify'); //修改手机帐号
    Route::match(['get', 'post'], '/passwordModify', 'CustomerController@passwordModify'); //修改登录密码
    Route::match(['get', 'post'], '/store/updateForFee', 'BusinessManageController@updateStoreForFee'); // add by jonzhang 店铺是否收费
    Route::match(['get', 'post'], '/openPermission', 'CustomerController@openPermission'); //一键添加权限
    Route::get('/openPermissionLog', 'CustomerController@openPermissionLog'); //一键添加权限设置日志
    Route::get('/operateLog', 'BusinessManageController@allOperateLog'); //何书哲 2018年9月21日 查看日志

    Route::match(['get', 'post'], '/relieveLogin', 'BusinessManageController@relieveLogin'); //解除商家后台登录限制
    Route::post('/userRemark', 'BusinessManageController@userRemark'); // 许立 2018年09月26日 备注修改接口

    /*权限管理*/
    Route::match(['get', 'post'], '/addAdminRole', 'PermissionController@addAdminRole');//添加后台角色
    Route::match(['get', 'post'], '/bindAdminRolePermission', 'PermissionController@bindAdminRolePermission');//绑定后台角色权限关系
    Route::post('/addPermission', 'PermissionController@addPermission');//添加权限
    Route::post('/deletePermission', 'PermissionController@deletePermission');//添加权限
    Route::post('/addWeixinRole', 'PermissionController@addWeixinRole');//绑定店铺角色
    Route::get('/getPermission', 'PermissionController@getPermission');//获取权限列表
    Route::match(['get', 'post'], '/bindRolePermission', 'PermissionController@bindRolePermission');//绑定前台角色权限
    Route::post('/openRole', 'PermissionController@openRole');//开启禁用前台角色
    Route::get('/getAdminRole', 'PermissionController@getAdminRole');//获取总后台角色
    Route::match(['get', 'post'], '/addRole', 'PermissionController@addRole');//添加前台角色
    Route::get('/getRole', 'PermissionController@getRole');//添加前台角色
    Route::match(['get', 'post'], '/permission/staffPermission', 'PermissionController@staffPermission');//绑定总后台权限
    Route::match(['get', 'post'], '/permission/bindStaffPermission', 'PermissionController@bindStaffPermission');//绑定总后台权限
    /*总后台用户管理*/
    Route::get('/account', 'PermissionController@account');//总后台用户列表
    /*文件上传*/
    Route::post('/fileUpload', 'InformationController@fileUpload');//文件上传
    /*潜在客户管理*/
    Route::get('/customer/reserveManage', 'CustomerController@reserveManage');//潜在客户管理
    Route::get('/customer/export', 'CustomerController@export');//导出潜在客户
    Route::match(['get', 'post'], '/customer/addStar/{id}/{status}', 'CustomerController@addStar');//潜在客户加星
    Route::match(['get', 'post'], '/customer/delete/{id}', 'CustomerController@delete');//潜在客户加星
    /*商品管理*/
    Route::get('/product/category', 'ProductController@category');//商品品类
    Route::post('/product/del/{id}', 'ProductController@del');//删除商品品类
    Route::post('/product/add', 'ProductController@add');//添加商品品类


    /*客服管理*/
    Route::match(['get', 'post'], '/CustomerServiceManage', 'CustomerManageController@save');
    Route::match(['get', 'post'], '/updateCustomerService', 'CustomerManageController@save');

    /*分享统计*/
    Route::get('/shareIncome', 'ShareController@shareIncome'); //分享统计列表
    Route::get('/showSignerList', 'ShareController@showSignerList'); //对应的分享报名用户列表

    //小程序查重功能
    Route::get('/customer/searchXCX', 'CustomerController@searchXCX');//小程序查重
    Route::get('/customer/exportSearchXCX', 'CustomerController@exportSearchXCX');//导出小程序查重客户
    Route::post('/customer/operate', 'CustomerController@operate');//小程序查重后台操作
    Route::get('/customer/liteapp', 'CustomerController@liteapp');//小程序列表
    Route::post('/customer/liteappAdd', 'CustomerController@liteappAdd');//添加小程序
    Route::post('/customer/liteappDelete', 'CustomerController@liteappDelete');//删除小程序
    Route::get('/customer/exportLiteapp', 'CustomerController@exportLiteapp');//导出小程序查重客户
    Route::get('/customer/liteappHistory', 'CustomerController@liteappHistory');//小程序搜索历史
    Route::post('/customer/liteappHistoryAdd', 'CustomerController@liteappHistoryAdd');//小程序搜索历史-新增
    /*Banner图管理*/
    Route::get('/banner/index', 'BannerController@index'); //banner图列表
    Route::match(['get', 'post'], '/banner/save', 'BannerController@save'); // 新建/编辑 banner图
    Route::match(['get', 'post'], '/banner/statusSave', 'BannerController@statusSave'); //状态修改
    Route::get('/banner/ad', 'BannerController@ad'); //广告列表
    Route::match(['get', 'post'], '/banner/adSave', 'BannerController@adSave'); //广告列表
    Route::post('/banner/adDel', 'BannerController@adDel'); //删除广告
    Route::get('/banner/sellerappad', 'BannerController@sellerappad'); //APP广告列表
    Route::match(['get', 'post'], '/banner/sellerappadAdd', 'BannerController@sellerappadAdd'); //添加广告
    Route::match(['get', 'post'], '/banner/selleradDel', 'BannerController@selleradDel'); //删除广告
    Route::match(['get', 'post'], '/banner/selleradOpen', 'BannerController@selleradOpen'); //开启广告连接

    /*案例管理*/
    Route::get('/example/index', 'ExampleController@index'); //案例列表
    Route::match(['get', 'post'], '/example/save', 'ExampleController@save'); //新建/编辑 案例
    Route::post('/example/caseDel', 'ExampleController@caseDel'); //删除案例
    Route::get('/example/industry', 'ExampleController@industry'); //行业分类
    Route::match(['get', 'post'], '/example/industrySave', 'ExampleController@industrySave'); //行业分类 新建/编辑
    Route::match(['get', 'post'], '/example/industryDel', 'ExampleController@industryDel'); //删除行业分类
    Route::get('/example/commentList', 'ExampleController@commentList'); //案例评论
    Route::post('/example/commentDel', 'ExampleController@commentDel'); //删除评论
    Route::get('/example/createQrcode', 'ExampleController@createQrcode'); //创建二维码

    /*SEO设置*/
    Route::get('/seo/index', 'SeoController@index'); //Seo列表
    Route::match(['get', 'post'], '/seo/save', 'SeoController@save'); //新建/编辑 seo设置
    Route::post('/seo/seoDel', 'SeoController@seoDel'); // seo 删除

    /*友情链接*/
    Route::get('/link/index', 'LinkController@index'); //友情链接列表
    Route::match(['get', 'post'], '/link/save', 'LinkController@save'); //新建/编辑 seo设置
    Route::post('/link/linkDel', 'LinkController@linkDel'); //删除友链
    Route::get('/link/mapIndex', 'LinkController@mapIndex'); //网站地铁

    /*业务员跟单*/
    Route::get('/seller/index', 'SellerController@index'); //销售管理客户列表
    Route::get('/seller/refresh', 'SellerController@refresh'); //刷新参团信息
    Route::get('/seller/getSaleManDetail/{id?}', 'SellerController@getSaleManDetail'); //刷新参团信息
    Route::match(['get', 'post'], '/seller/updateGroup', 'SellerController@updateGroup'); //销售管理修改分组
    Route::post('/seller/updateValid', 'SellerController@updateValid'); //销售管理修改有效单
    Route::get('/seller/sellerIndex', 'SellerController@sellerIndex'); //销售列表
    Route::get('/seller/exportSellerkpi', 'SellerController@exportSellerkpi'); //销售管理客户导出
    Route::get('/seller/exportSalesman', 'SellerController@exportSalesman');//销售信息导出
    Route::post('/seller/del', 'SellerController@del');
    Route::match(['get', 'post'], '/seller/joinInfo', 'SellerController@joinInfo');//按照参与则查询

    Route::get('/xcx/list', 'XCXController@list');//小程序列表
    Route::get('/xcx/templateList', 'XCXController@templateList');//小程序模板库列表
    Route::match(['get', 'post'], '/xcx/template/all', 'XCXController@showTemplateList');//小程序模板 add by jonzhang
    Route::match(['get', 'post'], '/xcx/template/del', 'XCXController@deleteTemplate');//删除小程序模板        add by jonzhang
    Route::match(['get', 'post'], '/xcx/template/add', 'XCXController@insertTemplate');//把草稿转化为小程序模板 add by jonzhang
    Route::match(['get', 'post'], '/xcx/cancelAudit', 'XCXController@cancelAudit');//小程序取消审核 add by jonzhang
    Route::match(['get', 'post'], '/xcx/template/setVersion', 'XCXController@updateXCXOnLine');//设置为当前版本 add by jonzhang
    Route::get('/xcx/updateErrorStatistics', 'XCXController@updateErrorStatistics');
    Route::get('/xcx/doUpdateErrorStatistic', 'XCXController@doUpdateErrorStatistic');

    Route::match(['get', 'post'], '/xcx/log/add', 'XCXController@insertXCXRecord');//添加小程序备注 add by jonzhang
    Route::match(['get', 'post'], '/xcx/log/showAll', 'XCXController@showXCXRecordList');//显示小程序备注 add by jonzhang
    Route::match(['get', 'post'], '/xcx/qrcode', 'XCXController@getXCXQRCode');//获取小程序二维码 add by jonzhang
    Route::match(['get', 'post'], '/xcx/code', 'XCXController@getXCXCode');//获取小程序码 add by jonzhang
    Route::match(['get', 'post'], '/xcx/cancel', 'XCXController@cancelXCX');//作废小程序 add by jonzhang
    Route::match(['get', 'post'], '/xcx/pulloff', 'XCXController@pullOffXCX');//下架小程序 add by jonzhang
    Route::match(['get', 'post'], '/xcx/change', 'XCXController@changeVisitStatus');//下架/上架小程序 开发使用 add by jonzhang
    Route::match(['get', 'post'], '/xcx/revertCodeRelease', 'XCXController@revertCodeRelease');//版本回退 开发使用 add by jonzhang
    Route::match(['get', 'post'], '/xcx/setWebviewDomain', 'XCXController@setWebviewDomain');//设置业务域名 add by jonzhang
    Route::match(['get', 'post'], '/xcx/batchSetWebviewDomain', 'XCXController@setWebviewDomain');//批量设置业务域名 add by jonzhang
    Route::match(['get', 'post'], '/xcx/updateDataForBatch', 'XCXController@updateDataForBatch');//批量设置业务域名 add by jonzhang
    Route::match(['get', 'post'], '/xcx/seeStaffOperLog', 'XCXController@seeStaffOperLog');//何书哲 2018年8月29日 查看日志


    Route::post('/xcx/commit', 'XCXController@commit'); //上传代码至微信小程序

    // 总后台小程序
    Route::post('/xcx/modifyDomain', 'XCXController@modifyDomain'); // 修改服务器地址
    Route::get('/xcx/getCategory', 'XCXController@getCategory'); // 获取类目
    Route::post('/xcx/bindTester', 'XCXController@bindTester'); // 绑定微信用户为小程序体验者
    Route::post('/xcx/getTemplates', 'XCXController@getTemplates'); // 获取帐号下已存在的模板列表
    Route::get('/xcx/getQrCode', 'XCXController@getQrCode'); // 获取体验小程序的体验二维码
    Route::post('/xcx/addTemplates', 'XCXController@addTemplates'); // 组合模板并添加至帐号下的个人模板库
    Route::post('/xcx/release', 'XCXController@release'); // 发布
    Route::post('/xcx/submitAudit', 'XCXController@submitAudit'); // 提交审核
    Route::get('/xcx/getPage', 'XCXController@getPage'); // 获取页面
    Route::get('/xcx/getAllDomains', 'XCXController@getAllDomains'); // 一键获取域名
    Route::post('/xcx/unbindTester', 'XCXController@unbindTester'); // 解除绑定小程序的体验者
    Route::get('/xcx/updateAuditStatus', 'XCXController@updateXcxAuditStatus'); // 更新审核状态
    Route::get('/xcx/queryQuota', 'XCXController@queryQuota'); // 查询服务商的当月提审限额（quota）和加急次数
    Route::match(['get', 'post'], '/xcx/speedUpAudit', 'XCXController@speedUpAudit'); // 加急审核申请
    Route::get('/xcx/pluginList', 'XCXController@pluginList'); // 已添加插件列表
    Route::post('/xcx/pluginApply', 'XCXController@pluginApply'); // 添加插件
    Route::post('/xcx/pluginUpdate', 'XCXController@pluginUpdate'); // 修改插件版本
    Route::post('/xcx/pluginUnbind', 'XCXController@pluginUnbind'); // 删除已添加插件


    // 第三方代商家小程序部署版本
    Route::post('/aliapp/versionUpload', 'AliappController@versionUpload'); // 许立 2018年07月30日 上传代码至微信小程序
    Route::post('/aliapp/versionAudit', 'AliappController@versionAudit'); // 许立 2018年07月30日 小程序提交审核
    Route::get('/aliapp/detail/{id}', 'AliappController@detail'); // 许立 2018年07月31日 小程序查看详情
    Route::match(['get', 'post'], '/aliapp/member/create', 'AliappController@createMembers'); //add by 张国军 添加体验者
    Route::match(['get', 'post'], '/aliapp/member/delete', 'AliappController@deleteMembers'); //add by 张国军 删除体验者
    Route::match(['get', 'post'], '/aliapp/experience/query', 'AliappController@queryExperience'); //add by 张国军 体验者二维码
    Route::match(['get', 'post'], '/aliapp/version/online', 'AliappController@onlineVersion'); //add by 张国军 上架支付宝小程序
    Route::match(['get', 'post'], '/aliapp/version/offline', 'AliappController@offlineVersion'); //add by 张国军 下架支付宝小程序
    Route::match(['get', 'post'], '/aliapp/safedomain/create', 'AliappController@createSafeDomain'); //add by 张国军 白名单
    Route::match(['get', 'post'], '/aliapp/config/list', 'AliappController@configList'); //add by 张国军 支付宝小程序配置路由
    Route::match(['get', 'post'], '/aliapp/config/select/all', 'AliappController@selectAll'); //add by 张国军 查询所有的支付宝小程序
    Route::match(['get', 'post'], '/aliapp/experience/create', 'AliappController@createExperience'); //add by 张国军 创建体验版
    Route::match(['get', 'post'], '/aliapp/experience/cancel', 'AliappController@cancelExperience'); //add by 张国军 取消体验版

    //续费
    Route::match(['get', 'post'], '/fee/order/select/all', 'FeeController@selectOrders'); //显示服务订单 add by 张国军
    Route::match(['get', 'post'], '/fee/order/select/one', 'FeeController@selectOneOrder'); //显示服务订单详情 add by 张国军
    Route::match(['get', 'post'], '/fee/order/update', 'FeeController@updateOrderData'); //更改服务订单状态 add by 张国军
    Route::match(['get', 'post'], '/fee/invoice/select/one', 'FeeController@selectOneInvoice'); //查询某个发票 add by 张国军
    Route::match(['get', 'post'], '/fee/invoice/select/all', 'FeeController@selectInvoices'); //查询发票 add by 张国军
    Route::match(['get', 'post'], '/fee/invoice/update', 'FeeController@updateInvoiceData'); //更改发票明细 add by 张国军
    Route::match(['get', 'post'], '/fee/order/list', 'FeeController@serviceList'); //服务列表路由 add by 张国军
    Route::match(['get', 'post'], '/fee/invoice/list', 'FeeController@invoiceList'); //发票列表路由 add by 张国军

    Route::match(['get', 'post'], '/sync/weixin_case', 'CaseController@syncWeixinCase'); //同步到商户案例
    Route::get('/weixin/case_list', 'CaseController@weixinCaselist'); //行业案例列表
    Route::get('/weixin/case_create', 'CaseController@caseCreate'); //新建商家案例
    Route::match(['get', 'post'], '/weixin/case_edit', 'CaseController@caseEdit'); //修改案例分类
    Route::match(['get', 'post'], '/weixin/case_del', 'CaseController@caseDel'); //删除案例分类
    Route::match(['get', 'post'], '/weixin/caseFile_up', 'CaseController@caseFileUp'); //商家案例导入文件上传
    Route::match(['get', 'post'], '/weixin/case_import', 'CaseController@caseImport'); //文件数据导入到案例库
    Route::match(['get', 'post'], '/weixin/case_qrcodeUpload', 'CaseController@caseQrcodeUpload'); //案例二维码图片上传
    Route::get('/weixin/case_downTemp', 'CaseController@downExcelTemp'); //下载商家案例模板

    // add 吴晓平 2019年08月19日 15:30:31 总后台的相关数据统计
    Route::get('get/weixin/statistic', 'StatisticController@getWeixinStatistic');    // 商家活跃度微商城数据统计
    Route::get('get/active/statistic', 'StatisticController@getActiveApi');          // 商家活跃度数据统计api
    Route::get('get/rank_list/statistic', 'StatisticController@getRankingListApi');  // 倒序排序列表api

});
Route::group(['middleware' => ['staff'], 'prefix' => 'staff'], function () {
    Route::get('/myfile/getCDNInfo', 'Merchants\MyFileController@getCDNInfo'); //获取上传信息
    Route::get('/myfile/setFile', 'Merchants\MyFileController@setFile'); //设置

});

/*登陆路由*/
Route::group(['namespace' => 'Staff', 'prefix' => 'staff'], function () {
    Route::match(['get', 'post'], '/login', 'LoginController@index'); // 登陆
    Route::get('/logout', 'LoginController@logout'); // 登陆


    Route::get('/xcx/getAuditStatusTest', 'XCXController@getAuditStatusByDeveloper'); //获取小程序代码审核状态 add by jonzhang
    Route::get('/xcx/third/templatedraft', 'XCXController@getTemplateDraftList'); //获取微信第三方平台中小程序代码草稿 add by jonzhang
    Route::get('/xcx/third/template', 'XCXController@getTemplateList'); //获取微信第三方平台中小程序代码模块 add by jonzhang
    Route::get('/xcx/batchCommit', 'XCXController@batchCommit'); //批量上传代码 add by jonzhang
    Route::get('/xcx/batchSubmitAudit', 'XCXController@batchsubmitAudit'); //批量提交审核 add by jonzhang
    Route::match(['get', 'post'], '/uedit', 'UeditorController@index');//编辑器

});

