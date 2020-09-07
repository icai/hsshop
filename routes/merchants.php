<?php
/*
|--------------------------------------------------------------------------
| Merchants Routes
|--------------------------------------------------------------------------
|
| 商家管理后台路由
|
*/
Route::group(['middleware' => ['merchants'], 'namespace'=>'Merchants', 'prefix'=>'merchants'], function()
{
    Route::get('/index/{wid?}','IndexController@index');

    Route::get('/download','IndexController@download'); // 下载文件通用方法 许立 2019/5/10

    /**
     * 管理店铺
     */

    Route::get('/login','AuthController@test');//管理我的店铺
    Route::get('/team','TeamController@index');//管理我的店铺
    Route::match(['get','post'],'/team/create/{id?}','TeamController@create');//创建我的店铺
    Route::match(['get','post'],'/team/template','TeamController@template');//选择店铺模版
    Route::post('/team/delete/{id}','TeamController@delete');//删除我的店铺
    Route::get('/team/getPhone','TeamController@getTeamPhone'); //删除店铺获取手机号
    Route::match(['get','post'],'/team/sendcode/{wid}','TeamController@sendCode'); //删除店铺时发送验证码

    /**
     * 店铺路由
     */
    Route::match(['get','post'],'/store/home','StoreController@home');//店铺概况
    Route::get('/store/indexStore/{wid}','StoreController@indexStore');//自定义店铺
    Route::get('/store','StoreController@index');//微页面
    Route::get('/store/showMicroPage/{option}/{id}','StoreController@showMicroPage');//新建微页面/编辑微页面
    Route::get('/store/indexMicroPage/{wid}/{id}','StoreController@indexMicroPage');//自定义微页面
    Route::post('/store/insertPage','StoreController@insertPage');//添加微页面
    Route::match(['get','post'],'/store/updatePage','StoreController@updatePage');//更改微页面
    Route::match(['get','post'],'/store/deletePage','StoreController@deletePage');//删除微页面
    Route::match(['get','post'],'/store/copyMicroPage','StoreController@copyMicroPage');//复制微页面
    Route::match(['get','post'],'/store/processCategorys','StoreController@processCategorys');//处理分类
    Route::get('/store/selectPage','StoreController@selectPage');//查询微页面数据
    Route::post('/store/updateMicroPageHome','StoreController@updateMicroPageHome');//更改为店铺主页
    Route::get('/store/pagecat','StoreController@pagecat');//页面分类
    Route::post('/store/insertMicroPageType','StoreController@insertMicroPageType');//添加微页面类型
    Route::post('/store/updateMicroPageType','StoreController@updateMicroPageType');//修改微页类型
    Route::match(['post','get'],'/store/deleteMicroPageType','StoreController@deleteMicroPageType');//删除微页类型
    Route::get('/store/pagecatAdd/{id?}','StoreController@pagecatAdd');//新建微页面分类
    Route::get('/store/selectMicroPageType','StoreController@selectMicroPageType');//查询微页面类型数据
    Route::get('/store/drapt','StoreController@drapt');//微页面草稿
    Route::get('/store/userCenter','StoreController@userCenter');//会员主页 此处any上线前会改为对应的post或get
    Route::get('/store/indexHome/{wid?}','StoreController@indexHome');//自定义店铺会员主页
    Route::get('/store/selectMemberHome','StoreController@selectMemberHome');//显示会员主页信息
    Route::post('/store/processMemberHome','StoreController@processMemberHome');//处理会员主页信息
    Route::get('/store/shopNav','StoreController@shopNav');//店铺导航
    Route::any('/store/selectStoreNav','StoreController@selectStoreNav');//显示店铺导航信息
    Route::any('/store/processStoreNav','StoreController@processStoreNav');//添加/更改店铺导航信息
    Route::match(['get','post'], '/store/globalTemplate/{wid?}','StoreController@globalTemplate');//全店风格
    Route::get('/store/ad','StoreController@ad');//公共广告
    Route::get('/store/selectNotice','StoreController@selectNotice');//查询公共广告
    Route::any('/store/processNotice','StoreController@processNotice');//处理公共广告
    Route::get('/store/component','StoreController@component');//自定义模块
    Route::post('/store/insertModule','StoreController@insertModule');//添加自定义模块  add by jonzhang
    Route::match(['get','post'],'/store/updateModule/{num?}','StoreController@updateModule');//修改自定义模块  add by jonzhang
    Route::post('/store/deleteModule','StoreController@deleteModule');//删除自定义模块  add by jonzhang
    Route::match(['get','post'],'/store/componentAdd/{id?}','StoreController@componentAdd');//自定义模块 -新增编辑
    Route::get('/store/attachmentImage','StoreController@attachmentImage');//我的文件 - 图片
    Route::get('/store/attachmentVoice','StoreController@attachmentVoice');//我的文件 - 语音
    Route::get('/store/attachmentVideo','StoreController@attachmentVideo');//我的文件 - 语音
    Route::get('/store/getVideoSign','StoreController@getVideoSign');
    Route::get('/store/getStoreHome','StoreController@getStoreHome');//显示店铺主页信息 add by jonzhagn 2017-05-09
    Route::get('/store/getMemberHome','StoreController@getMemberHome');//显示会员主页信息 add by jonzhang 2017-05-09
    Route::get('/store/getMicroPageType','StoreController@getMicroPageType');//显示微页面分类信息 add by jonzhang 2017-05-09
    Route::get('/store/updateSequenceNumber','StoreController@updateSequenceNumber');//更改微页面对应的序号 add by jonzhang
    Route::get('/store/getCustomtemplate','StoreController@getCustomtemplate');//获取自定义模板信息 add by jonzhang
    Route::get('/store/getTemplateMarket','StoreController@getTemplateMarket');//获取页面模板 add by jonzhang
    Route::get('/store/processCacheData','StoreController@processCacheData');//处理redis缓存
    Route::get('/store/shopUpgrade','StoreController@shopUpgrade');//免费升级店铺角色
    Route::get('/store/closeFrame','StoreController@closeFrame');//关闭弹框
    Route::match(['get','post'],'/store/selectTopNav','StoreOtherController@selectTopNav');//查询微商城首部导航 add by jonzhang
    Route::match(['get','post'],'/store/processTopNav','StoreOtherController@processTopNav');//处理微商城首部导航 add by jonzhang

    Route::match(['get','post'],'/store/getHomeModule','StoreController@getUserCenterModule');
    Route::match(['get','post'],'/store/isOpenWeath','StoreController@isOpenWeath');
    /**
     * 商品路由
     */
    Route::get('/product/index/{status?}','ProductController@index')->where('status', '1|-2|0');//商品库
    Route::match(['get','post'],'/product/create/{id?}','ProductController@create');//发布编辑商品
    Route::get('/product/productGroup','ProductController@productGroup');//商品分组
    Route::match(['get','post'],'/product/createGroup/{id?}','ProductController@createGroup');//新建编辑商品分组
    Route::get('/product/goodsTemplate','ProductController@goodsTemplate');//商品页模版
    Route::get('/product/getGoodsTemplates','ProductController@getGoodsTemplates');//商品页模版列表接口
    Route::match(['get','post'],'/product/createTemplate/{id?}','ProductController@createTemplate');//新建编辑商品页模版
    Route::get('/product/importGoods','ProductController@importGoods');//商品导入 - 外部商品导入
    Route::match(['get','post'],'/product/importMaterial','ProductController@importMaterial');//商品导入 - 导入商品素材
    Route::post('/product/importTaobao','ProductController@importTaobao');//商品导入 - 导入淘宝商品素材
    Route::post('/product/importAli','ProductController@importAli');//商品导入 - 导入阿里巴巴商品素材
    Route::post('/product/importAfanti','ProductController@importAfanti');//商品导入 - 导入阿凡提商品素材
    Route::post('/product/importXCX','ProductController@importXCX');//商品导入 - 导入小程序商品素材
    Route::get('/product/distributionGoods','ProductController@distributionGoods');//分销商品
    Route::post('/product/copy','ProductController@copy');  //复制一个商品

    // 导入会搜云新零售系统商品素材 吴晓平 2019年09月26日
    Route::post('/product/import_card', 'ProductController@importCard');

    Route::get('/product/getproducts','ProductController@index');    //商品查询
    Route::get('/product/getallgroup','ProductController@getAllGroup');    //查询所有商品分组
    Route::get('/product/getallcate','ProductController@getAllCategory');    //查询所有商品类目
    Route::get('/product/getcategories','ProductController@getCategories');//商品分类查询

    Route::get('/product/getgroup','ProductController@getGroup');   //查询单个 商品分组
    Route::post('/product/delgroup','ProductController@delGroup');  //删除单个 商品分组
    Route::post('/product/addgroup','ProductController@createGroup');  //增加单个 商品分组
    Route::get('/product/editgroup/{id}','ProductController@createGroup');  //修改单个 商品分组
    Route::post('/product/editgroup','ProductController@createGroup');  //修改单个 商品分组

    Route::post('/product/del','ProductController@productDel');            //删除单个商品
    Route::post('/product/delbatch','ProductController@batchDel');   //批量删除商品
    Route::post('/product/onoffsale','ProductController@productOnOffSale');  //批量上下架商品
    Route::post('/product/modgroup','ProductController@productModGroup');    //批量修改商品分组
    Route::post('/product/moddiscount','ProductController@productModDiscount');    //批量商品是否参与会员折扣
    Route::post('/product/setFreight','ProductController@setFreight');    //批量商品是否参与会员折扣
    Route::get('/product/exportXlsApi','ProductController@exportXls');    //商品导出接口

    Route::post('/product/setqrdiscount','ProductController@setQrDiscount');    //设置扫码优惠折扣
    Route::get('/product/getqrdiscount','ProductController@getQrDiscount');    //查询扫码优惠折扣
    Route::get('/product/getproducttpl','ProductController@getProductTemplete');  //查询商品模板
    Route::post('/product/modproducttpl','ProductController@modProductTemplete');  //修改商品模板
    Route::get('/product/getvipprop','ProductController@propMemPrice');  //根据商品属性等级查询
    Route::post('/product/setvipprop','ProductController@propMemPrice');  //根据商品属性等级设置

    Route::get('/product/getproduct','ProductController@getProduct');     //查询单个商品
    Route::post('/product/addproduct','ProductController@addProduct');    //添加单个商品
    Route::get('/product/editproduct/{id}','ProductController@setProduct');  //修改单个商品视图
    Route::post('/product/editproduct/{id?}','ProductController@setProduct');  //修改单个商品
    Route::get('/product/getgroup','ProductController@getGroup');   //查询单个 商品分组
    Route::post('/product/delgroup','ProductController@delGroup');  //删除单个 商品分组
    Route::post('/product/delproducttpl','ProductController@delProductTpl');  //删除单个 商品页面模板
    Route::get('/product/getTemplate','ProductController@getTemplate');  //获取商品模板

    Route::get('/product/getfreights','ProductController@getFreights');  //查询所有运费模板列表
    Route::get('/product/getmembercards','ProductController@getMemberCards');  //查询所有会员卡列表
    Route::post('/product/insertProductTemplate','ProductController@insertProductTemplate');  //添加产品模板
    Route::post('/product/updateProductTemplate','ProductController@updateProductTemplate');  //更改产品模板
    Route::post('/product/deleteProductTemplate','ProductController@deleteProductTemplate');  //删除产品模板
    Route::post('/product/selectProductTemplate','ProductController@selectProductTemplate'); //查询商品模板
    Route::get('/product/commodityPreview','ProductController@commodityPreview'); //查看商品预览
    Route::post('/product/updateGoodsTpl','ProductController@updateGoodsTpl'); //add by jonzhang 修改模板

    Route::post('/product/set','ProductController@set');  //设置商品属性

    Route::get('/product/propList','ProductController@propList'); //商品属性列表
    Route::post('/product/addProp','ProductController@addProp'); //添加商品属性
    Route::get('/product/propValues/{propID}','ProductController@propValues'); //商品属性列表
    Route::post('/product/addPropValue','ProductController@addPropValue'); //添加商品属性值
    Route::post('/product/editPropValue','ProductController@editPropValue'); //添加商品属性值
    Route::post('/product/getSku','ProductController@getSku'); //获取商品规格列表
    Route::get('/product/getQRCode','ProductController@getQRCode'); //商品详情页二维码
    Route::get('/product/downloadQRCode','ProductController@downloadQRCode'); //下载商品详情页二维码
    Route::get('/product/getLiteAppQRCode','ProductController@getLiteAppQRCode'); //商品详情页二维码
    Route::get('/product/downloadLiteAppQRCode','ProductController@downloadLiteAppQRCode'); //下载商品详情页二维码
    Route::post('/product/batchEdit','ProductController@batchEdit'); // 批量修改商品

    /**
     * 订单路由
     */
    Route::get('/order','OrderController@index');//订单概况
    Route::get('/order/orderList/{menu?}/{nav?}','OrderController@orderList')->where('menu', '[0-2]')->where('nav', '[0-2]');//订单列表
    Route::post('/order/setStar','OrderController@setStar');//订单设置星级
    Route::post('/order/setAdminDel','OrderController@setAdminDel');//删除订单
    Route::post('/order/setSellerRemark','OrderController@setSellerRemark');//订单设置商家备注
    Route::get('/order/orderDetail/{id}/{notify_id?}','OrderController@orderDetail');//订单详情 添加路由参数notify_id，区分消息已读未读 hsz 2018/6/25
    Route::get('/order/evaluateOrder/{level?}','OrderController@evaluateOrder')->where('level', '[0-3]');//评价列表
    Route::post('/order/evaluateReply','OrderController@evaluateReply');//评论回复
    Route::post('/order/evaluateDelete','OrderController@evaluateDelete');//评论删除
    Route::get('/order/distributionOrder','OrderController@distributionOrder');//分销采购单
    Route::any('/order/export','OrderController@export');//分销采购单
    Route::get('/order/setOrderStatus/{id}','OrderController@setOrderStatus');//订单发货
    Route::match(['get','post'],'/order/clearOrder/{id}','OrderController@clearOrder');//取消订单
    Route::get('/order/upOrderPrice/{id}','OrderController@upOrderPrice');//修改价格
    Route::get('/order/stateMentDetail/{id}/{notify_id?}','OrderController@stateMentDetail');//核销结算 添加路由参数notify_id，区分消息已读未读 hsz 2018/6/25
    Route::get('/order/printOrder','OrderController@printOrder');//快速打单 何书哲 2018年6月26日
    Route::match(['get','post'],'/order/printOrderParams','OrderController@printOrderParams');//快速100参数设置 何书哲 2018年6月26日
    Route::match(['get','post'],'/order/fastPrint','OrderController@fastPrint');//快速100快速打印快递单 何书哲 2018年6月28日
    Route::match(['get','post'],'/order/importOrderLogistics','OrderController@importOrderLogistics');//快速100快速批量导入 何书哲 2018年6月30日
    Route::get('/order/batchDeliveryLog','OrderController@batchDeliveryLog');//批量发货日志 何书哲 2018年6月30日
    Route::post('/order/contactUser','OrderController@contactUser');//联系用户 梅杰 2019/3/13 14:26

    Route::get('/order/hexiaoOrder','OrderController@hexiaoOrder');  //核销订单列表
    Route::match(['get','post'],'/order/finishOrder','OrderController@finishOrder'); //核销订单结单
    Route::match(['get','post'],'/order/setHexiaoRemark','OrderController@setHexiaoRemark'); //核销订单设置备注

    Route::match(['get','post'],'/order/changeSendOrderAddr','OrderController@changeSendOrderAddr');

    Route::post('/order/changePrice','OrderController@changePrice');//订单改价
    Route::match(['get','post'],'/order/delivery','OrderController@delivery');//订单发货
    Route::get('/order/getLogistics/{id}','OrderController@getLogistics');//获取订单物流信息
    Route::get('/order/delay/{id}','OrderController@delay');//订单申请延期收货
    Route::match(['get','post'],'/order/modifyLogistics/{id}','OrderController@modifyLogistics');//修改物流信息
    Route::post('/order/refundAgreeReturn/{refundID}/{oid}/{pid}','OrderController@refundAgreeReturn');//同意退货
    Route::match(['get','post'],'/order/refundAgree/{refundID}/{oid}/{pid}','OrderController@refundAgree');//同意退款
    Route::match(['get','post'],'/order/refundDisagree/{refundID}/{oid}/{pid}','OrderController@refundDisagree');//拒绝退款
    Route::match(['get','post'],'/order/refundComplete/{oid}/{pid}', 'OrderController@refundComplete'); // 商家打款 退款完成
    Route::match(['get','post'],'/order/getDetail/{id}', 'OrderController@getDetail'); // 订单详情接口
    Route::get('/order/refundDetail/{oid}/{pid}/{propID}','OrderController@refundDetail');//订单退款详情
    Route::match(['get','post'],'/order/printExpress', 'OrderController@printExpress'); // 打印快递接口
    Route::match(['get','post'],'/order/printExpressApi', 'OrderController@printExpressApi'); // 打印快递接口
    Route::match(['get','post'],'/order/salePrint', 'OrderController@salePrint'); // 销售单打印
    Route::match(['get','post'],'/order/salePrintApi', 'OrderController@salePrintApi'); // 销售单打印
    Route::post('/order/refundAddMessage/{refundID}/{oid}', 'OrderController@refundAddMessage'); // 退款添加留言
    Route::get('/order/getDistribute/{oid}','OrderController@getDistribute');//订单分销详情
    Route::match(['get','post'],'/order/addEvaluateClassify', 'OrderController@addEvaluateClassify'); // 添加订单评价分类接口
    Route::match(['get','post'],'/order/getEvaluateClassify', 'OrderController@getEvaluateClassify'); // 获取订单评价分类接口
    Route::match(['get','post'],'/order/addProductEvaluateClassify', 'OrderController@addProductEvaluateClassify'); // 添加评论分类
    Route::match(['get','post'],'/order/makeCompleteGroups/{id}', 'OrderController@makeCompleteGroups'); // 使订单成团
    Route::get('/order/orderExportCsv','OrderController@orderExportCsv');//订单批量导出
    Route::match(['get','post'],'/order/salePrint', 'OrderController@salePrint'); // 销售单打印
    Route::match(['get','post'],'/order/salePrintApi', 'OrderController@salePrintApi'); // 销售单打印
    Route::post('/order/manuallyRefundSuccess', 'OrderController@manuallyRefundSuccess'); // 许立 2018年7月2日 标记退款完成

    /*批量发货相关接口*/
    Route::post('/order/BatchDelivery','OrderController@BatchDelivery');//批量发货导入csv接口
    Route::get('/order/BatchDeliveryTemplate','OrderController@BatchDeliveryTemplate');//批量发货模板文件下载接口

    //享立减订单助减者列表
    Route::match(['get','post'],'/order/shareEvent/member/list','OrderController@getShareEventMember');
    Route::match(['get','post'],'/reward/set','ShareEventController@rewardSet');  //享立减红包设置
    Route::match(['get','post'],'/reward/open','ShareEventController@openReward');  //红包设置开关

    Route::get('/shareEvent/extendQrCode','ShareEventController@extendQrCode');  //获取微商城活动二维码
    Route::get('/shareEvent/extendQrCodeXcx','ShareEventController@extendQrCodeXcx');  //获取微商城活动二维码
    Route::get('/shareEvent/qrCodeDownload','ShareEventController@qrCodeDownload');  //下载微商城活动二维码
    Route::get('/shareEvent/qrCodeDownloadXcx','ShareEventController@qrCodeDownloadXcx');  //下载小程序活动二维码


    /*资产路由*/
    Route::get('/capital','CapitalController@index');//我的收入
    Route::any('/capital/rechargeMoney','CapitalController@rechargeMoney');//充值
    Route::get('/capital/withdrawals','CapitalController@withdrawals');//提现
    Route::get('/capital/withdrawalSetting','CapitalController@withdrawalSetting');//设置提现账号
    Route::get('/capital/transactionRecord/{status?}','CapitalController@transactionRecord')->where('status', '[1-4]');//交易记录
    Route::match(['get','post'],'/capital/billSummary/','CapitalController@billSummary');//对账单-账单汇总
    Route::get('/capital/billSummaryContent/{type}/{year}/{month}/{day?}','CapitalController@billSummaryContent');//对账单-账单汇总详情
    Route::get('/capital/billDetail','CapitalController@billDetail');//对账单-账单明细
    Route::get('/capital/billDetailContent','CapitalController@billDetailContent');//对账单-账单明细详
    Route::get('/capital/withdrawalRecord','CapitalController@withdrawalRecord');//提现记录
    Route::get('/capital/bailRecord','CapitalController@bailRecord');//保证金记录
    Route::get('/capital/disabledBalance','CapitalController@disabledBalance');//不可用余额
    Route::get('/capital/serviceOrdering','CapitalController@serviceOrdering');//服务市场-服务订购
    Route::get('/capital/bulkPurchase','CapitalController@bulkPurchase');//服务市场-批量采购
    Route::get('/capital/cdkeyExchange','CapitalController@cdkeyExchange');//服务市场-激活码兑换
    Route::get('/capital/myService','CapitalController@myService');//订购关系-我的服务
    Route::get('/capital/orderRecord','CapitalController@orderRecord');//订购关系-订购记录
    Route::get('/capital/virtualCurrency','CapitalController@virtualCurrency');//有赞币
    Route::get('/capital/inviteRewards','CapitalController@inviteRewards');//邀请奖励
    Route::get('/capital/invoiceManagement','CapitalController@invoiceManagement');//发票管理

    /**
     * 分销路由
     */
    Route::get('/distribute','DistributeController@index');//分销
    Route::get('/distribute/template','DistributeController@template');//分销模板
    Route::get('/distribute/commission','DistributeController@commission');//佣金发放
    Route::get('/distribute/exportXls','DistributeController@exportXlsApi'); //佣金发放信息导出   fuguowei
    Route::get('/distribute/partner','DistributeController@partner');//分销合伙人
    Route::get('/distribute/open/{status}','DistributeController@open');//开启关闭分销
    Route::match(['get','post'],'/distribute/addDistributeGrade','DistributeController@addDistributeGrade');//分销门槛

    Route::match(['get','post'],'/distribute/applyDistribut/{status}','DistributeController@applyDistribut');//开启分销申请
    Route::match(['get','post'],'/distribute/applyList','DistributeController@applyList');//申请成为分销页面模板
    Route::match(['get','post'],'/distribute/delApplyList','DistributeController@delApplyList');//批量删除模板
    Route::match(['get','post'],'/distribute/addApplyPage','DistributeController@addApplyPage');//添加分销申请模板
    Route::match(['get','post'],'/distribute/applayMemberList','DistributeController@applayMemberList');//分销申请人列表
    Route::match(['get','post'],'/distribute/purge/{mid}','DistributeController@purge');//分销清退
    Route::match(['get','post'],'/distribute/purgeLog','DistributeController@purgeLog');//分销清退列表
    Route::match(['get','post'],'/distribute/checkApplyMember/{id}/{status}','DistributeController@checkApplyMember');//审核申请
    Route::match(['get','post'],'/distribute/autoCheck/{status}','DistributeController@autoCheck');//开启关闭自动审核
    Route::match(['get','post'],'/distribute/qrCode','DistributeController@qrCode');//获取二维码

    Route::match(['get','post'],'/distribute/choice','DistributeController@choice');//选择分销
    Route::get('/distribute/partnerIncome','DistributeController@partnerIncome');//分销收入
    Route::get('/distribute/partnerContacts','DistributeController@partnerContacts');//分销人脉

    Route::get('/distribute/choice','DistributeController@choice');//选择分销模板
    Route::match(['get','post'],'/distribute/addTemplate','DistributeController@addTemplate');//添加分销模板
    Route::get('/distribute/del/{id}','DistributeController@del');//删除分销模板
    Route::get('/distribute/setTemplate/{id}','DistributeController@setTemplate');//统一设置分销模板
    Route::get('/distribute/copy/{id}','DistributeController@copy');//复制一条模板
    Route::get('/distribute/getTemplate','DistributeController@getTemplate');//获取分销模板
    Route::get('/distribute/cashLog','DistributeController@cashLog');//佣金发放记录
    Route::get('/distribute/agree/{id}/{status}','DistributeController@agree');//同意发放佣金
    Route::get('/distribute/refuse/{id}','DistributeController@refuse');//拒绝提现
    Route::get('/distribute/reDistribute/{oid}','DistributeController@reDistribute');//重新分钱
    Route::match(['get','post'],'/distribute/getMember','DistributeController@getMember');//获取分销合伙人信息
    Route::match(['get','post'],'/distribute/relationship/{id}','DistributeController@relationship');//获取人脉
    Route::match(['get','post'],'/distribute/getIncome/{mid}','DistributeController@getIncome');//获取佣金流水
    Route::match(['get','post'],'/distribute/getDistributeMember','DistributeController@getDistributeMember');//获取没有上级的用户列表
    Route::match(['get','post'],'/distribute/addJunior','DistributeController@addJunior');//添加下级
    Route::match(['get','post'],'/distribute/addCash','DistributeController@addCash');//添加佣金
    Route::match(['get','post'],'/distribute/openCompanyPay','DistributeController@openCompanyPay');//开启企业打款
    Route::match(['get','post'],'/distribute/withdrawGrade','DistributeController@withdrawGrade');//开启关闭提现门槛
    Route::match(['get','post'],'/distribute/addStoreDistributeGrade','DistributeController@addStoreDistributeGrade');//添加购买分销员设置
    Route::match(['get','post'],'/distribute/delStoreDistributeGrade','DistributeController@delStoreDistributeGrade');//删除分销员设置
    Route::match(['get','post'],'/distribute/setMemberDistributeGrade','DistributeController@setMemberDistributeGrade');//设置用户分销等级
    Route::match(['get','post'],'/distribute/getDistributeGrade','DistributeController@getDistributeGrade');//获取店铺等级
    Route::match(['get','post'],'/distribute/setDistributeTopLevel','DistributeController@setDistributeTopLevel');//设置分销顶级


    /*广告来源路由*/
    Route::match(['get','post'],'/distribute/getSourceInfo','DistributeController@getSourceInfo');//广告来源统计页面
    Route::match(['get','post'],'/distribute/refresh','DistributeController@refresh');//刷新当天数据
    Route::match(['get','post'],'/distribute/getGroupsInfo','DistributeController@getGroupsInfo');//获取留言信息
    Route::match(['get','post'],'/distribute/getOrderList','DistributeController@getOrderList');//获取订单信息




    /**
     * 客户路由
     */
    /*
    Route::get('/member','MemberController@index');//客户概况
    Route::get('/member/label','MemberController@label');//标签管理
    Route::get('/member/label/add','MemberController@labelAdd');//新建标签
    Route::get('/member/membercard','MemberController@membercard');//查看会员卡
    Route::get('/member/membercard/add','MemberController@membercardAdd');//添加会员卡
    Route::get('/member/fans','MemberController@fans');//粉丝管理
    Route::get('/member/fans/screen','MemberController@fansScreen');//粉丝等级筛选
    Route::get('/member/score','MemberController@score');//积分规则
    Route::get('/member/score/add','MemberController@scoreAdd');//新建积分规则
    */
    /**
     * 客户路由
     */
    Route::get('/member','MemberController@index');//客户概况
    Route::any('/member/customer','MemberController@customer');//客户管理
    Route::any('/member/info','MemberController@info');//注册信息
    Route::get('/member/members/{card_id?}','MemberController@members');//会员管理
    Route::get('/member/import','MemberController@membersImport');//导入会员
    Route::any('/member/add_import','MemberController@addImport');//新建导入会员
    Route::get('/member/label/add','MemberController@labelAdd');//新建标签
    Route::any('/member/label/{list?}','MemberController@label');//标签管理
    Route::get('/member/label/del/{id}','MemberController@labelDel');//删除标签
    Route::any('/member/membercard','MemberController@membercard');//查看会员
    Route::any('/member/storageValue','MemberController@storageValue');//会员储值
    Route::any('/member/storageValueAdd','MemberController@storageValueAdd');//新增储值规则
    Route::any('/member/addBalanceBySystem','MemberController@addBalanceBySystem');//后台添加余额
    Route::any('/member/delBalanceRule','MemberController@delBalanceRule');//删除规则
    Route::match(['get','post'],'/member/addBalanceRule','MemberController@addBalanceRule'); //
    Route::match(['get','post'],'/member/getMemberBalaceLog','MemberController@getMemberBalaceLog'); //
    Route::match(['get','post'],'/member/updateRemark','MemberController@updateRemark');
    Route::match(['post'],'/member/grantCardToMember','MemberController@grantCardToMember');//发放会员卡
    Route::match(['post'],'/member/deleteMemberCard','MemberController@deleteMemberCard');//删除指定会员的会员卡
    Route::match(['get'],'/member/getUnclaimedMemberCardList','MemberController@getUnclaimedMemberCardList');//获取指定会员的会员卡
    Route::match(['get'],'/member/getMemberCardList','MemberController@getMemberCardList');//会员卡列表
    Route::match(['get'],'/member/getOneMemberMemberCardList','MemberController@getOneMemberMemberCardList');//会员卡列表

    Route::any('/member/storageRecord','MemberController@storageRecord');//储值记录
    Route::any('/member/membercard/obtain','MemberController@membercardObtain');//会员领取记录
    Route::any('/member/membercard/refund','MemberController@membercardRefund');//会员退卡记录
    Route::get('/member/membercard/add/{id?}/{card_status?}','MemberController@membercardAdd');//添加会员卡
    Route::post('/member/membercard/delete','MemberController@membercardDelete');//删除会员卡
    Route::post('/member/membercard/disableCard/{id}','MemberController@disableCard');//禁用会员卡
    Route::match(['get','post'],'/member/memberCard/down_qrcode','MemberController@downQrcode');  //下载二维码
    Route::match(['get','post'],'/member/memberCard/putCard','MemberController@putCard');  //创建二维码
    Route::get('/microPage/memberCard','MemberController@getUsefulCard');//微页面获取会员卡接口
    Route::post('/microPage/setMemberCard','MemberController@setShopMemberCard');//微页面设置会员卡接口
    //add MayJay
    Route::post('/member/putXcxMemberCard','MemberController@putXcxMemberCard');//会员卡领取小程序码
    Route::get('/member/downloadXcxMemberCardCode','MemberController@downloadXcxMemberCard');//会员卡领取小程序码


    Route::any('/member/fans','MemberController@fans');//粉丝管理
    Route::any('/member/fans/edit','MemberController@fansEdit');//粉丝管理
    Route::get('/member/fans/screen/{status?}','MemberController@fansScreen');//粉丝等级筛选

    Route::get('/member/point/indexPoint','PointController@indexPoint');//add by jonzhang
    Route::get('/member/point/addPointApplyRule','PointController@addPointApplyRule');//积分消耗 添加 add by jonzhang
    Route::get('/member/point/updatePointApplyRule','PointController@updatePointApplyRule');//积分消耗 更改 add by jonzhang
    Route::get('/member/point/selectPointApplyRule','PointController@selectPointApplyRule');//显示消费积分规则 add by jonzhang
    Route::match(['get','post'],'/member/point/processPointRule','PointController@processPointRule'); //积分生成 add by jonzhang
    Route::get('/member/point/selectPointRule','PointController@selectPointRule'); //显示积分生成规则 add by jonzhang
    Route::get('/member/point/updateStorePointStatus','PointController@updateStorePointStatus'); //更改店铺积分状态 add by jonzhang
    Route::get('/member/point/selectStorePointStatus','PointController@selectStorePointStatus');//查询店铺积分开关按钮是否开启 add by jonzhang
    Route::get('/member/point/addPointBySystem','PointController@addPointBySystem'); //通过系统给会员添加积分 add by jonzhang
    Route::get('/member/point/selectPointRecord','PointController@selectPointRecord');//用户积分变化记录 add by jonzhang
    Route::match(['get','post'],'/member/point/processSign','PointController@processSign');//添加或更改签到规则信息 add by jonzhang
    Route::get('/member/li/registerList','MemberController@registerList'); //独立享立减2 herry
    Route::get('/member/li/exportXls','MemberController@exportXlsApi');//注册信息导出接口  fuguowei
    Route::post('/member/li/user','MemberController@user'); //绑定账号和注册短信

    //客户黑名单
    Route::get('/member/blackList','MemberController@memberBlackList'); //黑名单客户列表
    Route::match(['get','post'],'/member/setMemberType','MemberController@setMemberType'); //设置用户类型（拉入黑名单或移出）

    /**
     * 数据统计路由
     */
    Route::get('/statistics','StatisticsController@index');//数据概况
    Route::get('/statistics/pagedata','StatisticsController@pageData');//页面流量
    Route::get('/statistics/daystraffic','StatisticsController@daysTraffic');//按每天流量分析
    Route::get('/statistics/goods','StatisticsController@goods');//商品分析
    Route::get('/statistics/coupons','StatisticsController@coupons');//卡券统计
    Route::get('/statistics/transaction','StatisticsController@transaction');//交易分析
    Route::get('/statistics/shops/index','StatisticsController@shopAnalysis');//店铺分析
    Route::get('/statistics/shops/dailyData','StatisticsController@dailyData');//店铺分析
    Route::get('/statistics/shops/export','StatisticsController@export');//店铺分析
    Route::get('/statistics/customer/index','StatisticsController@customer');//客户分析--客户概况
    Route::get('/statistics/customer/fans','StatisticsController@fans'); //客户分析---粉丝分析
    Route::get('/statistics/customer/fansLayering','StatisticsController@fansLayering'); //客户分析---粉丝分层
    Route::get('/statistics/customer/fansInfo','StatisticsController@fansInfo'); //客户分析---粉丝信息
    Route::get('/statistics/customer/fansInteract','StatisticsController@fansInteract'); //客户分析---粉丝互动
    /**
     * 营销路由
     */
    Route::get('/marketing','MarketingController@index');//营销中心
    Route::get('/marketing/wechatMp','MarketingController@wechatMp');//营销中心 - 微信公众号
    Route::get('/marketing/weibo','MarketingController@weibo');//营销中心 - 微博 - 微博帐号
    Route::get('/marketing/messagepush','MarketingController@messagepush');//营销中心 - 消息推送

    Route::get('/marketing/coupons/{status?}','MarketingController@coupons');//营销中心 - 优惠卷
    Route::any('/marketing/coupon/set/{id?}','MarketingController@couponSet');//营销中心 - 优惠卷添加修改
    Route::any('/marketing/coupon/delete','MarketingController@couponDelete');//营销中心 - 优惠卷删除
    Route::any('/marketing/coupon/invalid','MarketingController@couponInvalid');//营销中心 - 优惠卷使失效
    Route::get('/marketing/couponReceiveList/{id}/{status?}', 'MarketingController@couponReceiveList'); // 某优惠券的领取记录
    Route::get('/marketing/couponcode','MarketingController@couponcode');//营销中心 - 优惠码
    Route::get('/marketing/achieveGive','MarketingController@achieveGive');//营销中心 - 减满/送
    Route::get('/marketing/achieveGive/add','MarketingController@achieveGiveAdd');//营销中心 - 减满/送添加
    Route::get('/marketing/togetherGroup','MarketingController@togetherGroup');//营销中心 - 多人拼团购买权限页
    Route::get('/marketing/togetherGroupList','MarketingController@togetherGroupList');//营销中心 - 多人拼团列表页
    Route::get('/marketing/togetherGroupAdd','MarketingController@togetherGroupAdd');//营销中心 - 多人拼团新增页
    Route::get('/marketing/getRemark','MarketingController@getRemark');//团购留言
    Route::get('/marketing/groupBuy','MarketingController@groupBuy');//营销中心 - 团购购买权限
    Route::get('/marketing/groupBuyList','MarketingController@groupBuyList');//营销中心 - 团购列表
    Route::get('/marketing/groupBuy/add','MarketingController@groupBuyAdd');//营销中心 - 团购添加
    Route::get('/marketing/groupBuy/content','MarketingController@groupBuyContent');//营销中心 - 团购内容页
    Route::get('/marketing/discount','MarketingController@discount');//营销中心 - 现实折扣
    Route::get('/marketing/discountAdd','MarketingController@discountAdd');//营销中心 - 现实折扣添加
    Route::get('/marketing/gift','MarketingController@gift');//营销中心 - 现实折扣添加
    Route::get('/marketing/gift/add','MarketingController@giftAdd');//营销中心 - 现实折扣添加
    Route::get('/marketing/cutsBuy','MarketingController@cutsBuy');//营销中心 - 降价拍
    Route::get('/marketing/cutsBuy/add','MarketingController@cutsBuyAdd');//营销中心 - 降价拍
    Route::get('/marketing/orderCash','MarketingController@orderCash');//营销中心 - 订单返现
    Route::get('/marketing/orderCash/add','MarketingController@orderCashAdd');//营销中心 - 新建订单返现
    Route::get('/marketing/payGift','MarketingController@payGift');//营销中心 -  支付有礼
    Route::get('/marketing/seckills/{status?}','MarketingController@seckills');//营销中心 -  秒杀
    Route::any('/marketing/seckill/set/{id?}','MarketingController@seckillSet');//营销中心 - 秒杀添加
    Route::get('/marketing/seckill/products','MarketingController@seckillProducts');//营销中心 - 秒杀商品列表
    Route::get('/marketing/seckill/detail/{id}','MarketingController@seckillDetail');//营销中心 - 秒杀详情
    Route::post('/marketing/seckill/delete','MarketingController@seckillDelete');//营销中心 - 秒杀删除
    Route::post('/marketing/seckill/invalidate','MarketingController@seckillInvalidate');//营销中心 - 秒杀使失效
    Route::get('/getSeckillQRCode','MarketingController@getSeckillQRCode'); //秒杀详情二维码
    Route::get('/downloadSeckillQRCode','MarketingController@downloadSeckillQRCode'); //下载秒杀详情二维码
    Route::get('/marketing/packagebuy','MarketingController@packagebuy');//营销中心 -  优惠套餐
    Route::get('/marketing/bale','MarketingController@bale');//营销中心 -  打包一口价
    Route::get('/marketing/sign','MarketingController@sign');//营销中心 -  签到
    Route::get('/marketing/verifycard/{uri?}','MarketingController@verifycard');//营销中心 -  卡券验证
    Route::get('/marketing/salesman/{list?}','MarketingController@salesman');//营销中心 -   销售员
    Route::get('/marketing/hotel','MarketingController@hotel');//营销中心 -   酒店预订
    Route::get('/marketing/apps/wheel','MarketingController@wheel');//营销中心 -   酒店预订

    Route::match(['get','post'],'/marketing/egg/index','MarketingController@smokedEggIndex');//营销中心 - 砸金蛋
    Route::match(['get','post'],'/marketing/egg/add','MarketingController@smokedEggAdd');//营销中心 - 砸金蛋活动添加
    Route::match(['get','post'],'/marketing/egg/edit/{eggId}','MarketingController@smokedEggEdit');//营销中心 - 砸金蛋活动编辑修改
    Route::get('/marketing/egg/del/{eggId?}','MarketingController@smokedEggDel');//营销中心 - 砸金蛋活动删除
    Route::get('/marketing/egg/stop/{eggId?}','MarketingController@smokedEggStop');//营销中心 - 砸金蛋活动手动终止
    Route::post('/marketing/score/add','MarketingController@addScore');//营销中心 - 添加积分奖库
    Route::get('/marketing/score/get','MarketingController@getScore');//营销中心 - 获取积分奖库
    Route::get('/marketing/egg/member/list/{eggId}','MarketingController@getEggMemberList');//营销中心 - 获取参与者列表
    Route::get('/marketing/egg/getCouponList','MarketingController@getCouponList');

    //小程序
    Route::get('/marketing/liteapp','MarketingController@liteapp');//营销中心 - 小程序设置首页
    Route::get('/marketing/litePage','MarketingController@litePage');//营销中心 - 小程序微页面列表
    Route::get('/marketing/liteAddPage','MarketingController@liteAddPage');//营销中心 - 小程序微页面列表
    Route::match(['get','post'],'/marketing/liteappConfig','MarketingController@liteappConfig');//营销中心 - 小程序设置
    Route::match(['get','post'],'/marketing/liteappInfo','MarketingController@liteappInfo');//营销中心 - 小程序配置信息
    Route::get('/marketing/xcxShopNav','MarketingController@xcxShopNav');//营销中心 - 小程序底部导航
    Route::match(['get','post'],'/marketing/liteStatistics','MarketingController@liteStatistics');//营销中心 - 小程序数据统计
    Route::match(['get','post'],'/marketing/xcx/topnav','MarketingController@topnav');//营销中心 - 小程序顶部导航
    Route::match(['get','post'],'/xcx/selectTopNav','XCXOtherController@selectTopNav');//查询小程序首部导航
    Route::match(['get','post'],'/xcx/processTopNav','XCXOtherController@processTopNav');//处理小程序首部导航
    Route::get('marketing/xcx/list','MarketingController@xcxList'); //店铺小程序列表
    //add mayjay 20180621
    Route::match(['get','post'],'/marketing/Info','MarketingController@Info');
    Route::match(['post','get'],'marketing/alixcx/list','MarketingController@aliXcxList'); //店铺支付宝小程序列表
    Route::match(['post','get'],'marketing/alixcx/configure','MarketingController@aliXcxConfigure'); //店铺支付宝小程序配置



    Route::get('/coupon/getQRCode','MarketingController@getCouponQRCode'); //优惠券二维码
    Route::get('/coupon/downloadQRCode','MarketingController@downloadCouponQRCode'); //下载优惠券二维码
    Route::get('/couponXcxQrCode/{id}','MarketingController@couponXcxQrCode'); // 许立 2018年08月07日 生成优惠券活动二维码
    Route::get('/downloadCouponXcxQrCode/{id}','MarketingController@downloadCouponXcxQrCode'); // 许立 2018年08月07日 生成优惠券活动二维码

    Route::get('/marketing/orderHexiao','MarketingController@zitiOrderHexiao'); //自提订单核销页面

    /*团购路由*/
    Route::get('/grouppurchase/invalid/{id}','GroupPurchaseController@invalid');//是团购失效
    Route::get('/grouppurchase/del/{id}','GroupPurchaseController@del');//删除团购
    Route::get('/grouppurchase/getProps/{id}','GroupPurchaseController@getProps');//根据商品id获取商品sku
    Route::match(['get','post'],'/grouppurchase/editRule','GroupPurchaseController@editRule');//添加编辑团购
    Route::match(['get','post'],'/grouppurchase/groupList','GroupPurchaseController@groupList');//团购列表接口
    Route::match(['get','post'],'/group/showGroupList','GroupPurchaseController@showGroupList');//查询拼团信息 add by jonzhang


    /*大转盘*/
    Route::match(['post','get'],'/marketing/wheelList','MarketingController@wheelList');//大转盘列表
    Route::get('/marketing/addWheel','MarketingController@addWheel');//添加大转盘
    Route::match(['get','post'],'/marketing/saveWheel','MarketingController@saveWheel');//保存大转盘数据
    Route::match(['get','post'],'/marketing/getCode','MarketingController@getCode');//获取二维码
    Route::get('/marketing/delWheel/{id}','MarketingController@delWheel');//删除大转盘活动
    Route::get('/marketing/wheelLog/{id}','MarketingController@wheelLog');//删除大转盘活动
    Route::get('/marketing/wheelCount/{wheelId}','MarketingController@wheelCount');//大转盘统计
    Route::get('/marketing/wheelQrCode/{id}','MarketingController@wheelQrCode'); // 许立 2018年08月16日 获取微商城大转盘活动二维码
    Route::get('/marketing/wheelQrCodeDownload/{id}','MarketingController@wheelQrCodeDownload'); // 许立 2018年08月16日 下载微商城大转盘活动二维码
    Route::get('/marketing/wheelQrCodeXcx/{id}','MarketingController@wheelQrCodeXcx'); // 许立 2018年08月16日 获取小程序大转盘活动二维码
    Route::get('/marketing/wheelQrCodeXcxDownload/{id}','MarketingController@wheelQrCodeXcxDownload'); // 许立 2018年08月16日 下载小程序大转盘活动二维码


    /*刮刮卡*/
    Route::match(['post','get'],'/marketing/scratchList','MarketingController@scratchList');//刮刮卡列表
    Route::get('/marketing/addScratch/{id?}','MarketingController@addScratch');//添加or编辑刮刮卡
    Route::match(['get','post'],'/marketing/saveScratch','MarketingController@saveScratch');//保存刮刮卡数据
    Route::get('/marketing/delScratch','MarketingController@delScratch');//删除刮刮卡活动
    Route::get('/marketing/scratchCount/{scratchId}','MarketingController@scratchCount');//刮刮卡统计
    Route::match(['post','get'],'/marketing/getUsefulScratch','MarketingController@getUsefulScratch');//微页面刮刮卡列表
    Route::get('/marketing/scratchQrCode/{id}','MarketingController@scratchQrCode'); // 何书哲 2018年08月24日 获取微商城刮刮卡活动二维码
    Route::get('/marketing/scratchQrCodeDownload/{id}','MarketingController@scratchQrCodeDownload'); // 何书哲 2018年08月24日 获取下载微商城刮刮卡活动二维码
    Route::get('/marketing/scratchQrCodeXcx/{id}','MarketingController@scratchQrCodeXcx'); // 何书哲 2018年08月24日 获取小程序刮刮卡活动二维码
    Route::get('/marketing/scratchQrCodeXcxDownload/{id}','MarketingController@scratchQrCodeXcxDownload'); // 何书哲 2018年08月24日 获取下载小程序刮刮卡活动二维码

    /**
     * 营销活动
     */
    Route::get('/marketing/apps/{action}',function($action){
        $ctrl = \App::make(\App\Http\Controllers\Merchants\MarketingController::class);
        return \App::call([$ctrl, $action]);
    });


    /**
     * 营销推送消息路由
     */
    Route::get('/marketing/pushstatistics','MarketingController@pushStatistics');//营销中心 - 消息推送 -推送统计
    Route::get('/marketing/msgrecharge','MarketingController@msgRecharge');//营销中心 -消息推送 - 短信充值
    Route::get('/marketing/expediting','MarketingController@expediting');//营销中心 - 消息推送 - 订单催付
    Route::get('/marketing/paysuccess','MarketingController@paySuccess');//营销中心 - 消息推送 - 付款成功通知
    Route::get('/marketing/sendnotice','MarketingController@sendNotice');//营销中心 - 消息推送 - 发货提醒
    Route::get('/marketing/signnotice','MarketingController@signNotice');//营销中心 - 消息推送 - 签收提醒
    Route::get('/marketing/agreerefund','MarketingController@agreeRefund');//营销中心 - 消息推送 - 同意退款
    Route::get('/marketing/disagreerefund','MarketingController@disagreeRefund');//营销中心 - 消息推送 - 拒绝退款
    Route::get('/marketing/takeorder','MarketingController@takeOrder');//营销中心 - 消息推送 - 接单提醒
    Route::get('/marketing/distakeorder','MarketingController@distakeOrder');//营销中心 - 消息推送 - 拒绝接单提醒
    Route::get('/marketing/verifynotice','MarketingController@verifyNotice');//营销中心 - 消息推送 - 核销提醒
    Route::get('/marketing/getvipcard','MarketingController@getVipcard');//营销中心 - 消息推送 - 获得会员卡提醒
    Route::get('/marketing/vipupgrade','MarketingController@vipUpgrade');//营销中心 - 消息推送 - 会员卡升级提醒
    Route::get('/marketing/salemanrelation','MarketingController@salemanRelation');//营销中心 - 消息推送 - 销售员关系通知
    Route::get('/marketing/salemanorder','MarketingController@salemanOrder');//营销中心 - 消息推送 - 销售员订单通知
    Route::get('/marketing/viprecharge','MarketingController@vipRecharge');//营销中心 - 消息推送 - 会员储值成功提醒
    Route::get('/marketing/banlancechange','MarketingController@banlanceChange');//营销中心 - 消息推送 - 储值余额变动提醒
    /*满减活动*/
    Route::match(['get','post'],'/marketing/getProductByGroupId','MarketingController@getProductByGroupId');//获取分组商品
    Route::get('/marketing/getProductGroups','MarketingController@getProductGroups');//获取店铺分组
    Route::match(['post','get'],'/marketing/edit','MarketingController@edit');//保存满减活动
    Route::match(['get','post'],'/marketing/getMore','MarketingController@getMore');//获取满减活动更多商品
    Route::get('/marketing/discountList','MarketingController@discountList');//获取满减列表
    Route::get('/marketing/getDiscountInfo/{id}','MarketingController@getDiscountInfo');//满减统计数据
    Route::get('/marketing/invalidate/{id}','MarketingController@invalidate');//满减活动失效
    Route::get('/marketing/delDiscount/{id}','MarketingController@delDiscount');//满减活动删除

    /**
     * 消息提醒
     */
    Route::get('/notification/notificationList', 'NotificationController@notificationList');//消息列表
    Route::get('/notification/getRightNavNotificationList', 'NotificationController@getRightNavNotificationList');//右侧通知栏消息列表
    Route::get('/notification/notificationCount', 'NotificationController@notificationCount');//消息数量
    Route::get('/notification/notificationDetail', 'NotificationController@notificationDetail');//消息详情
    Route::get('/notification/settingList', 'NotificationController@settingList');//设置列表
    Route::get('/notification/settingViewList', 'NotificationController@settingViewList');//消息提醒列表 hsz 2018/6/25
    Route::get('/notification/settingDetail', 'NotificationController@settingDetail');//设置详情
    Route::get('/notification/notificationListView', 'NotificationController@notificationListView');//消息列表页面
    Route::get('/notification/notificationDetailView', 'NotificationController@notificationDetailView');//消息详情页面
    Route::get('/notification/settingListView', 'NotificationController@settingListView');//设置列表页面
    Route::match(['get','post'],'/notification/settingDetailView', 'NotificationController@settingDetailView');//设置详情页面
    Route::post('/notification/notificationSubscribe', 'NotificationController@notificationSubscribe');//消息订阅
    Route::post('/notification/notificationUnsubscribe', 'NotificationController@notificationUnsubscribe');//消息取消订阅
    Route::post('/notification/deleteNotification', 'NotificationController@deleteNotification');//删除消息
    Route::post('/notification/readAllNotification', 'NotificationController@readAllNotification');//全部已读消息

    /**
     * 微论坛
     */
    Route::get('/microforum/settings/list', 'MicroForumController@settingsList');//社区设置-列表
    Route::post('/microforum/settings/listed', 'MicroForumController@settingsListed');//社区设置-保存
    Route::group(['middleware' => 'microforum.serv'], function () {
        Route::get('/microforum/posts/list', 'MicroForumController@postsList');//帖子管理-列表
        Route::post('/microforum/posts/topped', 'MicroForumController@postsTopped');//帖子管理-置顶
        Route::post('/microforum/posts/untopped', 'MicroForumController@postsUntopped');//帖子管理-取消置顶
        Route::post('/microforum/posts/deleted', 'MicroForumController@postsDeleted');//帖子管理-删除
        Route::get('/microforum/posts/release', 'MicroForumController@postsRelease');//帖子管理-发布
        Route::post('/microforum/posts/released', 'MicroForumController@postsReleased');//帖子管理-发布保存
        Route::get('/microforum/posts/edit/{pid}', 'MicroForumController@postsEdit');//帖子管理-编辑
        Route::post('/microforum/posts/edited', 'MicroForumController@postsEdited');//帖子管理-编辑保存
        Route::get('/microforum/evaluates/list/{pid}', 'MicroForumController@evaluatesList');//评价管理-列表
        Route::post('/microforum/evaluates/deleted', 'MicroForumController@evaluatesDeleted');//评价管理-删除
        Route::post('/microforum/evaluates/content', 'MicroForumController@evaluatesContent');//评价管理-获取评论内容
        Route::get('/microforum/categories/list', 'MicroForumController@categoriesList');//分类管理-列表
        Route::get('/microforum/categories/add', 'MicroForumController@categoriesAdd');//分类管理-添加
        Route::post('/microforum/categories/added', 'MicroForumController@categoriesAdded');//分类管理-添加保存
        Route::get('/microforum/categories/edit/{id}', 'MicroForumController@categoriesEdit');//分类管理-编辑
        Route::post('/microforum/categories/edited', 'MicroForumController@categoriesEdited');//分类管理-编辑保存
        Route::post('/microforum/categories/deleted', 'MicroForumController@categoriesDeleted');//分类管理-删除
        Route::get('/microforum/users/list', 'MicroForumController@usersList');//用户管理-列表
        Route::post('/microforum/users/blocked', 'MicroForumController@usersBlocked');//用户管理-拉黒
        Route::post('/microforum/users/unblocked', 'MicroForumController@usersUnblocked');//用户管理-恢复
        Route::get('/microforum/statistics/listView', 'MicroForumController@statisticsListView');//社区统计-列表页面
        Route::get('/microforum/statistics/list', 'MicroForumController@statisticsList');//社区统计-列表
    });

    /**
     * 通用设置
     */
    Route::match(['post','get'],'/currency/index/{wid?}','CurrencyController@index');//店铺信息
    Route::get('/currency/contact','CurrencyController@contact');//联系我们
    Route::get('/currency/outlets','CurrencyController@outlets');//门店管理
    Route::get('/currency/outletsAdd','CurrencyController@outletsAdd');//新建/编辑门店
    Route::match(['post','get'],'/currency/afterSale','CurrencyController@afterSale');//退货/维权
    Route::get('/currency/service','CurrencyController@service');//服务协议
    Route::get('/currency/serviceShop','CurrencyController@serviceShop');//服务商协议
    Route::any('/currency/admin','CurrencyController@admin');//店铺管理
    Route::get('/currency/outlets/getStoreCode','CurrencyController@getStoreCode');//门店二维码
    Route::get('/currency/outlets/downloadStoreXcxCode','CurrencyController@downloadStoreXcxCode');//门店小程序二维码下载


    Route::any('/currency/bindAdmin','CurrencyController@bindWeChat');//管理员绑定微信
    Route::any('/currency/unbindAdmin','CurrencyController@unbindWeChat');//管理员解绑微信
    Route::match(['post','get'],'/currency/adminAdd/{id?}','CurrencyController@adminAdd');//添加管理员
    Route::post('/currency/modifyAdmin','CurrencyController@modifyAdmin');//编辑管理员
    Route::post('/currency/delManger','CurrencyController@delManger');//删除管理员
    Route::get('/currency/partner','CurrencyController@partner');//我的拍档
    Route::any('/currency/partnerDel','CurrencyController@partnerDel');//删除我的拍档
    Route::any('/currency/payment/{id?}','CurrencyController@payment');//支付/交易
    Route::get('/currency/guarantee','CurrencyController@guarantee');//消费保障
    Route::get('/currency/margin','CurrencyController@margin');//消费保障
    Route::get('/currency/orderSet','CurrencyController@orderSet');//订单设置 - 上门自提
    Route::get('/currency/localCity','CurrencyController@localCity');//订单设置 - 同城配送
    Route::get('/currency/express','CurrencyController@express');//订单设置 - 快递发货
    Route::match(['get','post'],'/currency/expressSet/{id?}','CurrencyController@expressSet');//订单设置 - 新增编辑快递模版
    Route::match(['get','post'],'/currency/expressDel/{id}','CurrencyController@expressDel');//订单设置 - 新增编辑快递模版
    Route::post('/currency/expressToggle/{id}','CurrencyController@expressToggle');//订单设置 - 展开或者收缩运费模板
    Route::get('/currency/tradingSet','CurrencyController@tradingSet');//订单设置 - 交易设置
    Route::match(['get','post'], '/currency/generalSet','CurrencyController@generalSet');//通用设置
    Route::match(['get','post'], '/currency/location','CurrencyController@location');//通用设置
    Route::match(['get','post'], '/currency/editAddress','CurrencyController@editAddress');//编辑地址
    Route::get('/currency/delAddress/{id}','CurrencyController@delAddress');//删除地址
    Route::get('/currency/storeList','CurrencyController@storeList');//获取门店列表
    Route::get('/currency/editStore','CurrencyController@editStore');//添加编辑门店
    Route::get('/currency/delStore/{id}','CurrencyController@delStore');//删除门店
    Route::get('/currency/share/set','CurrencyController@shareSet'); //店铺分享设置
    Route::post('/currency/share/addShareInfo','CurrencyController@addShareInfo'); //店铺分享设置
    // Route::get('/currency/task','CurrencyController@task');//掌柜任务

    Route::match(['get','post'],'/currency/phoneKf','CurrencyController@phoneKf');//电话客服
    Route::match(['get','post'],'/currency/weChatKf','CurrencyController@weChatKf');//微信客服
    Route::get('/currency/KfList','CurrencyController@getKfList');//客服列表
    Route::match(['get','post'],'/currency/kefu','CurrencyController@kefu');// qq客服
    Route::match(['get','post'],'/currency/kefuDel/{id}', 'CurrencyController@kefuDel'); // 删除客服

    Route::match(['get','post'],'/currency/getListForAjax','CurrencyController@getListForAjax'); //返回ajax列表数据
    Route::post('/currency/setSmsConf','CurrencyController@setSmsConf'); //保存短信配置信息
    Route::get('/currency/smsConf','CurrencyController@smsConf'); //保存短信
    Route::get('/currency/delSmsConf','CurrencyController@delSmsConf'); //关闭短信配置

    Route::match(['get','post'],'/currency/cert','CurrencyController@cert');
    Route::get('/currency/downLoadCert','CurrencyController@downLoadCert');

    Route::get('/currency/shopLable','CurrencyController@shopLable'); //店铺标签设置
    Route::post('/currency/addShopLable','CurrencyController@addShopLable'); //添加修改标签

    Route::get('/currency/getDefaultAddress', 'CurrencyController@getRefundAddress'); // 获取退货地址

    Route::match(['get','post'],'/currency/commonSetting','CurrencyController@commonSetting'); //通用设置
    Route::match(['get','post'],'/currency/hexiaoQrcode','CurrencyController@hexiaoQrcode'); //吴晓平 2018年10月09日 绑定核销员
    Route::match(['get','post'],'/currency/unsetHexiaoUser','CurrencyController@unsetHexiaoUser'); //吴晓平 2018年10月09日 解除绑定核销员

    /**自提**/
    Route::get('/currency/receptionList','ReceptionController@list'); //自提列表信息页
    Route::get('/currency/editSeception','ReceptionController@editSeception'); //添加\编辑自提信息
    Route::match(['get','post'],'/currency/startZiti','ReceptionController@startZiti'); //设置自提是否启动
    Route::match(['get','post'],'/currency/saveZiti','ReceptionController@saveZiti'); //处理添加/编辑自提信息
    Route::match(['get','post'],'/currency/delZiti','ReceptionController@delReception'); //删除自提
    /**
     * 微信客服
     */
    Route::get('/WeChatCustom/list','WeChatCustomServiceController@getList');//客服列表
    Route::get('/WeChatCustom/CustomList','WeChatCustomServiceController@CustomList');//客服列表
    Route::post('/WeChatCustom/add','WeChatCustomServiceController@addCustom');//添加客服
    Route::post('/WeChatCustom/invite','WeChatCustomServiceController@inviteCustom');//邀请客服
    Route::post('/WeChatCustom/update','WeChatCustomServiceController@updateCustom');//修改客服
    Route::post('/WeChatCustom/delete','WeChatCustomServiceController@deleteCustom');//删除客服
    Route::post('/WeChatCustom/uploadHeadImg','WeChatCustomServiceController@uploadHeadImg');//修改客服



    /**
     * 文件管理系统路由
     */
    Route::post('/myfile/upfile','MyFileController@upFile'); //上传文件
    Route::post('/myfile/setUpxVideo','MyFileController@setUpxVideo'); //上传文件
    Route::post('/myfile/addClassify','MyFileController@addClassify'); //添加分组
    Route::post('/myfile/modifyClassify','MyFileController@modifyClassify'); //修改分组
    Route::post('/myfile/delFile','MyFileController@delFile'); //删除文件
    Route::post('/myfile/delClassify','MyFileController@delClassify'); //删除分组
    Route::post('/myfile/getUserFileByClassify','MyFileController@getUserFileByClassify'); //获取用户文件
    Route::post('/myfile/modifyFileName','MyFileController@modifyFileName'); //修改文件名称
    Route::post('/myfile/modifyVedio','MyFileController@modifyVedio'); //修改文件名称
    Route::get('/myfile/getClassify','MyFileController@getClassify'); //获取文件分组
    Route::get('/myfile/getImgList','MyFileController@getImgList'); //获取店铺列表
    Route::get('/myfile/test','MyFileController@test'); //获取店铺列表
    Route::get('/myfile/getCDNInfo','MyFileController@getCDNInfo'); //获取上传信息
    Route::match(['get','post'],'/myfile/setFile','MyFileController@setFile'); //设置图片信息

    /**
     * 链接到
     */
    Route::get('/linkTo/get','LinkToController@get'); // 链接到

    /**
     * 公众号设置
     */
    Route::match(['get', 'post'], '/wechat/setting','WechatController@setting');
    Route::get('/wechat/wxsettled','WechatController@wxsettled');
    Route::get('/wechat/weixinSet/{param?}','WechatController@weixinSet');
    Route::get('/wechat/errorAuth','WechatController@errorAuth'); //授权失败页面
    Route::get('/wechat/relieveAuth','WechatController@relieveAuth'); //取消授权（解除绑定）
    Route::get('/wechat/authRedirect','WechatController@authRedirect'); //授权跳转接口

    /**
     * 预约管理
     */
    Route::get('/wechat/book','WechatController@book');//预约管理列表页
    Route::match(['get','post'],'/wechat/bookSave','WechatController@bookSave'); //新增\编辑预约管理
    Route::get('/wechat/userList/{book_id}','WechatController@userList');//预约客户列表页
    Route::get('/wechat/delApi','WechatController@delApi');//预约客户删除接口
    Route::get('/wechat/bookDetail','WechatController@chaxun');     //预约客户详情页面
    Route::match(['get','post'],'/wechat/usersAlter','WechatController@usersAlter');  //预约客户处理页面
    Route::match(['get','post'],'/wechat/bookListApi','WechatController@bookListApi'); //微页面请求的数据接口
    Route::match(['get','post'],'/wechat/bookDel','WechatController@bookDel'); //删除预约
    Route::get('/wechat/orderExport','WechatController@orderExport');  //订单导出接口


    /**
     * 小程序
     */
    Route::match(['post','get'],'/xcx/config/processData','XCXController@processConfigData');//处理小程序配置信息
    Route::get('/xcx/config/query','XCXController@selectData');//查询小程序配置信息
    Route::get('/xcx/authorizer','XCXController@startAuthorizer');//小程序二维码url
    Route::get('/xcx/cancelAuthorizer','XCXController@cancelAuthorization');//小程序取消授权
    Route::get('/xcx/liveRoom','XCXController@liveRoom'); // 获取小程序直播间数据 update 焦建荣【945184949@qq.com】2020年03月06日
    Route::match(['post','get'],'/xcx/micropage/select','XCXController@selectAllXCX');//查询小程序微页面
    Route::match(['post','get'],'/xcx/micropage/selectOne','XCXController@selectOneXCX');//查询小程序微页面
    Route::match(['post','get'],'/xcx/micropage/insert','XCXController@insertXCXPage');//添加小程序微页面
    Route::match(['post','get'],'/xcx/micropage/update','XCXController@updateXCXPage');//更新小程序微页面
    Route::match(['post','get'],'/xcx/micropage/delete','XCXController@deleteXCXPage');//删除小程序微页面
    Route::match(['post','get'],'/xcx/micropage/copy','XCXController@copyXCX');//复制小程序微页面
    Route::match(['post','get'],'/xcx/micropage/updateHome','XCXController@updateXCXMainHome');//设为小程序店铺主页
    Route::match(['post','get'],'/xcx/code','XCXController@getXCXCode');//小程序码
    Route::match(['post','get'],'/xcx/unitData','XCXController@processUnitData');//处理小程序流量主信息
    Route::match(['post','get'],'/xcx/micropage/batchDelete','XCXController@batchDeleteXCXPage');//批量删除小程序微页面

    /*小程序底部导航*/
    Route::get('/marketing/footerBar','MarketingController@footerBar'); //小程序底部导航设置页面
    Route::match(['get','post'],'/marketing/getBarDataList','MarketingController@getBarDataList');  //获取小程序底部导航数据
    Route::match(['get','post'],'/marketing/SaveBar','MarketingController@SaveBar'); //小程序底部导航增，删，改操作
    Route::match(['get','post'],'/marketing/isAuthAuditing','MarketingController@isAuthAuditing'); //是否开启自动审核操作
    Route::match(['get','post'],'/marketing/nomalSubmitSave','MarketingController@nomalSubmitSave'); //手动提交审核处理接口
    Route::match(['get','post'],'/marketing/refresh_footerBar','MarketingController@refreshFooterBar'); //手动刷新底部导航数据

    // add by 吴晓平 2018年07月19日
    Route::get('/marketing/footerSyncBar','MarketingController@footerSyncBar'); //同步微信的新页面数据
    Route::match(['get','post'],'/marketing/getSyncSimpleBarDataList','MarketingController@getSyncSimpleBarDataList'); //获取新页面数据

    //add by 吴晓平 2018年07月19日
    Route::match(['get','post'],'/marketing/getSyncBarDataList','MarketingController@getSyncBarDataList');  //获取小程序底部导航数据

    Route::match(['get','post'],'/marketing/getCustomFooterBarList','MarketingController@getCustomFooterBarList'); //获取自定义底部导航列表
    Route::match(['get','post'],'/marketing/addCustomFooterBar','MarketingController@addCustomFooterBar'); //添加自定义底部导航
    Route::match(['get','post'],'/marketing/delCustomFooterBar','MarketingController@delCustomFooterBar'); //删除自定义底部导航

    //数据统计
    Route::match(['post','get'],'/xcx/dailyvisittrend','XCXController@visitTrendForDaily');//日趋势
    Route::match(['post','get'],'/xcx/weeklyvisittrend','XCXController@visitTrendForWeekly');//周趋势
    Route::match(['post','get'],'/xcx/monthlyvisittrend','XCXController@visitTrendForMonthly');//月趋势
    Route::match(['post','get'],'/xcx/visitdistribution','XCXController@visitDistribution');//访问分布

    Route::match(['post','get'],'/xcx/stat/flow','XCXController@statFlow');//统计流量
    Route::match(['post','get'],'/xcx/stat/overview','XCXController@statOverview');//统计概况
    Route::match(['post','get'],'/xcx/stat/trade','XCXController@statTrade');//交易统计

    /**
     * 投票
     */
    Route::get('/marketing/vote','MarketingController@vote');  //投票活动列表页
    Route::match(['get','post'],'/marketing/vote/save','MarketingController@voteSave'); //添加\编辑投票活动
    Route::match(['get','post'],'/marketing/vote/del','MarketingController@voteDel');  //删除投票活动
    Route::match(['get','post'],'/marketing/vote/createQrcode','MarketingController@createQrcode'); //生成\下载投票活动
    Route::get('/marketing/vote/userList','MarketingController@getEnrollUsersList'); //查看参加活动用户列表
    Route::get('/marketing/vote/voteUserList','MarketingController@getVoteUserList'); //查看参加活动用户列表
    Route::match(['get','post'],'/marketing/vote/enrollUserDel','MarketingController@enrollUserDel'); //删除参加投票用户

    // 许立 2018年6月27日 营销活动-调查投票
    Route::get('/marketing/researches/{type}/{status?}','MarketingController@researches');// 许立 2018年08月07日 在线报名在线预约在线预约路由
    Route::match(['get','post'],'/marketing/researchAdd','MarketingController@researchAdd');// 调查添加
    Route::match(['get','post'],'/marketing/researchEdit/{id}','MarketingController@researchEdit');// 调查编辑
    Route::post('/marketing/researchDelete','MarketingController@researchDelete');// 调查删除
    Route::post('/marketing/researchInvalidate','MarketingController@researchInvalidate');// 调查使失效
    Route::get('/marketing/researchMembers/{id}','MarketingController@researchMembers');// 参与人列表
    Route::get('/marketing/researchRecords/{id}/{mid}/{times}','MarketingController@researchRecords');// 参与记录列表
    Route::get('/marketing/researchResult/{id}','MarketingController@researchResult');// 许立 2018年6月27日 选项类型活动结果
    Route::get('/marketing/researchExport/{id}','MarketingController@researchExport');// 许立 2018年7月3日 导出参与人或投票类型活动的投票结果excel
    Route::get('/marketing/researchPreview/{id}','MarketingController@researchPreview');// 何书哲 2018年7月17日 预览调查活动详情
    Route::get('/marketing/getResearchTemplateList','MarketingController@getResearchTemplateList');// 何书哲 2018年7月18日 获取调查模板列表
    Route::get('/marketing/researchXcxQrCode/{id}','MarketingController@researchXcxQrCode');// 许立 2018年08月07日 生成小程序二维码
    Route::get('/marketing/researchXcxQrCodeDownload/{id}','MarketingController@researchXcxQrCodeDownload'); // 许立 2018年08月09日 下载小程序在线报名活动二维码
    Route::get('/marketing/researchQrCode/{id}','MarketingController@researchQrCode');// 许立 2018年08月23日 生成微商城二维码
    Route::get('/marketing/researchQrCodeDownload/{id}','MarketingController@researchQrCodeDownload'); // 许立 2018年08月23日 下载微商城在线报名活动二维码

    // 许立 2018年07月16日 红包活动路由
    Route::get('/marketing/bonus/index/{status?}','BonusController@index'); // 列表
    Route::match(['get','post'],'/marketing/bonus/add','BonusController@add'); // 添加
    Route::match(['get','post'],'/marketing/bonus/edit/{id}','BonusController@edit'); // 编辑
    Route::post('/marketing/bonus/delete/{id}','BonusController@delete'); // 删除
    Route::post('/marketing/bonus/stop/{id}','BonusController@stop'); // 停止
    Route::get('/marketing/bonus/isOn','BonusController@isOn'); // 判断是否有进行中的活动
    Route::post('/marketing/bonus/isTimeValid','BonusController@isTimeValid'); // 许立 2018年07月19日 判断待创建活动是否跟进行中或未来活动有重叠
    Route::get('/marketing/bonus/qrCode','BonusController@qrCode'); // 许立 2018年08月07日 获取微商城红包活动二维码
    Route::get('/marketing/bonus/qrCodeDownload','BonusController@qrCodeDownload'); // 许立 2018年08月07日 下载微商城红包活动二维码
    Route::get('/marketing/bonus/qrCodeXcx','BonusController@qrCodeXcx'); // 许立 2018年08月07日 获取小程序红包活动二维码
    Route::get('/marketing/bonus/qrCodeDownloadXcx','BonusController@qrCodeDownloadXcx'); // 许立 2018年08月07日 下载小程序红包活动二维码

    /**
     * 享立减
     */
    Route::get('/shareEvent/list', 'ShareEventController@list');
    Route::match(['get','post'],'/shareEvent/create/{id?}','ShareEventController@create');
    Route::match(['get','post'],'/shareEvent/del/{id?}','ShareEventController@del');
    Route::post('/shareEvent/refresh','ShareEventController@refreshKey');
    Route::get('/shareEvent/getList','ShareEventController@getList');
    Route::get('/shareEvent/getMinAppQRCode','ShareEventController@getMinAppQRCode');  //获取活动二维码
    Route::match(['get','post'],'/shareEvent/ShareEventDataStatistics','ShareEventController@ShareEventDataStatistics');//何书哲 2018年8月8日 获取享立减数据统计详情
    Route::match(['get','post'],'/shareEvent/ShareEventMemberAnalysis','ShareEventController@ShareEventMemberAnalysis');//何书哲 2018年8月8日 获取享立减用户分析数据
    Route::get('/shareEvent/ShareEventDataAnalysis','ShareEventController@ShareEventDataAnalysis');//何书哲 2018年8月8日 获取享立减数据统计渲染
    Route::match(['get','post'],'/shareEvent/ShareEventDataExport','ShareEventController@ShareEventDataExport');//何书哲 2018年8月8日 获取享立减用户分析数据


    /**
     * 消息模板后台
     */
    Route::get('/message/index','MessageController@index'); //后台小程序消息模板首页
    Route::match(['get','post'],'/message/save','MessageController@save');  //消息模板添加、编辑
    Route::match(['get','post'],'/message/del','MessageController@delete'); //消息模板删除
    Route::match(['get','post'],'/message/send','MessageController@send'); //后台消息模板发送
    Route::match(['get','post'],'/message/sendWeixinTemp','MessageController@sendWeixinTemp'); //后台消息模板发送
    Route::get('/message/recordList','MessageController@recordList'); //历史发送记录
    Route::match(['get','post'],'/message/record/del','MessageController@delRecord'); //删除历史记录
    Route::get('/message/list','MessageController@list'); //后台公众号消息模板首页
    Route::get('/message/create','MessageController@create'); //后台公众号消息模板首页


    /**独立享立减2**/
    Route::get('/share/event/index', 'LiEventController@index');
    Route::match(['get','post'],'/share/event/save','LiEventController@save');
    Route::match(['get','post'],'/share/event/del','LiEventController@del');
    Route::match(['get','post'],'/share/event/refresh','LiEventController@refreshKey');
    Route::match(['get','post'],'/share/event/openReward','LiEventController@openReward');
    Route::match(['get','post'],'/share/event/rewardSet','LiEventController@rewardSet');

    /**集赞活动**/
    Route::get('/share/praise/index', 'SetPraiseController@index');
    Route::match(['get','post'],'/share/praise/save','SetPraiseController@save');
    Route::match(['get','post'],'/share/praise/del','SetPraiseController@del');
    Route::match(['get','post'],'/share/praise/refresh','SetPraiseController@refreshKey');
    Route::match(['get','post'],'/share/praise/openReward','SetPraiseController@openReward');
    Route::match(['get','post'],'/share/praise/rewardSet','SetPraiseController@rewardSet');


    /**推荐**/
    Route::match(['get','post'],'/commend/list','RecommendController@commendationList'); //推荐列表 add by jonzhang
    Route::match(['get','post'],'/commend/process','RecommendController@processCommendation'); //添加推荐活动 add by jonzhang
    Route::match(['get','post'],'/commend/delete','RecommendController@deleteCommendation'); //删除推荐明细 add by jonzhang
    Route::match(['get','post'],'/commend/show','RecommendController@showCommendationDetails'); //显示推荐明细 add by jonzhang
    Route::match(['get','post'],'/commend/update','RecommendController@updateCommendation'); //更改推荐 add by jonzhang
    Route::match(['get','post'],'/commend/showCommendation','RecommendController@showCommendation'); //显示推荐活动 add by jonzhang
    Route::match(['get','post'],'/commend/showDetailID','RecommendController@showCommendationDetailID'); //显示已推荐活动id add by jonzhang


    /*卡密活动 吴晓平 2018年08月06日*/
    Route::get('/cam/list','CamController@list'); //卡密活动列表
    Route::get('/cam/create','CamController@create'); //编辑、添加卡密活动
    Route::get('/cam/camStockList','CamController@camStockList'); //发卡密库存
    Route::match(['get','post'],'/cam/save','CamController@save'); //保存编辑，新建卡密活动
    Route::match(['get','post'],'/cam/invalid','CamController@invalid'); //发卡密活动失效
    Route::match(['get','post'],'/cam/delCam','CamController@delCam'); //发卡密活动删除
    Route::match(['get','post'],'/cam/export','CamController@export'); //批量导出文件
    Route::match(['get','post'],'/cam/list/delbatch','CamController@delbatch'); //批量导出文件
    Route::match(['get','post'],'/cam/upExcel','CamController@upExcel'); //上传发卡密库存数据
    Route::get('/cam/addStock','CamController@addStock'); //添加卡密库存
    Route::match(['get','post'],'/cam/doAddStock','CamController@doAddStock'); //处理添加导入卡密库存
    Route::get('/cam/downExcel','CamController@downExcelTemp'); //处理添加导入卡密库存

    // 许立 2018年08月31日 商家后台生成和下载二维码公共方法
    Route::get('/qrCode','IndexController@qrCode'); // 获取微商城二维码
    Route::get('/qrCodeDownload','IndexController@qrCodeDownload'); // 下载微商城二维码
    Route::get('/qrCodeXcx','IndexController@qrCodeXcx'); // 获取小程序二维码
    Route::get('qrCodeDownloadXcx','IndexController@qrCodeDownloadXcx'); // 下载小程序二维码


    //消息推送 梅杰 2018年10月11日
    Route::get('/marketing/messagesPush','MessagesPushController@index'); // 首页
    Route::match(['get','post'],'/marketing/messagesPush/custom','MessagesPushController@custom'); // 客服
    Route::match(['get','post'],'/marketing/messagesPush/','MessagesPushController@index'); // 首页
    Route::match(['get','post'],'/marketing/messagesPush/enroll','MessagesPushController@enroll'); // 在线报名
    Route::match(['get','post'],'/marketing/messagesPush/tradeUrge','MessagesPushController@tradeUrge'); // 订单催付
    Route::match(['get','post'],'/marketing/messagesPush/paySuccess','MessagesPushController@paySuccess'); // 付款成功
    Route::match(['get','post'],'/marketing/messagesPush/deliverySuccess','MessagesPushController@deliverySuccess'); // 发货成功
    Route::match(['get','post'],'/marketing/messagesPush/orderRefund','MessagesPushController@orderRefund'); // 退货退款成功
    Route::match(['get','post'],'/marketing/messagesPush/getMemberCard','MessagesPushController@getMemberCard'); // 发送会员卡
    Route::match(['get','post'],'/marketing/messagesPush/newOrder','MessagesPushController@newOrder'); // 新订单
    Route::match(['get','post'],'/marketing/messagesPush/group','MessagesPushController@group'); // 拼团
    Route::match(['get','post'],'/marketing/messagesPush/customReply','MessagesPushController@customReply'); // 留言回复

    //小票打印 何书哲 2018年11月14日
    Route::get('/delivery/index','DeliveryController@index'); //外卖设置首页
    Route::get('/delivery/printerList','DeliveryController@printerList'); //小票打印机列表
    Route::match(['get','post'],'/delivery/addPrinter','DeliveryController@addPrinter'); //添加/编辑小票打印机
    Route::match(['get','post'],'/delivery/setPrinter','DeliveryController@setPrinter'); //连接/断开打印机
    Route::match(['get','post'],'/delivery/delPrinter','DeliveryController@delPrinter'); //删除打印机
    Route::match(['get','post'],'/delivery/deliveryConfig','DeliveryController@deliveryConfig'); //外卖订单配置
    Route::match(['get','post'],'/delivery/changeConfigStatus','DeliveryController@changeConfigStatus'); //改变外卖订单配置按钮状态
    Route::match(['get','post'],'/delivery/queryPrinter','DeliveryController@queryPrinter'); //查询打印机
    Route::get('/delivery/queryOrder/{orderIndex}','DeliveryController@queryOrder'); //查询外卖订单是否打印成功
    Route::get('/delivery/queryPrinterStatus','DeliveryController@queryPrinterStatus'); //查询打印机状态


    Route::post('/store/set','StoreController@set'); // 店铺基本信息设置

});
/**
 * 小程序提示不走中间件
 */
