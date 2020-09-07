@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/style1.3.25.6.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/index_a11364dc72ed102d49e9fe4d2a5d5d23.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/common_1.css">
    <link href="{{ config('app.source_url') }}static/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/shop.w14p72o2.css">
    <style type="text/css">
        .btn-red-f44 {color: #fff !important;border-color: #f44 !important;background-color: #f44 !important;
        }
        .no-coupon:after{
            background:none !important;
            background-size:0;
        }
        .hide{display:none;}
        .icon-circle-info{cursor:pointer;}
        .icon-check{cursor:pointer;}
        h4 {
            font-size: 14px;
            font-weight: 400;
        }
    </style>
@endsection
@section('main')
<div class="container " style="min-height: 482px;padding: 0;margin:0">
    <div class="content confirm-container">
        <div class="app app-order">
            <div class="app-inner inner-order" id="js-page-content">
                <div class="order-top-info-block block block-list border-top-0">
                    <!-- 物流 -->
                    <div class="block-item express border-0" id="js-logistics-container" style="margin-top: -1px;">
                        <div class="J_express">
                            <div class="js-logistics-content logistics-content js-express">
                                <div class="">
                                    <!-- 有收货地址的时候 -->
                                    @if(!empty($addressData))
                                    <input type="hidden" name="address_id" value="{{ $addressData['id']  }}">
                                    <a href="javascript:;" class="js-order-address express-panel js-edit-address express-panel-edit address_list shareEventFix">
                                        <ul class="express-detail">
                                            <li class="clearfix">
                                                <span class="name">收货人： {{ $addressData['name']  }}</span>
                                                <span class="tel">{{$addressData['phone']}}</span>
                                            </li>
                                            <li class="address-detail">收货地址： {{$addressData['detail_address']}}</li>
                                        </ul>
                                    </a>
                                    @else
                                    <!-- 没有收货地址的时候 -->
                                    <div class="js-order-address express-panel js-edit-address express-panel-edit address_list hide">
                                        <ul class="express-detail">
                                            <li class="clearfix">
                                                <span class="name">收货人:</span>
                                                <span class="tel"></span></li>
                                            <li class="address-detail"></li>
                                        </ul>
                                    </div>
                                    <a href="javascript:void(0);" class="js-edit-address empty-address-tip no-user-select js-input-validation no_address_data">新增收货地址</a>
                                    @endif
                                </div>
                            </div>
                            <div class="js-logistics-content logistics-content js-self-fetch hide"></div>
                            <div class="js-logistics-tips logistics-tips font-size-12 c-orange hide">很抱歉，该地区暂不支持配送。</div>
                        </div>
                    </div>
                </div>
                <div class="js-goods-list-container block block-list block-order ">
                    <div class="js-header header">
                        <a class="font-size-14;" style="color:#333" href="{{$shop_url}}">{{$shop_name}}</a>
                    </div>
                    <div class="js-goods-list">
                        <div class="js-goods-item order-goods-item clearfix block-list">
                            <div class="name-card name-card-goods clearfix block-item">
                                <a href="javascript:;" class="thumb">
                                    <img class="js-view-image" src="{{ config('app.url') }}{{$productData['img']}}">
                                </a>
                                <div class="detail">
                                    <div class="clearfix detail-row">
                                        <div class="right-col text-right">
                                            <div class="price">￥
                                                <span>{{sprintf('%.2f',$productData['price'])}}</span>
                                            </div>
                                        </div>
                                        <div class="left-col">
                                            <a href="javascript:;">
                                                <h3 class="l2-ellipsis">{{$productData['product_name']}}</h3>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="clearfix detail-row">
                                        <div class="right-col">
                                            <div class="num c-gray-darker">×
                                                <span class="num-txt">{{$productData['num']}}</span></div>
                                        </div>
                                        <div class="left-col">
                                            <p class="c-gray-darker sku">{{$productData['product_spec']}}</p>
                                        </div>
                                    </div>
                                    <div class="clearfix detail-row">
                                        <div class="right-col">
                                            <div class="goods-action"></div>
                                        </div>
                                        <div class="left-col"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="js-presale-delivery presale-delivery-panel hide"></div>
                    <div class="js-hotel-info hotel-block-list block block-list border-top-0 hide"></div> 
                    <div class="js-express-block block-item info-panel">
                        <span class="left-part">配送方式</span>
                        <div class="js-express-info right-part c-gray-darker arrow J_hexiao-express" >
                            <p class="freight J_freight" data-freight="{{ $freight }}">￥{{$freight}}</p>
                            <p class="font-size-12 J_fetch-method">快递发货</p>
                        </div>
                    </div>

                    <div class="hide block-item js-localdelivery-block  info-panel "></div>
                    <div class="hide block-item js-localdelivery-block-info"></div>
                    <div class="block-item order-message clearfix js-order-message">
                        <span class="">买家留言：</span>
                        <div class="input-wrapper">
                            <textarea class="js-msg-container" placeholder="点击给商家留言"></textarea>
                        </div>
                    </div>
                    <div class="js-total block-item order-message border-none">
                        <span>小计</span>
                        <div class="js-sum-price input-wrapper c-orange theme-price-color pull-right J_ziti-price">￥{{sprintf('%.2f',$product_amount)}}</div>
                    </div>
                    <div class="js-empty-goods empty-goods hide">
                        <div class="empty-icon"></div>
                        <p class="empty-info center c-gray-dark">哎呀，当前没有可购买的商品，请重新选择～</p>
                        <div class="empty-action center">
                            <a class="btn btn-white font-size-14 c-gray-darker" href="javascript:history.back();">返回重新选择</a>
                        </div>
                    </div>
                </div>
                <!-- <div class="js-used-coupon block block-item">
                    <div class="info-panel info-panel-big clearfix">
                        <h4 class="left-part">优惠</h4>
                        <div class="js-right-part right-part arrow">
                            <div class="js-normal-coupon detail c-gray-darker js-change-coupon coupon_list" style="line-height: 1.6;">
                                <div class="use-coupon">
                                    <span>name</span>
                                    <p>下单立减discount_amount元</p>
                                </div>
                                <div class="no-use" style="display:none">
                                    请选择优惠券
                                </div>
                            </div>
                            <div class="js-normal-coupon detail c-gray-darker noCoupon">
                                <span>暂无优惠券</span>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="js-point-panel block block-item hide"></div>
                <div class="js-ecard-panel block block-item hide"></div>

                <div class="js-order-total block-item block order-total">
                    <p>
                        <span>商品金额</span>
                        <span class="pull-right c-gray-darker J_ziti-price">￥{{sprintf('%.2f',$product_amount)}}</span>
                    </p>
                    <p>
                        <span>运费</span>
                        <span class="pull-right c-gray-darker sum-freight J_freight" data-freight="{{ $freight }}">￥{{$freight}}</span>
                    </p>
                </div>
                <div class="js-invalid-goods invalid-goods hide"></div>
                <div class="J_self-fetch-invalid hide">
                    
                </div>
                <div class="js-order-total-pay order-total-pay bottom-fix">
                    <div class="pay-container clearfix">
                        <div class="pull-right pull-margin-up">
                            <span class="c-gray-darker font-size-16">合计：</span>
                            <span class="J_ziti-price-last js-price c-red-f44 font-size-16 theme-price-color lastPrice" data-price="{{$last_amount}}" id="last_amount"></span>
                            <button class="js-confirm btn btn-red-f44 commit-bill-btn">提交订单</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-inner inner-order peerpay-gift" style="display:none;" id="sku-message-poppage">
                <h2>备注信息</h2>
                <ul class="block form js-message-container"></ul>
                <div class="action-container">
                    <button class="btn btn-white btn-block js-cancel">查看订单详情</button></div>
            </div>
            <div class="app-inner inner-order" style="display:none;padding-top:40px;" id="js-datetime-picker-poppage"></div>
            <div class="app-inner inner-order selffetch-address" style="display:none;padding-top:40px;" id="js-address-poppage"></div>
        </div>
    </div>
