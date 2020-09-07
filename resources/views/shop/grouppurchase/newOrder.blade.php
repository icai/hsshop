@extends('shop.common.marketing')
@section('head_css') 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_73312555357699e0e66a1b0138c464fd.css"  media="screen">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_gt61j61y.css"  media="screen"> 
@endsection

@section('main')
    <div class="container">
        <div class="content confirm-container">
            <div class="js-groupon-guide guide-container clearfix">
                <div class="guide-step guide-step1 tuan-guide-step1">
                    <p class="guide-text">
                        1.选择商品开团/参团
                    </p>
                </div>
                <div class="guide-step guide-step2 tuan-guide-step2">
                    <p class="guide-text">
                        2.邀请好友参团
                    </p>
                </div>
                <div class="guide-step guide-step3 tuan-guide-step3">
                    <p class="guide-text">
                        3.人满成团
                    </p>
                </div>
            </div>
            <div class="app app-order">
                <div class="app-inner inner-order" id="js-page-content">
                    <div class="order-top-info-block block block-list border-top-0">
                        <!-- 物流 -->
                        <div class="block-item express border-0" id="js-logistics-container" style="margin-top: -1px;">
                            <div class="logistics hide">
                                <div class="js-logistics-select tabber tabber-n2 tabber-ios tabber-ios-gray-darker">
                                    <a href="javascript:void(0);" class="active" data-type="express">
                                        商家配送
                                    </a>
                                    <a href="javascript:void(0);" class="js-tabber-self-fetch hide" data-type="self-fetch">
                                        到店自提
                                    </a>
                                </div>
                            </div>
                            <div class="js-logistics-content logistics-content js-express">
                                <div class="">
                                    <div class="js-order-address express-panel js-edit-address express-panel-edit">
                                        <ul class="express-detail">
                                            <li class="clearfix">
                                                <span class="name">
                                                    收货人： 天体重
                                                </span>
                                                <span class="tel">
                                                    18368029000
                                                </span>
                                            </li>
                                            <li class="address-detail">
                                                收货地址： 河南省信阳市浉河区东方红大道251号大商集团新玛特购物广场F1层 SCS(大商集团新玛特购物广场)
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="js-logistics-content logistics-content js-self-fetch hide">
                            </div>
                            <div class="js-logistics-tips logistics-tips font-size-12 c-orange hide">
                                很抱歉，该地区暂不支持配送。
                            </div>
                        </div>
                    </div>
                    <div class="js-goods-list-container block block-list block-order ">
                        <div class="js-header header">
                            <a class="font-size-14" href="https://h5.youzan.com/v2/showcase/homepage?kdt_id=19120739">
                                07050705
                            </a>
                        </div>
                        <div class="js-goods-list">
                            <div class="js-goods-item order-goods-item clearfix block-list">
                                <div class="name-card name-card-goods clearfix block-item">
                                    <a href="javascript:;" class="thumb">
                                        <img class="js-view-image" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fq9Xi4vSuS8D804oC_1CD04sb8uA.png!100x100.jpg"
                                        alt="实物商品（购买时需填写收货地址，测试商品，不发货，不退款）">
                                    </a>
                                    <div class="detail">
                                        <div class="clearfix detail-row">
                                            <div class="right-col text-right">
                                                <div class="price">
                                                    ¥
                                                    <span>
                                                        0.02
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="left-col">
                                                <a href="javascript:;">
                                                    <h3 class="l2-ellipsis">
                                                        实物商品（购买时需填写收货地址，测试商品，不发货，不退款）
                                                    </h3>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="clearfix detail-row">
                                            <div class="right-col">
                                                <div class="num c-gray-darker">
                                                    ×
                                                    <span class="num-txt">
                                                        1
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="left-col">
                                                <p class="c-gray-darker sku">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clearfix detail-row">
                                            <div class="right-col">
                                                <div class="goods-action">
                                                </div>
                                            </div>
                                            <div class="left-col">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="js-presale-delivery presale-delivery-panel hide">
                        </div>
                        <div class="js-shop-ump info-panel block-item">
                            <span class="left-part">
                                店铺活动
                            </span>
                            <div class="ump-info arrow js-shop-ump-info">
                                <p class="ellipsis">
                                    团长优惠
                                </p>
                            </div>
                        </div>
                        <div class="js-hotel-info hotel-block-list block block-list border-top-0 hide">
                        </div>
                        <div class="js-express-block block-item info-panel">
                            <span class="left-part">
                                配送方式
                            </span>
                            <div class="js-express-info right-part c-gray-darker
                            arrow">
                                <p>
                                    免运费
                                </p>
                                <p class="font-size-12">
                                    快递发货
                                </p>
                            </div>
                        </div>
                        <div class="hide block-item js-localdelivery-block  info-panel ">
                        </div>
                        <div class="hide block-item js-localdelivery-block-info">
                        </div>
                        <div class="js-period-buy-info-block block-item info-panel hide">
                        </div>
                        <div class="js-period-buy-time-block block-item info-panel hide">
                        </div>
                        <div class="block-item order-message clearfix js-order-message">
                            <span class="">
                                买家留言：
                            </span>
                            <div class="input-wrapper">
                                <textarea class="js-msg-container" placeholder="点击给商家留言">
                                </textarea>
                            </div>
                        </div>
                        <div class="js-total block-item order-message border-none">
                            <span>
                                合计
                            </span>
                            <div class="js-sum-price input-wrapper c-orange theme-price-color
                            pull-right
                            ">
                                ¥0.02
                            </div>
                        </div>
                        <div class="js-empty-goods empty-goods hide">
                            <div class="empty-icon">
                            </div>
                            <p class="empty-info center c-gray-dark">
                                哎呀，当前没有可购买的商品，请重新选择～
                            </p>
                            <div class="empty-action center">
                                <a class="btn btn-white font-size-14 c-gray-darker" href="javascript:history.back();">
                                    返回重新选择
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="js-used-coupon block block-item hide">
                    </div>
                    <div class="js-point-panel block block-item hide">
                    </div>
                    <div class="js-ecard-panel block block-item hide">
                    </div>
                    <div class="js-send-message send-message block block-item">
                        <div class="info-panel info-panel-big clearfix">
                            <h4 class="left-part">
                                短信通知收件人
                            </h4>
                            <div class="right-part">
                                <button class="js-msg-switcher send-message-switcher ui-switcher 
                                ui-switcher-on
                                ">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="js-order-total block-item block order-total">
                        <p>
                            <span>
                                商品金额
                            </span>
                            <span class="pull-right c-gray-darker">
                                ¥0.02
                            </span>
                        </p>
                        <p>
                            <span>
                                运费
                            </span>
                            <span class="pull-right c-gray-darker">
                                + ¥0.00
                            </span>
                        </p>
                        <p>
                            <span>
                                活动优惠
                            </span>
                            <span class="pull-right c-gray-darker">
                                - ¥0.01
                            </span>
                        </p>
                    </div>
                    <div class="js-invalid-goods invalid-goods hide">
                    </div>
                    <div class="js-order-total-pay order-total-pay bottom-fix">
                        <div class="pay-container clearfix">
                            <div class="pull-right pull-margin-up">
                                <span class="c-gray-darker font-size-16">
                                    合计：
                                </span>
                                <span class="js-price c-red-f44 font-size-16 theme-price-color">
                                    ¥0.
                                </span>
                                <span class="js-price-sub c-red-f44 font-size-12 theme-price-color">
                                    01
                                </span>
                                <button class="js-confirm btn btn-red-f44 commit-bill-btn">
                                    提交订单
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-inner inner-order peerpay-gift" style="display:none;"
                id="sku-message-poppage">
                    <h2>
                        备注信息
                    </h2>
                    <ul class="block form js-message-container">
                    </ul>
                    <div class="action-container">
                        <button class="btn btn-white btn-block js-cancel">
                            查看订单详情
                        </button>
                    </div>
                </div>
                <div class="app-inner inner-order" style="display:none;padding-top:40px;"
                id="js-datetime-picker-poppage">
                </div>
                <div class="app-inner inner-order selffetch-address" style="display:none;padding-top:40px;"
                id="js-address-poppage">
                </div> 
            </div>
        </div>
    </div> 
@endsection
@section('page_js') 
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}shop/js/order_gt61j61y.js"></script>
    <!-- 临时使用 -->
    <script type="text/javascript">
        $(".js-confirm").click(function(){
            location.href="/shop/grouppurchase/groupon";
        });
    </script>
@endsection