Route::group( ['namespace'=>'Merchants', 'prefix'=>'merchants'], function()
{
    Route::get('/xcx/authorizePrompt','XCXController@authorizePrompt');//小程序授权提示
    Route::get('/xcx/authorizerAccessToken','XCXController@getAuthorizerAccessTokenTest');//小程序AuthorizerAccessToken
    Route::get('/xcx/selectAuthorizeTest','XCXController@selectAuthorizeTest');//小程序appid信息查询
    Route::get('/xcx/deleteAuthorizeTest','XCXController@deleteAuthorizeTest');//小程序appid信息删除
    Route::get('/pintuan/test','XCXController@getTestBySpell');//拼团测试
    Route::get('/xcx/accessPages','XCXController@accessPages');//小程序访问页面数据分析
    Route::get('/card/user','MemberController@getMemberCardByDeveloper');// add by jonzhang 获取某个用户在某个店铺下的会员卡信息 仅供开发者使用

    //又拍云异步回调
    Route::match(['get','post'],'/myfile/notify','MyFileController@notify');//又拍云回调
    Route::match(['get','post'],'/kuaidi/kuaidiNotify','KuaidiController@kuaidiNotify');//快递100回调 何书哲 2018年6月28日

    /**续费**/
    Route::match(['get','post'],'/capital/fee/serviceList','FeeController@serviceList'); //服务列表 add by 张国军
    Route::match(['get','post'],'/capital/fee/serviceDetail','FeeController@serviceDetail'); //服务详情 add by 张国军
    Route::match(['get','post'],'/capital/fee/invoiceList','FeeController@invoiceList'); //发票列表 add by 张国军
    Route::match(['get','post'],'/capital/fee/printInvoice','FeeController@printInvoice'); //打印发票 add by 张国军
    Route::match(['get','post'],'/fee/selfProduct/select/all','FeeController@selectSelfProducts'); //显示服务列表 add by 张国军
    Route::match(['get','post'],'/fee/selfProduct/select/one','FeeController@selectOneSelfProduct'); //显示某一个服务 add by 张国军
    Route::match(['get','post'],'/fee/invoice/select/all','FeeController@selectInvoices'); //发票列表 add by 张国军
    Route::match(['get','post'],'/fee/invoice/select/one','FeeController@selectOneInvoice'); //发票详情 add by 张国军
    Route::match(['get','post'],'/fee/invoice/insert','FeeController@insertInvoice'); //申请发票 add by 张国军
    Route::match(['get','post'],'/fee/order/submit','FeeController@submitSelfOrder'); //创建服务订单 add by 张国军
    Route::match(['get','post'],'/fee/order/select/all','FeeController@selectOrders'); //显示服务订单 add by 张国军
    Route::match(['get','post'],'/fee/order/delete','FeeController@deleteOrder'); //删除服务订单 add by 张国军
    Route::match(['get','post'],'/fee/order/aliPay','AliPayController@waitPay'); //支付宝支付 add by 张国军
    Route::match(['get','post'],'/fee/order/wechatPay','WechatPayController@waitPay'); //微信支付 add by 张国军
    Route::match(['get','post'],'/fee/order/remitPay','FeeController@waitPayForRemit'); //汇款支付 add by 张国军
    Route::match(['get','post'],'/fee/order/remit/config','FeeController@waiPayForRemitConfig'); //汇款支付账户信息 add by 张国军
    Route::match(['get','post'],'/fee/invoice/download','FeeController@download'); //发票下载 add by 张国军
    Route::match(['get','post'],'/fee/order/select/one','FeeController@selectOneOrder'); //订单详情 add by 张国军
    Route::match(['get','post'],'/capital/fee/order/pay/list','FeeController@payList'); //订单详情 add by 张国军
    Route::match(['get','post'],'/capital/fee/order/pay/finish','FeeController@payFinish'); //支付完成 add by 张国军
    Route::match(['get','post'],'/capital/fee/order/list','FeeController@orderList'); //订购列表路由 add by 张国军
    Route::match(['get','post'],'/capital/fee/aliPay/page','FeeController@aliPayPage'); //支付宝支付页面 add by 张国军

    //支付宝支付回调
    Route::match(['get','post'],'/fee/aliPay/webNotify','AliPayController@webNotify'); //支付宝异步支付结果 add by 张国军
    Route::match(['get','post'],'/fee/aliPay/webReturn','AliPayController@webReturn'); //支付宝同步支付结果 add by 张国军
    //微信支付回调
    Route::match(['get','post'],'/fee/wechatPay/webNotify','WechatPayController@webNotify'); //微信扫描异步回调结果 add by 张国军



});

