<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/8
 * Time: 16:43
 * 微信小程序路由
 */
Route::group(['namespace' =>'WXXCX','prefix' => 'xcx','middleware' => ['xcx','xcxAfter']], function()
{
    //支付
    Route::match(['post','get'],'/payment/waitPay','PaymentController@waitPay');//等待支付
    Route::match(['post','get'],'/payment/success','PaymentController@paySuccess'); //0元支付成功跳转
    Route::match(['post','get'],'/payment/fail','PaymentController@payFail'); //0元支付失败跳转


    //订单
    Route::match(['post','get'],'/order/waitSubmit','OrderController@waitSubmitOrder');//显示待提交的订单信息
    Route::match(['post','get'],'/order/submit','OrderController@submitOrder');//提交订单
    Route::match(['post','get'],'/order/showAll','OrderController@showAllOrders');//订单列表
    Route::match(['post','get'],'/order/showDetail','OrderController@showOrderDetail');//订单详情
    Route::match(['post','get'],'/order/cancel','OrderController@cancelOrder');//取消订单
    Route::match(['post','get'],'/order/statData','OrderController@statOrderData');//统计订单数量
    Route::match(['post','get'],'/order/receive','OrderController@receive');//确认收货
    Route::match(['post','get'],'/order/track','OrderController@getOrderTrackInfo');//查询物流信息
    Route::match(['post','get'],'/order/delay','OrderController@delay');//延迟收货
    Route::match(['post','get'],'/order/comments/{oid}','OrderController@comments');//订单评论列表
    Route::match(['post','get'],'/order/comment','OrderController@comment');//添加订单评论
    Route::get('/order/getEventCard','OrderController@getEventCard');//享立减卡片

    Route::get('/order/refundList/{status?}','OrderController@refundList');//退款订单列表
    Route::match(['post','get'],'/order/refundApply/{oid}/{pid}/{propID?}', 'OrderController@refundApply'); // 申请退款
    Route::post('/order/refundReturn/{refundID}', 'OrderController@refundReturn'); // 买家退款发货
    Route::match(['post','get'],'/order/refundApplyEdit/{oid}/{pid}/{propID?}', 'OrderController@refundApplyEdit'); // 申请退款修改
    Route::get('/order/refundDetail/{oid}/{pid}/{propID?}', 'OrderController@refundDetail'); // 退款详情
    Route::post('/order/refundCancel/{oid}/{refundID}', 'OrderController@refundCancel'); // 买家撤销退款
    Route::get('/order/refundMessages/{oid}/{pid}/{propID?}', 'OrderController@refundMessages'); // 退款协商列表
    Route::post('/order/refundAddMessage/{refundID}/{oid}', 'OrderController@refundAddMessage'); // 退款添加留言
    Route::get('/order/refundVerify/{refundID}', 'OrderController@refundVerify'); // 退款微信审核 钱款去向
    Route::get('/order/share/{oid}', 'OrderController@share'); // 订单分享页面信息
    Route::get('/order/getOrderLog/{oid}', 'OrderController@getOrderLog'); // 获取订单日志
    Route::get('/order/getRemark', 'OrderController@getRemark'); // 获取订单留言


    Route::get('/order/zitiVoucher','OrderController@zitiVoucher'); //自提订单凭证
    Route::get('/order/hexiaoConfirm','OrderController@hexiaoConfirm'); //自提订单凭证
    Route::match(['get','post'],'/order/scanLongConnet','OrderController@scanLongConnet'); //长连接接口
    Route::get('/order/hexiaoRedirect','OrderController@hexiaoRedirect'); //用户展示核销码核销成功跳转页面
    Route::match(['get','post'],'/order/hexiaoSure','OrderController@hexiaoSure'); //确定核销按钮

    //商品
    Route::match(['post','get'],'/product/index','ProductController@index'); //商品列表
    Route::match(['post','get'],'/product/detail/{id}','ProductController@detail'); //商品详情
    Route::match(['post','get'],'/product/sku/{id}','ProductController@sku'); //商品sku
    Route::get('/product/commentList/{id}', 'ProductController@commentList'); // 商品详情页评价列表
    Route::get('/product/commentDetail', 'ProductController@commentDetail'); // 商品评价详情
    Route::get('/product/commentReplies', 'ProductController@commentReplies'); // 商品评价回复列表
    Route::post('/product/commentReply', 'ProductController@commentReply'); // 商品评价回复
    Route::post('/product/commentLike/{eid}', 'ProductController@commentLike'); // 商品评价点赞
    Route::match(['get','post'],'/product/showRecommendProduct','ProductController@showRecommendProducts'); //推荐商品 add by jonzhang
    Route::get('/group/detail/{id}','ProductController@groupDetail'); //获取商品分组详情
    Route::get('/group/getProductGroupDetail','ProductController@getProductGroupDetail'); //获取商品分组下的所有商品

    //购物车
    Route::match(['post','get'],'/cart/index','CartController@index');  //购物车列表
    Route::match(['get','post'],'/cart/add/{wid}', 'CartController@add'); // 加入购物车
    Route::match(['get','post'],'/cart/discount', 'CartController@discount'); // 购物车满减优惠
    Route::match(['get','post'],'/cart/del', 'CartController@del'); // 删除购物车
    Route::match(['get','post'],'/cart/edit', 'CartController@edit'); // 修改购物车
    Route::match(['get','post'],'/cart/saveRemark', 'CartController@saveRemark'); // 添加留言

    /*地址管理*/
    Route::match(['post','get'],'/member/addressList','MemberController@addressList');  //收货地址列表
    Route::match(['post','get'],'/member/addressAdd','MemberController@addressAdd');  //添加编辑收货地址
    Route::match(['post','get'],'/member/addressDel/{id}','MemberController@addressDel');  //添加编辑收货地址
    Route::get('/member/addressDefault/{id}','MemberController@addressDefault');  //添加编辑收货地址
    Route::get('/member/region','MemberController@region');  //省市区列表
    Route::get('/member/getMember','MemberController@getMember');  //获取当前用户信息
    Route::get('/member/getDefaultAddress','MemberController@getDefaultAddress');  //获取默认地址
    Route::get('/member/addressAddFormWechat','MemberController@addressAddFormWechat');  //获取微信地址n
    Route::match(['post','get'],'/member/authorize','MemberController@authorizeUserInfo');  //用户授权更新用户信息
    Route::post('/member/authorizePhoneNumber','MemberController@authorizePhoneNumber');  //用户授权更新手机号码

    Route::get('/member/getMemberHomeModule','MemberController@getMemberHomeModule');  // add by 吴晓平 2018年08月29日 获取个人中心功能列表
    Route::get('/member/distributionExplan','MemberController@distributionExplan');
    Route::get('/member/setDistributionLevel','MemberController@setDistributionLevel');
    //营销活动
    Route::get('/seckill/detail/{id}', 'ActivityController@seckillDetail'); //秒杀详情
    Route::get('/seckill/sku/{id}','ActivityController@seckillSku'); //获取秒杀商品规格列表
    //优惠券
    Route::get('/activity/couponDetail/{id}', 'ActivityController@couponDetail'); // 优惠券详情
    Route::get('/activity/couponReceive/{id}', 'ActivityController@couponReceive'); // 优惠券领取
    //大转盘
    Route::get('/activity/wheel/{id}', 'ActivityController@wheel')->middleware('stationing'); // 大转盘展示页
    Route::match(['get','post'],'/activity/wheelPlay/{id}', 'ActivityController@wheelPlay'); // 大转盘操作
    Route::match(['get','post'],'/activity/myGift', 'ActivityController@myGift')->middleware('stationing'); // 我的奖品
    Route::match(['get','post'],'/activity/delGift/{id}', 'ActivityController@delGift'); // 删除我的赠品

    // 许立 2018年6月28日 调查留言活动
    Route::get( '/activity/researchDetail/{id}/{mid?}', 'ActivityController@researchDetail'); // 获取详情 何书哲 2018年7月27日 添加mid参数
    Route::post('/activity/researchSubmit', 'ActivityController@researchSubmit'); // 提交回答
    Route::get('/activity/myResearches', 'ActivityController@myResearches'); // 我的留言记录
    Route::get('/activity/researchRecord/{id}', 'ActivityController@researchRecord'); // 我的留言记录-记录详情

    // 许立 2018年07月18日 红包活动
    Route::get('/activity/bonusShow', 'ActivityController@bonusShow'); // 红包展示
    Route::post('/activity/bonusUnpack', 'ActivityController@bonusUnpack'); // 拆红包
    Route::get('/activity/bonusDetail', 'ActivityController@bonusDetail'); // 红包详情
    Route::post('/activity/bonusClose', 'ActivityController@bonusClose'); // 拆红包弹窗关闭

    Route::get('/activity/method/{id}/{type?}', 'ActivityController@method'); // 许立 2018年08月24日 营销活动-我的奖品-兑奖方式
    Route::post('/activity/setAwardAddress', 'ActivityController@setAwardAddress'); // 许立 2018年08月24日 营销活动设置奖品专有的收货地址

    Route::get('/activity/couponReceiveList/{id}', 'ActivityController@couponReceiveList'); // 优惠券的领取记录列表
    Route::get('/member/couponList/{status}', 'MemberController@couponList'); // 我的优惠券列表
    Route::get('/member/couponDetail/{id}', 'MemberController@couponDetail'); // 已领取优惠券详情
    Route::get('/member/couponProducts/{id}', 'MemberController@couponProducts'); // 优惠券指定商品列表

    Route::get('/member/cardRecharge', 'MemberController@cardRecharge'); // 会员卡充值页面
    Route::get('/member/balanceDetailAjax', 'MemberController@balanceDetailAjax'); // 充值详情
    Route::get('/member/addBalance', 'MemberController@addBalance'); // 提交充值订单

    //会员卡  MayJay
    Route::get('/member/memberCardDetail', 'MemberController@memberCardDetail'); //获取会员卡详情
    Route::get('/member/getMemberCard', 'MemberController@getMemberCard');//领取会员卡
    Route::post('/member/memberCardActive', 'MemberController@memberCardActive');//会员卡激活
    Route::get('/member/getMemberCardList', 'MemberController@getMemberCardList');//获取用户会员卡列表
    Route::post('/member/setDefaultMemberCard', 'MemberController@setDefaultMemberCard');//设置默认会员卡
    Route::post('/member/deleteMemberCard', 'MemberController@deleteMemberCard'); //删除会员卡
    Route::get('/member/getMemberCardSetting', 'MemberController@getMemberCardSetting'); //会员卡三级联动
    Route::get('/member/getMemberCardCode', 'MemberController@getMemberCardCode'); //会员卡弹出二维码API
    Route::get('/member/newMemberCard','MemberController@newMemberCard'); // 是否有新会员卡标识
    Route::post('/member/newMemberCardCallBack','MemberController@newMemberCardCallBack'); // 新会员卡标识回调

    //其他
    Route::post('/upload', 'XCXController@upload'); // 上传文件
    Route::match(['post','get'],'/store/homePage','XCXController@getHomePage');  //小程序店铺主页数据
    Route::match(['post','get'],'/store/microPage','XCXController@getXCXMicroPage');  //小程序微页面数据
    Route::match(['post','get'],'/store/logoMicroPage','XCXController@getLogoMicroPage')->middleware('stationing');  //何书哲 2018年9月11日 小程序开启底部logo链接跳转微页面
    Route::match(['post','get'],'/store/logoIsOpen','XCXController@shopLogoIsOpen');  //何书哲 2018年9月11日 店铺底部logo是否开启链接



    /*绑定手机号吗相关路由*/
    Route::match(['post','get'],'/bindmobile/sendCode','XCXController@sendCode');  //发送绑定手机验证码
    Route::match(['post','get'],'/bindmobile/verifyCode','XCXController@verifyCode');  //手机验证码验证 Herry 20180124
    Route::match(['post','get'],'/bindmobile/bindMobile','XCXController@bindMobile');  //小程序绑定小程序
    Route::match(['post','get'],'/bindmobile/changeMobile','XCXController@changeMobile');  //换帮手机号码
    Route::match(['post','get'],'/bindmobile/isBind','XCXController@isBind');  //是否需要绑定手机号
    Route::match(['post','get'],'/bindmobile/isShowChangeMobile','XCXController@isShowChangeMobile');  //个人中心是否显示修改手机号码
    Route::match(['post','get'],'/bindmobile/imgCode','XCXController@imgCode');  //图形验证码
    Route::match(['post','get'],'/bindmobile/getMemberMobile','XCXController@getMemberMobile');  //获取手机号码
    Route::match(['post','get'],'/store/getQrCode','XCXController@getQrCode');  //获取二维码 张永辉 2018年7月2日

    /* 积分 */
    Route::get('/point/addSignRecord/', 'PointController@addSignRecord'); // add by jonzhang 签到活动
    Route::get('/point/addShareRecord/', 'PointController@addShareRecord'); // add by jonzhang  分享送积分
    Route::get('/point/selectPointRecord', 'PointController@selectPointRecord'); // 积分变更 add by jonzhang
    Route::get('/point/selectSignTemplateData', 'PointController@selectSignTemplateData'); // 签到规则 add by jonzhang
    Route::get('/point/selectSignRule', 'PointController@selectSignRule'); // 签到活动规则 add by jonzhang

    Route::get('/point/showPoint', 'PointController@getPointByAmount'); // 通过金额得到对应的可用积分信息 add by jonzhang
    Route::get('/point/isShowPrompt','PointController@isGivePoint'); //   是否显示提示 add by jonzhang

    /**小程序分销**/
    Route::match(['get','post'],'/distribute/wealth','DistributeController@wealth'); //我的财富
    Route::match(['get','post'],'/member/withdrawal','MemberController@withdrawal'); //提现金额页面数据
    Route::match(['get','post'],'/member/addAccount','MemberController@addAccount'); //添加银行帐户
    Route::get('/member/selectAccount','MemberController@selectAccount'); //银行账户列表
    Route::match(['get','post'],'/distribute/getIncome','DistributeController@getIncome'); //获取收益记录
    Route::match(['get','post'],'/distribute/getCashLog','DistributeController@getCashLog'); //提现记录
    Route::match(['get','post'],'/distribute/withdrawals','DistributeController@withdrawals'); //提现页面信息
    Route::match(['get','post'],'/distribute/getMyAccount','DistributeController@getMyAccount'); //用户账号列表
    Route::match(['get','post'],'/distribute/getBank','DistributeController@getBank'); //银行列表
    Route::match(['get','post'],'/distribute/addAccount','DistributeController@addAccount'); //添加账户
    Route::match(['get','post'],'/distribute/delAccount','DistributeController@delAccount'); //删除账号
    Route::match(['get','post'],'/distribute/isShowDistribute','DistributeController@isShowDistribute'); //个人中心是否显示分销客
    Route::match(['get','post'],'/distribute/cancelDistribute','DistributeController@cancelDistribute'); //取消成为分销客
    Route::match(['get','post'],'/distribute/beDistribute','DistributeController@beDistribute'); //成为分销客
    Route::match(['get','post'],'/distribute/bindParent','DistributeController@bindParent'); //公共绑定接口
    Route::match(['get','post'],'/distribute/isShowWealth','DistributeController@isShowWealth'); //是否显示我的财富
    Route::match(['get','post'],'/distribute/isShowWealthEye','MemberController@isShowWealthEye'); //是否显示财富眼
    Route::match(['get','post'],'/distribute/isOpenWeath','MemberController@isOpenWeath'); //打开关闭财富眼
    Route::get('/distribute/explan','DistributeController@distributionExplan'); //分享二维码页面

    Route::get('/distribute/cashLog', 'DistributeController@cashLog'); // 提现记录
    Route::get('/distribute/distributeOrder', 'DistributeController@distributeOrder'); // 团队订单
    Route::get('/distribute/incomeLog', 'DistributeController@incomeLog'); // 收益记录
    Route::post('/distribute/apply', 'DistributeController@apply'); // 申请成为分销客
    Route::post('/distribute/myTeam', 'DistributeController@myTeam'); // 我的团队
    Route::match(['get','post'],'/distribute/productList', 'DistributeController@productList'); // 分销商品
    Route::match(['get','post'],'/distribute/apply/{wid}/{id}', 'DistributeController@apply'); // 申请成为分销客页面
    Route::get('/distribute/apply/{wid}/{id}', 'DistributeController@apply'); // 申请成为分销客页面



    /*团购相关路由*/
    Route::match(['get','post'],'/groups/detail/{id}','GroupsController@detail'); //团购详情
    Route::match(['get','post'],'/groups/getGroups/{id}','GroupsController@getGroups'); //获取凑团信息
    Route::match(['get','post'],'/groups/getProductEvaluate/{id}','GroupsController@getProductEvaluate'); //获取商品评价
    Route::match(['get','post'],'/groups/getEvaluateClassify/{id}','GroupsController@getEvaluateClassify'); //获取商品分类
    Route::match(['get','post'],'/groups/getDetailEvaluate/{id}','GroupsController@getDetailEvaluate'); //团购详情页获取评价详情
    Route::match(['get','post'],'/groups/recommendGroups','GroupsController@recommendGroups'); //获取推荐团购
    Route::match(['get','post'],'/groups/getSkus/{id}','GroupsController@getSkus'); //获取商品skus
    Route::match(['get','post'],'/groups/getSettlementInfo','GroupsController@getSettlementInfo'); //获取结算信息
    Route::match(['get','post'],'/groups/createOrder','GroupsController@createOrder'); //创建订单
    Route::match(['get','post'],'/groups/groupsDetail/{id}','GroupsController@groupsDetail'); //参团信息
    Route::match(['get','post'],'/groups/groupsList','GroupsController@groupsList'); //一键参团
    Route::match(['get','post'],'/groups/myGroups','GroupsController@myGroups'); //我的团购列表
    Route::match(['get','post'],'/groups/groupsById/{id}','GroupsController@groupsById'); //获取单个团信息
    Route::match(['get','post'],'/groups/getGroupsMessage','GroupsController@getGroupsMessage'); //参团数据
    Route::match(['get','post'],'/groups/getShareData/{gid}','GroupsController@getShareData'); //获取团购分享信息

    /*小程序底部导航栏*/
    Route::match(['get','post'],'/bar/pageFirst','XCXController@getBarMicroPageFirst'); //底部导航栏第一个微页面数据
    Route::match(['get','post'],'/bar/pageSec','XCXController@getBarMicroPageSec'); //底部导航栏第一个微页面数据
    Route::match(['get','post'],'/bar/pageThird','XCXController@getBarMicroPageThird'); //底部导航栏第一个微页面数据
    Route::match(['get','post'],'/bar/getPageBarList','XCXController@getPageBarList'); //获取底部导航栏page列表
    Route::get('/bar/barList','XCXController@getFooterBar'); //获取底部导航栏数据
    Route::get('/bar/syncBarList','XCXController@getSyncFooterBar'); //获取底部导航栏数据


    /*享立减参与日志路由*/
    Route::match(['get','post'],'/shareEvent/getAllActor/{activity_id}/{source_id}/{wid}','ShareEventController@getAllActorData');
    //享立减商品详情
    Route::get('/shareevent/product/showproductdetail','ShareEventController@showProductDetail')->middleware('stationing'); //add by jonzhang
    //待提交订单
    Route::get('/shareevent/order/waitsubmit','ShareEventController@processWaitSubmitShareEventOrder'); //add by jonzhang
    //提交订单
    Route::get('/shareevent/order/submit','ShareEventController@submitShareEventOrder'); //add by jonzhang
    //计算运费
    Route::get('/shareevent/order/feight','ShareEventController@statFreight'); //add by jonzhang
    //获取红包
    Route::get('/shareevent/getRedPacket','ShareEventController@getRedPacket'); //add MayJay
    //使用红包
    Route::post('/shareevent/useRedPacket','ShareEventController@useRedPacket'); //add MayJay
    //活动进度
    Route::post('/shareevent/getProcess','ShareEventController@getProcess'); //add MayJay
    //更多享立减活动
    Route::get('/shareevent/showMoreShareEvent','ShareEventController@showMoreShareEvent'); //add jonzhang
    //享立减活动参与者
    Route::get('/shareevent/showRecord','ShareEventController@showShareEventRecord'); //add jonzhang

    //生成卡片
    Route::get('/shareevent/shareCode','ShareEventController@getShareCode'); //add MayJay
    Route::get('/shareevent/shareCode2','ShareEventController@getShareCode2'); //何书哲 2018年11月27日 享立减生成卡片新接口
    Route::post('/shareevent/shareRecord','ShareEventController@shareRecord'); //何书哲 2018年8月6日 小程序添加分享记录


    Route::post('/collect/index','CollectFormController@index');

    /*享立减路由 for Gao*/
    Route::match(['get','post'],'/lishareEvent/getAllActor/{activity_id}/{source_id}/{wid}','LiShareEventController@getAllActorData');
    //享立减商品详情
    Route::get('/lishareevent/product/showproductdetail','LiShareEventController@showProductDetail'); //add by jonzhang
    //待提交订单
    Route::get('/lishareevent/order/waitsubmit','LiShareEventController@processWaitSubmitShareEventOrder'); //add by jonzhang
    //提交订单
    Route::get('/lishareevent/order/submit','LiShareEventController@submitShareEventOrder'); //add by jonzhang
    //计算运费
    Route::get('/lishareevent/order/feight','LiShareEventController@statFreight'); //add by jonzhang

    //获取红包
    Route::get('/lishareevent/getRedPacket','LiShareEventController@getRedPacket'); //add MayJay
    //使用红包
    Route::post('/lishareevent/useRedPacket','LiShareEventController@useRedPacket'); //add MayJay
    //活动进度
    Route::post('/lishareevent/getProcess','LiShareEventController@getProcess'); //add MayJay
    //更多享立减活动
    Route::get('/lishareevent/showMoreShareEvent','LiShareEventController@showMoreShareEvent'); //add jonzhang
    //享立减活动参与者
    Route::get('/lishareevent/showRecord','LiShareEventController@showShareEventRecord'); //add jonzhang
    //注册信息
    Route::post('/lishareevent/register','LiShareEventController@register'); //add Herry
    //助减
    Route::post('/lishareevent/reduceLi','LiShareEventController@reduceLi');//MayJay
    //分享回调
    Route::post('/lishareevent/shareCallBack','LiShareEventController@shareCallBack');
    /*享立减路由end for Gao*/

    Route::get('/lishareevent/friendLi','LiShareEventController@friendLi'); //add cwh

    /* 免费领小程序 */
    Route::match(['get','post'],'/freeXCX/apply','FreeXCXController@apply'); // 申请报名 Herry
    Route::get('/freeXCX/applySuccess','FreeXCXController@applySuccess'); // 报名成功 Herry
    /*  刮刮卡 */
    Route::post('/activity/scratch', 'ActivityController@scratch'); // 刮刮卡显示
    Route::post('/activity/scratchPlay', 'ActivityController@scratchPlay'); // 刮刮卡抽奖
    Route::match(['get','post'],'/activity/myScratchGift', 'ActivityController@myScratchGift')->middleware('stationing'); // 我的奖品
    Route::match(['get','post'],'/activity/delScratchGift/{id}', 'ActivityController@delScratchGift'); // 删除我的奖品

    Route::get('/reception/getZitiListBySort','ReceptionController@getZitiListBySort'); //小程序获取自提列表（由近到远排序）
    Route::get('/reception/getZitiDates/{id}','ReceptionController@getZitiDates'); //小程序获获取相应的自提点日期，时间
    Route::get('/reception/getZitiInfoById/{id}','ReceptionController@getZitiInfoById'); //小程序获获取相应的自提点日期，时间

    Route::get('/store/contact','StoreController@contact'); //联系我们 Herry 2018/06/26 10:10

    // 许立 2018年09月10日 收藏模块接口组
    Route::get('/member/favoriteListApi', 'MemberController@favoriteListApi'); // 我的收藏
    Route::get('/member/isFavorite', 'MemberController@isFavorite'); // 商品或活动是否收藏
    Route::post('/member/favorite', 'MemberController@favorite'); // 收藏
    Route::post('/member/cancelFavorite', 'MemberController@cancelFavorite'); // 取消收藏

    // 吴晓平 2019年12月18日 订阅模板消息
    Route::get('get/sub_message/list', 'XCXController@getAllTemplates');
    Route::get('get/sub_message/send', 'XCXController@messagePush');

});