</div>
<div class="tip_container"></div>
<!-- 选择收货地址弹窗 -->
<div id="PdXcgqJuo6" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display:none"></div>
<div id="zSKAL2haMN" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;display:none">
    <div class="js-scene-address-list">
        <div class="address-ui address-list">
            <h4 class="address-title">选择收货地址</h4>
            <div class="cancel-img js-cancel close_choose_address"></div>
            <div class="js-address-container address-container block block-list border-top-0 address_container">
                @forelse($userAddress['all'] as $item)
                <div  class="js-address-item block-item ">
                    <div>
                        @if($item['type']=='1')
                        <div class="icon-check icon-checked" data-id="{{$item['id']}}"></div>
                        @else
                        <div class="icon-check" data-id="{{$item['id']}}"></div>
                        @endif
                        <p>
                            <span class="address-name" style="margin-right: 5px;">{{$item['name']}}</span>
                            <span class="address-tel">{{$item['phone']}}</span></p>
                        <span class="address-str address-str-sf">收货地址：{{$item['detail_address']}}</span>
                        <div class="address-opt  js-edit-address " data-id="{{$item['id']}}" data-province="{{$item['province_id']}}" data-city="{{$item['city_id']}}" data-area="{{$item['area_id']}}" data-code="{{$item['code']}}" data-address="{{$item['address']}}" data-type="{{$item['type']}}">
                            <i class="icon-circle-info"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="add-address js-add-address">
                <span class="icon-add"></span>
                <a class="" href="javascript:;">新增地址</a>
                <span class="icon-arrow-right"></span>
            </div>
        </div>
    </div>