/**
 * 管理店铺链接
 */
Route::group(['middleware' => ['userlogin'],'namespace'=>'Merchants','prefix'=>'merchants'],function(){
    /**
     * 管理店铺
     */
    Route::get('/team','TeamController@index');//管理我的店铺
    Route::match(['get','post'],'/team/create/{id?}','TeamController@create');//创建我的店铺
    Route::match(['get','post'],'/team/template/{id?}','TeamController@template');//选择店铺模版
    Route::post('/team/delete/{id}','TeamController@delete');//删除我的店铺
    Route::match(['get','post'], '/team/complete/{id}','TeamController@complete');//发布完成
});

Route::group(['middleware' => ['merchants', 'wechat'], 'namespace'=>'Merchants', 'prefix'=>'merchants'], function() {
    /**
     * 微信公众号配置路由
     */
    Route::get('/wechat','WechatController@index');//微信状况
    Route::get('/wechat/constantly','WechatController@constantly');//实时信息
    Route::get('/wechat/mass','WechatController@mass');//群发消息
    Route::get('/wechat/materialWechat','WechatController@materialWechat');//微信图文列表
    Route::get('/wechat/materialAdvanced','WechatController@materialAdvanced');//高级图文列表
    Route::match(['get', 'post'], '/wechat/materialWechatSingle/{id?}','WechatController@materialWechatSingle');//获取
    Route::get('/wechat/materialWechatSingleDel/{id}','WechatController@materialWechatSingleDel');//微信图文 - 单条图文删除
    Route::match(['get', 'post'], '/wechat/materialWechatMulti/{id?}','WechatController@materialWechatMulti');//微信图文 - 多条图文添加/编辑
    Route::get('/wechat/materialWechatMultiDel/{id}','WechatController@materialWechatMultiDel');//微信图文 - 多条图文删除
    Route::match(['get', 'post'], '/wechat/materialAdvancedSingle/{id?}','WechatController@materialAdvancedSingle');//高级图文 - 单条图文添加/编辑
    Route::post('/wechat/materialAdvancedSingleDel/{id}','WechatController@materialAdvancedSingleDel');//高级图文 - 单条图文删除
    Route::match(['get', 'post'], '/wechat/materialAdvancedMulti/{id?}','WechatController@materialAdvancedMulti');//高级图文 - 多条图文添加/编辑
    Route::get('/wechat/materialAdvancedMultiDel/{id}','WechatController@materialAdvancedMultiDel');//高级图文 - 多条图文删除
    Route::get('/wechat/timersend','WechatController@timerSend');//定时发送
    Route::get('/wechat/historymsg','WechatController@historyMsg');//历史消息
    //add MayJay
    Route::post('/wechat/replyType','WechatController@replyType');//自动回复类型
    //end
    Route::get('/wechat/replySet','WechatController@replySet');//自动回复
    Route::post('/wechat/replyRuleAdd/{ruleType}','WechatController@replyRuleAdd')->where('ruleType', '1|3');//添加/编辑规则
    Route::post('/wechat/replyRuleDel','WechatController@replyRuleDel');//删除规则
    Route::post('/wechat/replyKeywordAdd/{ruleType}','WechatController@replyKeywordAdd')->where('ruleType', '[1-3]');//添加/编辑关键词
    Route::post('/wechat/replyKeywordDel','WechatController@replyKeywordDel');//删除关键词
    Route::post('/wechat/replyContentAdd/{ruleType}','WechatController@replyContentAdd')->where('ruleType', '[1-3]');//添加/编辑回复内容
    Route::post('/wechat/replyContentDel','WechatController@replyContentDel');//删除回复内容
    Route::get('/wechat/subscribeReply','WechatController@subscribeReply');//关注时回复
    Route::get('/wechat/messages','WechatController@messages');//消息托管
    Route::get('/wechat/messagesTips','WechatController@messagesTips');//小尾巴
    Route::get('/wechat/weeklyReply','WechatController@weeklyReply');//每周回复
    Route::get('/wechat/phrase','WechatController@phrase');//快捷短语
    Route::get('/wechat/menu','WechatController@menu');//自定义菜单
    Route::get('/wechat/save_menu','WechatController@menuSave'); //自定义菜单保存数据操作
    Route::get('/wechat/create_menu','WechatController@createMenu'); //生成微信公众平台自定义菜单
    Route::get('/wechat/materialGetSingle','WechatController@materialGetSingle');
});