Route::group(['namespace' =>'WXXCX','prefix' => 'xcx'], function() {
    //登录
    Route::match(['post','get'],'/checkLogin','XCXController@checkLogin');
    Route::match(['post','get'],'/checkLoginV2','XCXController@checkLoginV2');
    // @update 张永辉 直播验证用户登陆绑定关系 2020年3月20日11:03:22
    Route::match(['post', 'get'], '/liveUserLogin', 'XCXController@liveUserLogin');
    //支付回调
    Route::match(['post','get'],'/payment/payNotify','PaymentController@payNotify');
    //小程序第三方平台推送
    Route::match(['post','get'],'/third/receiveEvent','XCXController@receiveEvent');
    //小程序二维码回调
    Route::match(['post','get'],'/third/sendCallBack','XCXController@sendCallBack');
    //小程序上传代码审核结果回调
    Route::match(['post','get'],'/third/auditCode/{appId}','XCXController@receiveAudit');
    //查询店铺信息 仅供开发者使用
    Route::get('/homepage','XCXController@getHomePageByDeveloper');
    //查询某个微页面信息 仅供开发者使用
    Route::get('/micropage','XCXController@getXCXMicroPageByDeveloper');
   //查询店铺是否过期
    Route::get('/wid/checkExpire','XCXController@getWidData');
    //小程序获取基础信息
    Route::match(['get','post'],'/base','XCXController@base');
    //何书哲 2018年6月27日 小程序获取用户信息
    Route::match(['get','post'],'/getMemberInfo','XCXController@getMemberInfo');

});