</div>
<!-- 选择收货地址弹窗 -->
<!-- 新优惠券弹窗 -->
<div class="couponsModel hide">
    <ul class="couponsList">
        
    </ul>
    <div class="noUse">不使用优惠券</div>
</div>
<!-- 新优惠券弹窗 -->
<!-- 支付弹窗 -->
<div id="XkYNfCpz6p" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display:none"></div>
<div id="ltFCNUqxmZ" class="pay-btn-popup popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: rgb(249, 249, 249); visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;display:none">
    <div style="margin-bottom: 10px;">
        <button type="button" data-pay-type="wxwappay" class="btn-pay btn btn-block btn-large btn-wxwappay  btn-green">微信支付</button></div>
    <div style="margin-bottom: 10px;">
        <button type="button" data-pay-type="aliwap" class="btn-pay btn btn-block btn-large btn-aliwap  btn-blue">支付宝付款</button></div>
    <div style="margin-bottom: 10px;">
        <button type="button" data-pay-type="yzpay" class="btn-pay btn btn-block btn-large btn-yzpay  btn-white">信用卡付款</button></div>
    <div style="margin-bottom: 10px;">
        <button type="button" data-pay-type="baiduwap" class="btn-pay btn btn-block btn-large btn-baiduwap  btn-white">储蓄卡付款</button></div>
</div>
<!-- 支付弹窗 -->
<!-- 微信支付点击弹窗 -->
<div id="M0OoCHUeQQ" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display:none"></div>
<div id="FiG1ldky0n" class="pay-popout popout-box-ios" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 290px; padding: 15px 0px 0px; opacity: 1;display:none">
    <div class="header center font-size-16 c-black">微信支付确认</div>
    <div class="content font-size-14 c-gray-dark">若您已付款成功，请点击“已完成支付”；若付款时遇到问题，可选择“其他支付方式”</div>
    <div class="action-container">
        <button class="btn btn-l c-black js-cancel-wechat-pay">其他支付方式</button>
        <button class="btn btn-l c-green js-ok-wechat-pay">已完成支付</button>
    </div>
</div>
<!-- 微信支付点击弹窗 -->


<!-- 运费选择弹窗 -->
<div id="P9bxm4G8NL" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display: none;"></div>
<div id="ko7oBEIP8n" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;display:none">
    <div class="js-scene-address-list">
        <div class="address-ui address-list">
            <h4 class="address-title">配送方式</h4>
            <div class="cancel-img js-cancel"></div>
            <div class="js-address-container address-container block block-list border-top-0">
                <div id="js-express-item-0" data-type="0" class="js-express-item  block-item ">
                    <div>
                        <div class="icon-check  icon-checked" ></div>
                        <p>
                            <span class="address-name" style="margin-right: 5px;">快递发货</span>
                        </p>
                        <span class="address-str address-str-sf">由商家选择合作快递为您服务</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 选择地址弹框 -->
