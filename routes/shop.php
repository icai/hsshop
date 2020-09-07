<?php
/*
|--------------------------------------------------------------------------
| Shop Routes
|--------------------------------------------------------------------------
|
| 前台商城路由
|
*/
Route::group(['namespace' => 'Shop', 'prefix' => 'shop', 'middleware' => ['shop','shopAfter']], function() {
    /**
     * 首页路由
     */
	Route::get('/index/{wid}', 'IndexController@index')->middleware('stationing'); // 首页  add by jonzhang
    Route::match(['get','post'],'/indexStore/{wid}', 'IndexController@indexStore'); // 首页所需要的json数据  add by jonzhang
    //微页面路由
    Route::get('/microPage/index/{wid}/{id}', 'MicroPageController@index')->middleware('stationing'); // 微页面 add by jonzhang
    //微页面数据路由
    Route::get('/microPage/indexPage/{wid}/{id}', 'MicroPageController@indexPage'); // 微页面 add by jonzhang
    Route::get('/microPage/type/{wid}', 'MicroPageController@type'); // 微页面分类
    Route::get('/weixin/getWeixinSecretKey','IndexController@getWeixinSecretKey');//获取微信公众号密钥
    Route::get('/store/getMicroPageNotice','IndexController@getMicroPageNotice');//获取微页面公共广告信息
    Route::get('/store/getStoreNav','IndexController@getStoreNav');//获取店铺导航应用区域
    Route::get('/microPage/cardList','MicroPageController@showCardList');

    //关注相关  add mj
    Route::get('/getApiName', 'IndexController@getApiName');
    Route::get('/isSubscribe', 'IndexController@isSubscribe');
    Route::get('/kf/index','IndexController@newKf');
    Route::get('/cache/clearCache','IndexController@clearCache');

    /**
     * 图文
     */
    Route::get('/news/detail/{wid}/{id}', 'NewsController@detail'); // 图文详情页
    Route::get('/news/Mdetail/{wid}/{id}', 'NewsController@Mdetail');
    

    /**
     * 商品路由
     */
    Route::get('/product/index/{wid}', 'ProductController@index'); // 商品列表
    Route::get('/product/list/{wid}', 'ProductController@list'); // 商品列表分页数据
    Route::get('/product/detail/{wid}/{pid}', 'ProductController@detail')->middleware('stationing'); // 商品详情
    Route::get('/product/groups/{wid}', 'ProductController@groups'); // 商品分类
    Route::match(['get','post'],'/product/evaluate/{wid}', 'ProductController@evaluate'); // 获取商品评价信息
    Route::match(['get','post'],'/product/showProductEvaluate/{pid}', 'ProductController@showProductEvaluate');
    Route::get('/product/evaluatePraise/{wid}/{eid}', 'ProductController@evaluatePraise'); // 评论点赞
    Route::post('/product/evaluateReply/{wid}', 'ProductController@evaluateReply'); // 回复评论
    Route::match(['get','post'],'/product/evaluateDetail/{wid}', 'ProductController@evaluateDetail'); // 评论详情
    Route::get('/product/simpleDetail/{wid}/{pid}', 'ProductController@simpleDetail'); // 商品简易详情
    Route::get('/product/search/{wid}', 'ProductController@search'); // 商品搜索页面
    Route::post('/product/getSku','ProductController@getSku'); //获取商品规格列表
    Route::match(['post','get'],'/product/showTemplateDetail/{wid}/{id}','ProductController@showProductTemplateDetail'); //商品模板详情 add by jonzhang
    Route::post('/seckill/getSku','ProductController@getSeckillSku'); //获取秒杀商品规格列表
    Route::get('/group/detail/{wid}/{id}','ProductController@groupDetail'); //获取商品分组详情
    Route::get('/group/productGroupDetail','ProductController@productGroupDetail'); //获取商品分组下的所有商品
    Route::get('/product/getProductCard','ProductController@getProductCard'); //获取商品的分享卡片

    /**
     * 购物车路由
     */
    Route::get('/cart/index/{wid}', 'CartController@index'); // 购物车列表
    Route::match(['get','post'],'/cart/add/{wid}', 'CartController@add'); // 加入购物车
    Route::match(['get','post'],'/cart/discount/{wid}', 'CartController@discount'); // 计算满减活动优惠
    Route::post('/cart/edit/{wid}', 'CartController@edit'); // 编辑购物车
    Route::post('/cart/del/{wid}', 'CartController@del'); // 删除购物车
    Route::get('/cart/getNumber/{wid}', 'CartController@getNumber'); // 删除购物车
    Route::match(['get','post'],'/cart/saveRemark', 'CartController@saveRemark'); // 添加留言

    /**
     * 订单路由
     */
    Route::match(['get','post'],'/order/waitPayOrder', 'OrderController@waitPayOrder');//待付款订单  add by jonzhang
    Route::match(['get','post'],'/order/submit/{wid?}', 'OrderController@submitOrder'); // 提交订单  add by jonzhang
    Route::get('/order/pay/{wid}/{payment}/{oid}', 'OrderController@pay'); // 支付订单
    Route::match(['get','post'],'/order/index/{wid}', 'OrderController@index')->middleware('stationing'); // 普通订单列表
    Route::get('/order/commentList/{wid}/{oid}', 'OrderController@commentList'); // 订单商品评论列表
    Route::get('/order/detail/{oid}/{wid?}', 'OrderController@detail'); // 普通订单详情
    Route::get('/order/b2tList/{wid}', 'OrderController@b2tList'); // 多人拼团订单列表
    Route::get('/order/b2tdetail/{wid}', 'OrderController@b2tdetail'); // 多人拼团订单详情
    Route::post('/order/del/{wid}/{oid}', 'OrderController@del'); // 删除订单
    Route::post('/order/received/{wid}/{oid}', 'OrderController@received'); // 确认收货
    Route::match(['get','post'],'/order/comment/{wid}', 'OrderController@comment'); // 订单评论
    Route::match(['get','post'],'/order/cancle/{wid}/{oid}', 'OrderController@cancelOrder'); // 取消订单
    Route::match(['get','post'],'/order/upfile/{wid}', 'OrderController@upfile'); // 上传文件

    Route::get('/order/zitiVoucher','OrderController@zitiVoucher'); //自提订单凭证
    Route::get('/order/hexiaoConfirm','OrderController@hexiaoConfirm'); //自提订单凭证
    Route::match(['get','post'],'/order/scanLongConnet','OrderController@scanLongConnet'); //长连接接口
    Route::get('/order/hexiaoRedirect','OrderController@hexiaoRedirect'); //用户展示核销码核销成功跳转页面
    Route::match(['get','post'],'/order/hexiaoSure','OrderController@hexiaoSure'); //用户展示核销码核销成功跳转页面


    /**
     * 获取自提列表（可由近到远排序）
     */
    Route::match(['get','post'],'/zitiList','OrderController@getZitiList'); //微商城获取自提列表
    Route::match(['get','post'],'/zitiDate','OrderController@getZitiDates'); //微商城获取自提列表
    Route::get('/order/detailMap','OrderController@detailMap');  //自提地图导航页
    Route::get('/order/location','OrderController@location');  
    //=============退款 start
    Route::get('/order/refund/{wid}','OrderController@refund')->middleware('stationing');//退款订单列表页面
    Route::get('/order/refundList/{wid}/{status?}','OrderController@refundList');//退款订单列表
    Route::match(['get','post'],'/order/refundDetailView/{wid}/{oid}/{pid}/{propID?}', 'OrderController@refundDetailView'); // 退款详情页面
    Route::match(['get','post'],'/order/refundDetail/{wid}/{oid}/{pid}/{propID?}', 'OrderController@refundDetail'); // 退款详情
    Route::match(['get','post'],'/order/refundApplyType/{wid}/{oid}/{pid}/{isEdit}/{propID?}', 'OrderController@refundApplyType'); // 申请退款 类型页面
    Route::match(['get','post'],'/order/refundApplyView/{wid}/{oid}/{pid}/{isEdit}/{propID?}', 'OrderController@refundApplyView'); // 申请退款页面
    Route::match(['get','post'],'/order/refundApply/{wid}/{oid}/{pid}/{propID?}', 'OrderController@refundApply'); // 申请退款
    Route::match(['post','get'],'/order/refundApplyEdit/{wid}/{oid}/{pid}/{propID?}', 'OrderController@refundApplyEdit'); // 申请退款修改
    Route::match(['get','post'],'/order/refundReturnView/{wid}/{refundID}', 'OrderController@refundReturnView'); // 买家退款发货页面
    Route::match(['get','post'],'/order/refundReturn/{wid}/{refundID}', 'OrderController@refundReturn'); // 买家退款发货
    Route::match(['get','post'],'/order/refundMessages/{wid}/{oid}/{pid}/{propID?}', 'OrderController@refundMessages'); // 退款协商列表
    Route::match(['get','post'],'/order/refundMessagesView/{wid}/{oid}/{pid}/{propID?}', 'OrderController@refundMessagesView'); // 退款协商列表
    Route::match(['get','post'],'/order/refundCancel/{wid}/{oid}/{refundID}', 'OrderController@refundCancel'); // 取消退款
    Route::match(['get','post'],'/order/refundAddMessage/{wid}/{refundID}/{oid}', 'OrderController@refundAddMessage'); // 退款添加留言
    Route::match(['get','post'],'/order/refundAddMessageView/{wid}/{refundID}/{oid}', 'OrderController@refundAddMessageView'); // 退款添加留言
    Route::match(['get','post'],'/order/refundVerifyView/{wid}/{refundID}', 'OrderController@refundVerifyView'); // 欠款去向页面
    Route::match(['get','post'],'/order/refundVerify/{wid}/{refundID}', 'OrderController@refundVerify'); // 退款微信审核 钱款去向
    //============退款 end


    Route::post('/order/receiveDelay/{wid}/{oid}', 'OrderController@receiveDelay'); // 延长收货
    Route::get('/order/share/{wid}/{oid}', 'OrderController@share'); // 订单分享
    Route::get('/order/getLogistics/{wid}/{oid}', 'OrderController@getLogistics'); // 获取物流信息
    Route::get('/order/expresslist/{wid}/{oid}', 'OrderController@expresslist'); // 物流信息
    Route::match(['get','post'],'/order/getFreight', 'OrderController@getFreight'); // 根据购物车获取运费
    Route::match(['get','post'],'/order/groupsOrderDetail/{oid}/{wid?}', 'OrderController@groupsOrderDetail'); // 团购订单详情接口

    Route::get('/order/payTest','OrderController@payTest');  //支付测试
    Route::get('/order/assistant','OrderController@assistant');//订单助手


    /**
     * 支付
     */
    Route::get('/pay/index', 'PaymentController@index'); // 订单支付
    Route::get('/pay/paySuccess/{id}', 'PaymentController@paySuccess'); // 订单成功页面
    Route::get('/pay/payFail/{id}', 'PaymentController@payFail'); // 支付失败页面
    Route::get('/pay/rechargeSuccess', 'PaymentController@rechargeSuccess'); // 充值成功页面
    Route::get('/pay/rechargeFail', 'PaymentController@rechargeFail'); // 充值失败页面

    /**
     * 支付成功回调页面
     */
    Route::get('/payment/wechatPayNotify/{wid}','PaymentController@wechatPayNotify'); //微信支付异步回调页面

    /**
     * 会员路由
     */
    Route::match(['get','post'],'/member/index/{wid?}','MemberController@index')->middleware('stationing'); // 会员主页  add by jonzhang
    Route::match(['get','post'],'/member/indexHome/{wid?}','MemberController@indexHome'); // 会员主页所需要的json数据  add by jonzhang
    Route::get('/member/mycards/{wid}', 'MemberController@myCards')->middleware('stationing'); // 我的会员卡
    Route::get('/member/balanceDetail', 'MemberController@balanceDetail'); // 余额明细
    Route::get('/member/balanceDetailAjax', 'MemberController@balanceDetailAjax'); // 余额明细
    Route::get('/member/cardRecharge', 'MemberController@cardRecharge'); // 会员卡充值
    Route::get('/member/rechargeRecord', 'MemberController@rechargeRecord'); // 充值记录
    Route::get('/member/detail/{wid}/{card_id}','MemberController@cardDetail')->middleware('stationing'); //会员卡详情
    Route::match(['get','post'],'/member/cardQrcodeCreated/{card_id}','MemberController@cardQrcodeCreated'); //会员卡二维码同步微信入口
    Route::post('/member/delete/{wid}/{card_id}','MemberController@cardDelete'); //删除会员卡
    Route::get('/member/getCardAction/{wid}/{encrypt_cardId}','MemberController@getCardAction'); //领取会员卡
    Route::get('/member/recharge/{wid}/{encrypt_cardId}/{encrypt_money}','MemberController@recharge'); //会员卡充值
    Route::get('/member/set/{wid}','MemberController@memberSet') ; //会员信息页面
    Route::get('/member/save','MemberController@save'); //会员卡设置
    Route::post('/member/setDefault/{wid}/{card_id}','MemberController@setDefault'); //设置默认会员卡
    Route::get('/member/cardActive/{wid}','MemberController@cardActive'); //激活会员卡
    Route::get('/member/complete','MemberController@complete'); //领取完成
    Route::get('/member/isOpenWeath','MemberController@isOpenWeath'); //开启关闭财富眼
    Route::get('/member/distribution','MemberController@distributionExplan'); //分销说明页
    Route::get('/member/distributionRedirect','MemberController@distributionRedirect'); //分销二维码识别跳转页面
    Route::get('/member/showAddress','MemberController@showAddress'); //地址管理
    Route::get('/member/addAddress','MemberController@addAddress'); //添加地址页面
    Route::get('/member/address','MemberController@address'); // 三级联动地址接口
    Route::get('/member/newMemberCard','MemberController@newMemberCard'); // 是否有新会员卡标识
    Route::post('/member/newMemberCardCallBack','MemberController@newMemberCardCallBack'); // 新会员卡标识回调


    Route::match(['get', 'post'], '/member/save', 'MemberController@save'); // 会员资料添加/编辑
    Route::post('/member/signIn', 'MemberController@signIn'); // 签到
    Route::get('/member/addressList', 'MemberController@addressList'); // 收货地址列表
    Route::match(['get', 'post'], '/member/addressAdd', 'MemberController@addressAdd'); // 收货地址添加/编辑
    Route::post('/member/addressDel', 'MemberController@addressDel'); // 收货地址删除
    Route::post('/member/addressDefault', 'MemberController@addressDefault'); // 设为默认收货地址
    Route::get('/member/coupons/{wid}/{status}', 'MemberController@coupons')->middleware('stationing'); // 优惠券某用户领取列表
    Route::get('/member/couponList/{wid}/{status}', 'MemberController@couponList'); // 我的优惠券列表
    Route::get('/member/couponDetail/{wid}/{id}', 'MemberController@couponDetail'); // 已领取优惠券详情
    Route::get('/member/couponProducts/{wid}/{id}', 'MemberController@couponProducts'); // 优惠券指定商品列表
    Route::get('/member/getDefaultAddress', 'MemberController@getDefaultAddress'); // 获取默认收货地址

    Route::get('/member/addBalance/{wid}/{money}', 'MemberController@addBalance');
    Route::match(['get','post'],'/member/wechat/addressAdd','MemberController@addressAddFormWechat');
    /*无权限访问页面*/
    Route::match(['get','post'],'/member/noPermission','MemberController@noPermission');
    Route::get('/member/researchList/{wid}', 'MemberController@researchList'); // 许立 2018年08月16日 我的留言记录
    Route::get('/member/researchDetail/{wid}/{id}/{times}', 'MemberController@researchDetail'); // 许立 2018年08月16日 我的留言记录-记录详情
    Route::get('/member/favoriteList/{wid}', 'MemberController@favoriteList'); // 许立 2018年09月04日 我的收藏页面
    Route::get('/member/favoriteListApi/{wid}', 'MemberController@favoriteListApi'); // 许立 2018年09月06日 我的收藏接口
    Route::get('/member/isFavorite', 'MemberController@isFavorite'); // 许立 2018年09月06日 商品或活动是否收藏
    Route::post('/member/favorite', 'MemberController@favorite'); // 许立 2018年09月05日 收藏
    Route::post('/member/cancelFavorite', 'MemberController@cancelFavorite'); // 许立 2018年09月05日 取消收藏
    Route::get('/member/bindHexiaoUser', 'MemberController@bindHexiaoUser'); // 吴晓平 2018年10月09日 绑定店铺核销员


    /**
     * 活动路由
     */
    Route::get('/activity/index/{wid}', 'ActivityController@index'); // 活动列表
    Route::get('/activity/eggDetail/{wid}', 'ActivityController@eggDetail'); // 砸金蛋展示页
    Route::post('/activity/eggPlay/{wid}', 'ActivityController@eggPlay'); // 砸金蛋操作
    Route::get('/activity/lotteryDetail/{wid}', 'ActivityController@lotteryDetail'); // 幸运大抽奖展示页
    Route::post('/activity/lotteryPlay/{wid}', 'ActivityController@lotteryPlay'); // 幸运大抽奖操作
    /*大转盘*/
    Route::get('/activity/wheel/{wid}/{id}', 'ActivityController@wheel')->middleware('stationing'); // 大转盘展示页
    Route::match(['get','post'],'/activity/wheelPlay/{wid}/{id}', 'ActivityController@wheelPlay'); // 大转盘操作
    Route::match(['get','post'],'/activity/myGift/{wid}', 'ActivityController@myGift')->middleware('stationing'); // 我的奖品
    Route::match(['get','post'],'/activity/delGift/{wid}/{id}', 'ActivityController@delGift'); // 删除我的赠品
    Route::match(['get','post'],'/activity/method/{id}/{type?}', 'ActivityController@method'); // 许立 2018年08月20日 我的奖品-兑奖方式

    /*刮刮卡*/
    Route::get('/activity/scratch/{wid}/{id}', 'ActivityController@scratch')->middleware('stationing'); // 何书哲 2018年08月24日 刮刮卡展示页
    Route::match(['get','post'],'/activity/scratchPlay/{wid}/{id}', 'ActivityController@scratchPlay'); // 何书哲 2018年08月24日 刮刮卡操作
    Route::match(['get','post'],'/activity/myScratchGift/{wid}', 'ActivityController@myScratchGift')->middleware('stationing'); //何书哲 2018年08月24日 我的奖品
    Route::match(['get','post'],'/activity/delScratchGift/{wid}/{id}', 'ActivityController@delScratchGift'); // 何书哲 2018年08月24日 删除我的赠品
       
    Route::get('/activity/cardDetail/{wid}', 'ActivityController@cardDetail'); // 会员卡展示页
    Route::post('/activity/cardPlay/{wid}', 'ActivityController@cardPlay'); // 会员卡操作
    Route::match(['get', 'post'], '/activity/couponDetail/{wid}/{id}', 'ActivityController@couponDetail')->middleware('stationing'); // 优惠券详情
    Route::match(['get', 'post'], '/activity/couponReceive/{wid}/{id}', 'ActivityController@couponReceive'); // 领取优惠券页面
    Route::post('/activity/coupon/receive/{wid}/{id}', 'ActivityController@couponReceiveApi'); // 领取优惠券接口
    Route::match(['get', 'post'], '/activety/couponAuth/{card_id}','ActivityController@couponAuth'); //同步到微信卡券
    Route::match(['get', 'post'], '/activity/couponReceiveList/{wid}/{id}', 'ActivityController@couponReceiveList'); // 某优惠券领取列表
    Route::get('/activity/bargainList/{wid}', 'ActivityController@bargainList'); // 砍价商品列表
    Route::match(['get', 'post'], '/activity/bargainDo/{wid}', 'ActivityController@bargainDo'); // 砍价操作
    Route::get('/activity/seckillList/{wid}', 'ActivityController@seckillList'); // 秒杀商品列表
    Route::get('/activity/b2tList/{wid}', 'ActivityController@b2tList'); // 多人拼团商品列表
    Route::get('/activity/achieveGiveList/{wid}', 'ActivityController@achieveGiveList'); // 满减/送商品列表
    Route::get('/seckill/detail/{wid}/{id}', 'ActivityController@seckillDetail')->middleware('stationing'); //秒杀详情
    Route::get('/activity/eggDetail/{wid}/{id}', 'ActivityController@eggDetail'); //砸金蛋活动首页数据接口
    Route::get('/activity/egg/{wid}/{id}', 'ActivityController@eggRun'); //砸金蛋抽獎
    Route::get('/activity/egg/index/{wid}/{id}', 'ActivityController@eggIndex')->middleware('stationing');//砸金蛋首页
    Route::get('/activity/egg/getPrizeList/{wid}/{id}','ActivityController@getPrizeList');
    Route::get('/activity/eggPrizeList/{wid}','ActivityController@eggPrizeList');// 许立 2018年08月20日 砸金蛋我的奖品
    Route::get('/activity/eggPrize/del','ActivityController@eggPrizeDel');// 梅杰 2018年08月20日 砸金蛋我的奖品删除
    Route::match(['get','post'],'/activity/registerSalesMan/{wid}','ActivityController@registerSalesMan');
    Route::match(['get','post'],'/activity/getGroupsInfo','ActivityController@getGroupsInfo');

    Route::post('/activity/setAwardAddress/{wid}', 'ActivityController@setAwardAddress'); // 许立 2018年08月16日 营销活动设置奖品专有的收货地址

    Route::get('/point/mypoint', 'PointController@point')->middleware('stationing'); // add by jonzhang 我的积分
    Route::get('/point/sign/{wid}', 'PointController@sign')->middleware('stationing'); // add by jonzhang 签到
    Route::get('/point/addSign', 'PointController@addSign'); // add by jonzhang 添加记录
    Route::get('/point/signActivityRule', 'PointController@signActivityRule'); // add by jonzhang 签到活动规则
    Route::get('/point/addSignRecord/{wid}', 'PointController@addSignRecord'); // add by jonzhang 签到活动
    Route::get('/point/addShareRecord/{wid}', 'PointController@addShareRecord'); // add by jonzhang  分享送积分
    Route::get('/point/selectPointRecord', 'PointController@selectPointRecord'); // 积分变更 add by jonzhang
    Route::get('/point/selectSignTemplateData/{wid}', 'PointController@selectSignTemplateData'); // 签到规则 add by jonzhang
    Route::get('/point/selectSignRule', 'PointController@selectSignRule'); // 签到活动规则 add by jonzhang
    Route::get('/point/showPoint', 'PointController@showPoint'); // 通过金额得到对应的可用积分信息 add by Herry 20171117
    Route::get('/point/isShowPrompt','PointController@isGivePoint'); //   是否显示提示 add by jonzhang

    /*分销相关路由*/
    Route::get('/distribute/wealth', 'DistributeController@wealth'); // 我的财富
    Route::match(['get','post'],'/distribute/withdrawal', 'DistributeController@withdrawal'); // 提取金额
    Route::get('/distribute/selectAccount', 'DistributeController@selectAccount'); // 选择账户
    Route::get('/distribute/manageAccount', 'DistributeController@manageAccount'); // 管理账户
    Route::match(['get','post'],'/distribute/addAccount', 'DistributeController@addAccount'); // 添加账户
    Route::get('/distribute/addAlipay', 'DistributeController@addAlipay'); // 添加支付宝
    Route::get('/distribute/earnings', 'DistributeController@earnings'); // 收益明细
    Route::get('/distribute/delAccount', 'DistributeController@delAccount'); // 删除账户
    Route::get('/distribute/cancelDistribute', 'DistributeController@cancelDistribute'); // 取消成为分销客或不在提示
    Route::get('/distribute/beDistribute', 'DistributeController@beDistribute'); // 成为分销客
    Route::match(['get','post'],'/distribute/beDistributor', 'DistributeController@beDistributor'); // 分销客页面
    Route::match(['get','post'],'/distribute/distributeAgreement', 'DistributeController@distributeAgreement'); // 分销客页面
    Route::match(['get','post'],'/distribute/cashLog', 'DistributeController@cashLog'); // 提现记录
    Route::match(['get','post'],'/distribute/productList', 'DistributeController@productList'); // 分销商品
    Route::match(['get','post'],'/distribute/apply/{wid}/{id}', 'DistributeController@apply'); // 申请成为分销客页面
    Route::get('/distribute/apply/{wid}/{id}', 'DistributeController@apply'); // 申请成为分销客页面
    Route::post('/distribute/apply', 'DistributeController@apply'); // 申请成为分销客
    Route::post('/distribute/myTeam', 'DistributeController@myTeam'); // 我的团队
    Route::get('/distribute/incomeLog', 'DistributeController@incomeLog'); // 收益记录
    Route::get('/distribute/cashLog', 'DistributeController@cashLog'); // 提现记录
    Route::get('/distribute/distributeOrder', 'DistributeController@distributeOrder'); // 团队订单


	/**
	 * 微论坛
	 */
	Route::group(['middleware' => ['microforum.client']], function() {
		Route::get('/microforum/forum/index/{wid}', 'MicroForumController@forumIndex');//论坛主页
		Route::post('/microforum/post/list', 'MicroForumController@postsList');//帖子列表
		Route::post('/microforum/post/favorsed', 'MicroForumController@postsFavorsed');//帖子点赞
		Route::post('/microforum/post/unfavorsed', 'MicroForumController@postsUnfavorsed');//帖子取消点赞
		Route::post('/microforum/post/deleted', 'MicroForumController@postsDeleted');//帖子删除
		Route::get('/microforum/user/index/{id_type}/{user_id}', 'MicroForumController@userIndex');//用户主页
		Route::get('/microforum/post/detail/{pid}', 'MicroForumController@postsDetail');//帖子详情
		Route::get('/microforum/post/owner', 'MicroForumController@postsOwner');//我的
		Route::get('/microforum/post/release', 'MicroForumController@postsRelease');//帖子发布
		Route::post('/microforum/post/released', 'MicroForumController@postsReleased');//帖子发布-保存
		Route::get('/microforum/post/replies', 'MicroForumController@postsReplies');//帖子回复
		Route::post('/microforum/post/repliesed', 'MicroForumController@postsRepliesed');//帖子回复-保存
		Route::post('/microforum/replies/deleted', 'MicroForumController@repliesDeleted');//回复删除
		Route::post('/microforum/notification/list', 'MicroForumController@notificationList');//消息列表
	});

    /*门店相关路由*/
    Route::get('/store/getStore/{wid?}', 'StoreController@getStore'); // 门店列表
    Route::get('/store/storeMap/{id}/{source?}', 'StoreController@storeMap'); // 门店地图
    Route::get('/store/getStoreList', 'StoreController@getStoreList'); // 获取门店列表
    Route::get('/store/shopZiti', 'StoreController@getShopZiti'); // 获取自提列表

    /*团购相关路由*/
    Route::get('/grouppurchase/index/{wid?}', 'GroupPurchaseController@index'); // 团购列表
    Route::get('/grouppurchase/detail/{id}/{wid?}', 'GroupPurchaseController@detail2')->middleware('stationing'); // 团购详情
    Route::get('/grouppurchase/guide/{wid?}', 'GroupPurchaseController@guide'); // 玩法详情
    Route::get('/grouppurchase/newOrder/{wid?}', 'GroupPurchaseController@newOrder'); // 待付款的订单
    Route::get('/grouppurchase/groupon/{groups_id}/{wid?}', 'GroupPurchaseController@groupon2'); // 拼团详情
    Route::get('/grouppurchase/orderDetail/{wid?}', 'GroupPurchaseController@orderDetail'); // 拼团订单详情
    Route::get('/grouppurchase/notSupport/{wid?}', 'GroupPurchaseController@notSupport'); // 等待买家发货的订单
    Route::get('/grouppurchase/helplist/{wid?}', 'GroupPurchaseController@helplist'); // 订单助手
    Route::match(['get','post'],'/grouppurchase/getSkus/{wid?}', 'GroupPurchaseController@getSkus'); // 获取商品规格
    Route::match(['get','post'],'/grouppurchase/getGroups/{id}/{wid?}', 'GroupPurchaseController@getGroups'); // 获取商品规格
    Route::match(['get','post'],'/grouppurchase/ceoInfo', 'GroupPurchaseController@ceoInfo'); // 统计用户信息
    /*新团购相关路由*/
    Route::match(['get','post'],'/web/groups/detail/{id}','GroupsController@detail'); //团购详情
    Route::match(['get','post'],'/web/groups/getGroups/{id}','GroupsController@getGroups'); //获取凑团信息
    Route::match(['get','post'],'/web/groups/getProductEvaluate/{id}','GroupsController@getProductEvaluate'); //获取商品评价
    Route::match(['get','post'],'/web/groups/getEvaluateClassify/{id}','GroupsController@getEvaluateClassify'); //获取商品分类
    Route::match(['get','post'],'/web/groups/getDetailEvaluate/{id}','GroupsController@getDetailEvaluate'); //团购详情页获取评价详情
    Route::match(['get','post'],'/web/groups/recommendGroups','GroupsController@recommendGroups'); //获取推荐团购
    Route::match(['get','post'],'/web/groups/getSkus/{id}','GroupsController@getSkus'); //获取商品skus
    Route::match(['get','post'],'/web/groups/getSettlementInfo','GroupsController@getSettlementInfo'); //获取结算信息
    Route::match(['get','post'],'/web/groups/createOrder','GroupsController@createOrder'); //创建订单
    Route::match(['get','post'],'/web/groups/groupsDetail/{id}','GroupsController@groupsDetail'); //参团信息
    Route::match(['get','post'],'/web/groups/groupsList','GroupsController@groupsList'); //团购列表
    Route::match(['get','post'],'/web/groups/myGroups','GroupsController@myGroups'); //我的团购列表
    Route::match(['get','post'],'/web/groups/addGroupList','GroupsController@addGroupList'); //一键参团列表
    Route::match(['get','post'],'/web/groups/getGroupsMessage','GroupsController@getGroupsMessage'); //获取团购消息
    Route::match(['get','post'],'/web/groups/getShareData/{gid}','GroupsController@getShareData'); //获取分享信息
    Route::match(['get','post'],'/web/groups/groupsById/{gid}','GroupsController@groupsById'); //获取团购信息
    Route::match(['get','post'],'/web/groups/showMyGroups','GroupsController@showMyGroups'); //我的团列表
    Route::get('/web/groups/getFreight','GroupsController@getFreight'); //拼团获取运费接口
    Route::match(['get','post'],'/web/groups/upShowStatus','GroupsController@upShowStatus'); //我的团购列表修改查看状态

    /*团购会场专用路由*/
    Route::match(['get','post'],'/meeting/groups/detail/{id}','GroupsMeetingController@detail'); //团购详情
    Route::match(['get','post'],'/meeting/groups/getGroups/{id}','GroupsMeetingController@getGroups'); //获取凑团信息
    Route::match(['get','post'],'/meeting/groups/getProductEvaluate/{id}','GroupsMeetingController@getProductEvaluate'); //获取商品评价
    Route::match(['get','post'],'/meeting/groups/getEvaluateClassify/{id}','GroupsMeetingController@getEvaluateClassify'); //获取商品分类
    Route::match(['get','post'],'/meeting/groups/getDetailEvaluate/{id}','GroupsMeetingController@getDetailEvaluate'); //团购详情页获取评价详情
    Route::match(['get','post'],'/meeting/groups/recommendGroups','GroupsMeetingController@recommendGroups'); //获取推荐团购
    Route::match(['get','post'],'/meeting/groups/getSkus/{id}','GroupsMeetingController@getSkus'); //获取商品skus
    Route::match(['get','post'],'/meeting/groups/getSettlementInfo','GroupsMeetingController@getSettlementInfo'); //获取结算信息
    Route::match(['get','post'],'/meeting/groups/createOrder','GroupsMeetingController@createOrder'); //创建订单
    Route::match(['get','post'],'/meeting/groups/groupsDetail/{id}','GroupsMeetingController@groupsDetail'); //参团信息
    Route::match(['get','post'],'/meeting/groups/groupsList','GroupsMeetingController@groupsList'); //团购列表
    Route::match(['get','post'],'/meeting/groups/saveRemark','GroupsMeetingController@saveRemark'); //添加留言
    Route::match(['get','post'],'/meeting/groups/myGroups','GroupsMeetingController@myGroups'); //我的团购列表
    Route::match(['get','post'],'/meeting/groups/addGroupList','GroupsMeetingController@addGroupList'); //一键参团列表
    Route::match(['get','post'],'/meeting/groups/getGroupsMessage','GroupsMeetingController@getGroupsMessage'); //获取团购消息
    Route::match(['get','post'],'/meeting/groups/getShareData/{gid}','GroupsMeetingController@getShareData'); //获取分享信息
    Route::match(['get','post'],'/meeting/groups/groupsById/{gid}','GroupsMeetingController@groupsById'); //获取团购信息
    Route::match(['get','post'],'/meeting/groups/showMyGroups/{wid?}','GroupsMeetingController@showMyGroups'); //我的团列表
    Route::match(['get','post'],'/meeting/groups/myOrder','GroupsMeetingController@myOrder'); //我的订单
    Route::match(['get','post'],'/meeting/groups/index','GroupsMeetingController@index'); //独立团购支付
    Route::get('/meeting/groups/getFreight','GroupsMeetingController@getFreight'); //拼团获取运费接口
    Route::match(['get','post'],'/meeting/groups/upShowStatus','GroupsMeetingController@upShowStatus'); //我的团购列表修改查看状态
    Route::get('/meeting/detail/{id}/{wid?}', 'GroupsMeetingController@detail2')->middleware('stationing'); // 团购详情
    Route::get('/meeting/groupon/{groups_id}/{wid?}', 'GroupsMeetingController@groupon2'); // 拼团详情
    Route::get('/meeting/getGroupsNum/{ruleid}', 'GroupsMeetingController@getGroupsNum'); // 获取参团人数和数量



    /*绑定手机号码*/
    Route::match(['get','post'],'/bindmobile/index/{wid?}', 'BindMobileController@index'); // 绑定手机号码页面
    Route::match(['get','post'],'/bindmobile/sendCode', 'BindMobileController@sendCode'); // 发送验证码
    Route::match(['get','post'],'/bindmobile/changeMobile', 'BindMobileController@changeMobile'); // 更改号码
    Route::match(['get','post'],'/bindmobile/isBind', 'BindMobileController@isBind'); // 是否需要绑定手机号码



    /*移动端投票页面*/
    Route::match(['get','post'],'/vote/index/{wid}/{id}','VoteController@index');  //移动端投票首页

    Route::match(['get','post'],'/vote/enroll','VoteController@enroll'); //报名参加投票
    Route::get('/vote/getSearchList','VoteController@getEnrollListBySearch'); //获取搜索数据
    Route::get('/vote/prizes/{id}','VoteController@prizes'); //奖项设置
    Route::get('/vote/canvass/{id}','VoteController@canvass'); //奖项设置
    Route::get('/vote/getEnrollData/{id}','VoteController@getEnrollData'); //获取投票页数据


    /*微预约*/
    Route::get('/book/index/{wid}','BookController@index'); //商家的预约列表
    Route::match(['get','post'],'/book/detail/{wid}/{id}','BookController@detail'); //预约详情
    Route::get('/book/user/list/{wid}/{id}','BookController@userBookList'); //用户的预约列表
    Route::match(['get','post'],'/book/user/save/{id}','BookController@bookSave'); //我的预约编辑
    Route::match(['get','post'],'/book/user/del','BookController@userBookDel'); //用户删除预约
    Route::post('/book/getBookListApi','BookController@getBookListApi');  //查询数据接口
    
    
    /*微信图文*/
 	Route::get('/material/detail/{wid}/{type}/{id}','NewsController@materialDetail'); //移动端微信图文详情

    /* 免费领小程序 */
    Route::match(['get','post'],'/freeXCX/apply/{wid}','FreeXCXController@apply'); // 申请报名 Herry
    Route::get('/freeXCX/applySuccess/{wid}','FreeXCXController@applySuccess'); // 报名成功 Herry
    /*会长活*/
    //Route::match(['get','post'],'/meeting/register/{wid}','MeetingController@register'); // 注册用户
    Route::match(['get','post'],'/meeting/register/{wid}','MeetingController@defaulRegister'); // 注册用户
    Route::match(['get','post'],'/meeting/defaulRegister/{wid}','MeetingController@defaulRegister'); // 注册用户

    Route::match(['get','post'],'/meeting/extensionRegister/{wid}','MeetingController@extensionRegister'); // 领取课程
    Route::match(['get','post'],'/meeting/registerSuccess/{wid}','MeetingController@registerSuccess'); // 领取成功
	Route::match(['get','post'],'/meeting/seeDetail/{wid}','MeetingController@seeDetail'); // 查看邀请数据
    //海报页面
    Route::match(['get','post'],'/meeting/posterPage/{wid}', 'MeetingController@posterPage'); //海报页面
    Route::match(['get','post'],'/meeting/getVideoSign','MeetingController@getVideoSign'); 
    Route::get('/meeting/getQrcode','MeetingController@getQrcode'); 
    Route::get('/meeting/upWeixinHeadImg','MeetingController@upWeixinHeadImg'); 
    Route::get('/meeting/defaultUpload','MeetingController@defaultUpload');
    /*需求*/
    Route::match(['get','post'],'/activity/{wid}','ActivityController@activity');

    /*享立减路由*/
    Route::match(['get','post'],'/shareevent/getAllActor','ShareEventController@getAllActorData');
    //享立减商品详情
    Route::get('/shareevent/product/showproductdetail','ShareEventController@showProductDetail')->middleware('stationing'); //add by jonzhang
    //待提交订单
    Route::get('/shareevent/order/waitsubmit','ShareEventController@processWaitSubmitShareEventOrder'); //add by jonzhang
    //提交订单
    Route::match(['get','post'],'/shareevent/order/submit','ShareEventController@submitShareEventOrder'); //add by jonzhang
    //计算运费
    Route::get('/shareevent/order/feight','ShareEventController@statFreight'); //add by jonzhang
    //活动进度
    Route::post('/shareevent/getProcess','ShareEventController@getProcess'); //add MayJay
    //享立减活动参与者
    Route::get('/shareevent/showRecord','ShareEventController@showShareEventRecord'); //add jonzhang

    Route::get('/shareevent/order/waitsubmitpage','ShareEventController@waitSubmitShareOrder'); //add wuxiaoping
    Route::get('/shareevent/showEventDetail','ShareEventController@showEventDetail'); //add wuxiaoping
    Route::match(['get','post'],'/shareevent/shareRecord','ShareEventController@shareRecord'); //何书哲 2018年8月6日 添加微商城分享记录

    Route::match(['get','post'],'/activity/freeApply/{wid}/{id}/{type}', 'MicroPageController@freeApply')->middleware('stationing'); // 许立 2018年7月9日 7月9日活动提交页
    Route::get('/activity/freeApplyResult/{wid}/{id}/{type}', 'MicroPageController@freeApplyResult'); // 许立 2018年7月9日 7月9日活动结果页面
    Route::get('/activity/freeApplyUserList/{wid}/{type}', 'MicroPageController@freeApplyUserList'); // 许立 2018年7月9日 7月9日活动 用户列表滚动展示接口
    Route::get('/activity/freeApplyInviteList/{wid}/{id}/{type}', 'MicroPageController@freeApplyInviteList'); // 许立 2018年7月10日 7月9日活动 邀请的好友页面

    // 许立 2018年08月02日 调查留言活动
    Route::get( '/activity/researchDetail/{wid}/{id}', 'ActivityController@researchDetail'); // 获取详情
    Route::post('/activity/researchSubmit/{wid}', 'ActivityController@researchSubmit'); // 提交回答

    // 许立 2018年08月02日 红包活动
    Route::get('/activity/bonusShow/{wid}', 'ActivityController@bonusShow'); // 红包展示
    Route::post('/activity/bonusUnpack/{wid}', 'ActivityController@bonusUnpack'); // 拆红包
    Route::post('/activity/bonusClose/{wid}', 'ActivityController@bonusClose'); // 拆红包弹窗关闭
    Route::get('/activity/bonusDetail/{wid}', 'ActivityController@bonusDetail'); // 红包详情

});
/* 预览不需要使用中间件 */
Route::group(['namespace' => 'Shop', 'prefix' => 'shop'], function() {
    Route::get('/page/preview/{wid}/{id}', 'MicroPageController@preview'); // 预览 add by jonzhang
    Route::get('/page/previewPage/{wid}/{id}', 'MicroPageController@previewPage'); //预览请求信息 add by jonzhang
    Route::get('/seckill/preview/{id}', 'ActivityController@seckillPreview'); //秒杀预览
    Route::get('/group/preview/{wid}/{id}','ProductController@groupPreview'); //商品分组详情预览
    //拼团微页面
    Route::match(['get','post'],'/page/showPinTuan', 'MicroPageController@showPageByPT'); // 拼团微页面 add by jonzhang
    //商品详情预览
    Route::get('/preview/{wid}/{id}', 'ProductController@preview'); //商品详情预览  add fuguowei 20171229
    /*邀请情况*/
    //海报页面
    Route::match(['get','post'],'/meeting/fighting', 'MeetingController@fighting'); //会场邀请战况
    Route::get('/meeting/getInvitationData','MeetingController@getInvitationData');//销售邀请数据
    Route::get('/meeting/invitationDetail','MeetingController@invitationDetail');//详情
    Route::get('/groups/preview/{id}','GroupsController@preview');//拼团详情预览
    Route::get('/groups/getPreViewGroups/{id}','GroupsController@getPreViewGroups');//拼团获取参团详情
    Route::get('/groups/preViewRecommendGroups','GroupsController@preViewRecommendGroups');//获取推荐
    Route::get('/groups/getPreViewDetailEvaluate/{id}','GroupsController@getPreViewDetailEvaluate');//获取商品评价


    Route::get('/share/preview', 'ShareEventController@preview'); //预览路由 add by jonzhang
    Route::get('/share/showDetail', 'ShareEventController@showDetailForPreview'); //预览 add by jonzhang
    Route::get('/share/more', 'ShareEventController@showMoreShareEvent'); //推荐 add by jonzhang

    Route::get('/product/getShareData/{pid}','ProductController@getShareData'); //何书哲 2018年11月06日 获取商品分享信息
});