<div class="addr-popup hide">
    <p class="addr-title">选择提货点</p>
    <div class="select-box">
        <select class="addr-city">
        </select>
        <span class="select-arrow"></span>
        <span class="search-icon"></span>
    </div>
    <div class="search-box">
        <div class="search-input-box">
            <i class="search-input-icon"></i>
            <span class="calcel-btn">×</span>
            <input type="text" class="search-input J_search-input" placeholder="请输入自提点名称">
            <span class="search-text J_search">搜索</span>
        </div>
    </div>
    <div class="addr-container">
    </div>
</div>

<!-- 选择提货时间弹框 -->
<div id="fetch-time-wrap" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display:none"></div>
<div id="fetch-time-box" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;display:none">
    <div class="js-scene-address-list">
        <div class="address-ui address-list">
            <h4 class="address-title">请选择到店时刻</h4>
            <div class="cancel-img js-fetch-cancel"></div>
            <div class="week-box">
                <div class="week-container" style="display:inline-block">
                </div>
                <div id="datetimepicker" data-date="2018-06-11" data-date-format="dd-mm-yyyy" class="datetimepicker-box" >
                    <input type="button" class="timer-input" size="16" >
                    <div class="date-icon"></div>
                </div>
            </div>
            <div class="timer-box">
            </div>
            <div class="fetch-time-confirm J_fetch-time-confirm">确定</div>
        </div>
    </div>
</div>
<!-- 查看商品无法购买原因 -->
<div id="invalid-reason-wrap" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display:none;"></div>
<div id="invalid-reason" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;display:none;">
    <div class="js-scene-address-list">
        <div class="address-ui address-list">
            <h4 class="address-title">以下商品无法一起下单</h4>
            <div class="cancel-img js-invalid-cancel"></div>
            <div class=" invalid-container border-top-0" style="max-height:360px; overflow-y:scroll;padding-top: 10px;">
              
            </div>
        </div>
    </div>
</div>
<!-- 运费选择弹窗 -->
<!-- 团长优惠开始 -->
<div class="popup group-popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;display: none;">
    <div class="js-scene-address-list">
        <div class="address-ui address-list">
            <h4 class="address-title">店铺活动</h4>
            <div class="cancel-img js-cancel"></div>
            <div class="js-address-container address-container block block-list border-top-0">
                <div id="js-express-item-0" data-type="0" style="padding: 15px 10px 20px;position: relative;" class="js-express-item  block-item">
                    团长优惠
                    <span style="position: absolute;right:0;top:0;padding: 20px 10px;margin-right:20px;">1</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 团长优惠结束 -->
<!-- 页面加载开始 -->

<!-- 页面加载结束 -->
@include('shop.common.footer')
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<script> 
    var no_coupon_point=true; //不使用优惠券的积分数
    var no_coupon_bonus_points=true; //不使用优惠券的积分抵现金额
    var point = 0; //使用优惠券后积分数
    var bonus_points = 0; //使用优惠券后的积分抵现金额
    var goods_price = 0; //商品金额
    var goods_price_temp = 0; //商品金额
    var freight = {{$freight}}; //运费 
    var regions_datas =0;
    var is_groups = false;
    var imgUrl = 0;  
    var pay_price = 0;
    var pifa = false //批发
    var balance = '{{$balance}}'; //余额
    var host = "{{ config('app.url') }}";
    var couponList = [];
    var wid = "{{ $wid }}";
    var use_point_amount = 0;
    var coupon_amount = 0;//原始优惠券价格
    var distributionData = 0;
    var per = 0; // 一块钱多少积分
    var totalPoint = 0;
    var coupon_id = null;
    var productId = "{{$productData['product_id']}}";
    var productAmount = "{{$product_amount}}"
    var reqFrom = "{{ $reqFrom }}";
</script>
 @if($reqFrom == 'aliapp')
<script type="text/javascript" src="https://appx/web-view.min.js"></script>
@endif
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/shop.w14p72o2.js"></script>  
@if($reqFrom == 'baiduapp')
	<script type="text/javascript" src="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan.js"></script>
@endif
@endsection