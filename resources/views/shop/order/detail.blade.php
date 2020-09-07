@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/style1.3.25.6.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/index_a11364dc72ed102d49e9fe4d2a5d5d23.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/common_1.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/shop.w14p72o1.css">
    <style type="text/css">
        .btn-red-f44 {
            color: #fff !important;
            border-color: #f44 !important;
            background-color: #f44 !important;
        }
        .hide{display:none;}
        .order-total-pay .btn{line-height:50px;padding:0;}
        .btn_position{position:absolute;right:10px;bottom:10px;}
        .result-actions .action-button {position: relative;min-width: 60px;padding: 10px;font-size: 15px;background-color: #F72F37;border: none;color:#fff;}
        .result-actions .action-button::after {border-radius: 6px;content: '';position: absolute;top: 0;left: 0;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width: 200%;height: 200%;-webkit-transform: scale(0.5);-moz-transform: scale(0.5);-ms-transform: scale(0.5);transform: scale(0.5);-webkit-transform-origin: left top;-moz-transform-origin: left top;-ms-transform-origin: left top;transform-origin: left top;-webkit-perspective: 1000;-webkit-backface-visibility: hidden;pointer-events: none;border-top: 1px solid #e5e5e5;border-left: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5;border-bottom: 1px solid #e5e5e5;}
        .result-actions .action-button+.action-button {margin-right: 10px;}
        .result-actions .action-button a{
            color: #fff;
            font-size: 15px;
        }
        .order-total {padding: 10px !important;}
        .order-total p { line-height: 30px;}
        .js-goods-list-container .card_mi{
            display: inline-block;
            background: #F58E32;
            color: #fff;
            padding: 1px 2px;
            line-height: 18px;
            font-size: 11px !important;
        }
        .order_header{
            /*height:100px;*/
            background:url("{{ config('app.source_url') }}shop/images/order_header_bg.jpg") no-repeat;
            background-size: cover;
            color:#fff;
            padding:30px 10px;
        }
        .order_header.order-pintuan{
            background-image: url("{{ config('app.source_url') }}shop/images/order_header_bg_pintuan.jpg");
        }
        .order_header .order_icon{
            width:20px;
        }
        .order_header .status-text{
            position: relative;
            top: -4px;
            margin-left:10px;
            font-size: 18px;
        }
        .order_header .timer{
            padding-left:33px;
            padding-top:5px;
        }
        .express-panel1:before {
            background:none;
        }
        .js-express-info .copy{
            border:1px solid #ccc;
            color:#666;
            padding:5px 10px; 
            border-radius: 2px;
        }
        .js-express-info .copy_attr{
            border:1px solid #ccc;
            color:#666;
            padding:5px 10px; 
            border-radius: 2px;
        }
        .order-top-info-block .card-list{
            padding-bottom:15px;
        }
        .order-top-info-block .card-item{
            border:none;
        }
        .border_bottom{
            border-bottom: 1px solid #e5e5e5;
        }
        .use-info{
            padding:10px !important;
        }
        .use-info h3{
            font-weight: bold;
        }
        .use-info .info{
            line-height:18px;
            color:#666;
        }
        .arrow_container{
            text-align: center;
            padding: 0 0 20px 0;
        }
        .arrow_container img{
            width: 16px;
            position: relative;
            top: 3px;
        }
        .my-pay {
            color: #F72F37;
            border-top: 1px solid #e5e5e5;
            margin-top: 10px;
            padding-top: 10px;
        }
        .message-status {
            display: inline-block;
        }
        .order-goods-item+.order-goods-item{
            margin-top: 0;
        }
    </style>
@endsection
@section('main')
<div class="container " style="min-height: 482px;"> 
    <div class="content confirm-container">
        @if($orderDetail['groups_id'] != 0)
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
        @endif
        <div class="app app-order">
            <!-- 正常商品 -->
            @if($orderDetail['type'] != 12)
            <div class="app-inner inner-order" id="js-page-content">
                <div class="tips">
                    <p class="alarm-tips">请收藏该页面地址，方便查询订单状态。</p>
                </div>
                <div class="order-top-info-block block block-list block-list2 border-top-0">
                    <div class="confirm-important-message important-message relative block-item border-0">
                        <!-- 客户看 -->
                        <div class="order_header">
                            @if($orderDetail['status'] == 0)
                                <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order_wait_pay.png">
                                <div class="message-status order-state-topay timeIn">
                                    <span class="status-text">等待买家付款</span>
                                </div>
                                <p class="font-size-12 timer">请于
                                    @if($orderDetail['is_takeaway'] == 0)
                                        <span class="js-time" data-seconds="{{ $orderDetail['order_create_time'] }}"></span>
                                    @else
                                        <span class="js-unpay-time"></span>
                                    @endif
                                    内付款，超时订单将自动关闭
                                </p>
                                @elseif($orderDetail['status'] == 1 && $orderDetail['groups_status']==1)
                                <div class="message-status order-state-topay">
                                    <span class="status-text">待成团</span>
                                </div>
                                @elseif($orderDetail['status'] == 1)
                                <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order_fahuo.png">
                                <div class="message-status order-state-topay">
                                    @if($orderDetail['is_hexiao'] == 1)
                                    <span class="status-text">等待买家取货</span>
                                    @else
                                    <span class="status-text">等待商家发货</span>
                                    @endif
                                </div>
                                @elseif($orderDetail['status'] == 2)
                                <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order-fahuo.png">
                                <div class="message-status order-state-topay">
                                    @if($orderDetail['is_hexiao'] == 1)
                                    <span class="status-text">买家已提货</span>
                                    @else
                                    <span class="status-text">商家已发货 
                                    @if($orderDetail['is_takeaway'] == 1)
                                        <span class="show_count" style="font-size:14px"><span class="count-hour">00小时</span><span class="count-minute">00分</span><span class="count-second">00秒</span>将自动确认收货</span>
                                    @endif
                                    </span>
                                    @endif
                                </div>
                                @elseif($orderDetail['status'] == 3)
                                <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order_success.png">
                                <div class="message-status order-state-topay">
                                    <span class="status-text">订单已完成</span>
                                </div>
                                @elseif($orderDetail['status'] == 4)
                                <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order-close.png">
                                <div class="message-status order-state-topay">
                                    <span class="status-text">交易关闭</span>
                                </div>
                            @endif
                        </div>
                        <!-- hide样式为隐藏，后台调试是可去除hide -->

                    @if($orderDetail['no_express'] == 1)   
                    <!-- 物流 -->
                        <div class="nonne_logistics"><img src="{{ config('app.source_url') }}shop/static/images/express@2x.png"/>
                        @if($orderDetail['is_takeaway'] == 0)
                        无需物流
                        @else
                        商家配送
                        @endif
                    </div>
                    @endif
                    <div class="block-item border-0" id="js-logistics-container" style="margin-top: -1px;padding:0">
                        <div class="logistics hide">
                            <div class="js-logistics-select tabber tabber-n2 tabber-ios tabber-ios-gray-darker">
                                <a href="javascript:void(0);" class="active" data-type="express">商家配送</a>
                                <a href="javascript:void(0);" class="js-tabber-self-fetch hide" data-type="self-fetch">到店自提</a>
                            </div>
                        </div>
                        <div class="js-logistics-content js-express @if($orderDetail['is_hexiao'] == 1) hide @endif">
                            <div class="">
                                <div class="js-order-address express-panel js-edit-address address_list">
                                    <ul class="express-detail">
                                        <li class="clearfix">
                                            <span class="name">收货人： {{ $orderDetail['address_name']  }}</span>
                                            <span class="tel">{{$orderDetail['address_phone']}}</span></li>
                                        <li class="address-detail">收货地址： {{$orderDetail['address_detail']}}</li>
                                    </ul>
                                </div>
                                
                            </div>
                        </div>
                        <div class="js-logistics-content logistics-content js-self-fetch hide"></div>
                        <div class="js-logistics-tips logistics-tips font-size-12 c-orange hide">很抱歉，该地区暂不支持配送。</div>
                        <div class="ziti-container @if($orderDetail['is_hexiao'] == 0) hide @endif">
                            <div class="ziti-item">
                                <div class="ziti-item-left">提货地址：</div>
                                <div class="ziti-item-right J_go-destination" data-lng="{{ $orderDetail['ziti']['orderZiti']['longitude'] or '' }}" data-lat="{{ $orderDetail['ziti']['orderZiti']['latitude'] or '' }}">{{ $orderDetail['ziti']['orderZiti']['province_title'] or '' }}{{ $orderDetail['ziti']['orderZiti']['city_title'] or '' }}{{ $orderDetail['ziti']['orderZiti']['area_title'] or '' }}{{ $orderDetail['ziti']['orderZiti']['address'] or '' }}</div>
                            </div>
                            <div class="ziti-item">
                                <div class="ziti-item-left">温馨提示：</div>
                                <div class="ziti-item-right">请尽快到店自提</div>
                            </div>
                            <!--update by 韩瑜 2018-9-21 添加自提凭证入口-->
                            @if($orderDetail['status'] == 1)
                            <a href="{{ config('app.url') }}shop/order/zitiVoucher?oid={{ $orderDetail['id'] }}">
	                            <div class="ziti-item">
	                                <div class="ziti-item-left">提货码：</div>
	                                <div class="ziti-item-right" style="padding-left: 38%">{{ $orderDetail['hexiao_code'] or '' }}</div>
	                                <span style=""><img src="{{ config('app.source_url') }}shop/images/codego.png" alt="" style="width: 18px;vertical-align: middle; margin-right: 10px;"/></span>
	                            </div>
                            </a>
                            @elseif($orderDetail['status'] == 2)
                            <a href="{{ config('app.url') }}shop/order/hexiaoRedirect?oid={{ $orderDetail['id'] }}">
                                <div class="ziti-item">
                                    <div class="ziti-item-left">提货码：</div>
                                    <div class="ziti-item-right" style="padding-left: 38%">{{ $orderDetail['hexiao_code'] or '' }}</div>
                                    <span style=""><img src="{{ config('app.source_url') }}shop/images/codego.png" alt="" style="width: 18px;vertical-align: middle; margin-right: 10px;"/></span>
                                </div>
                            </a>
                            @endif
                            <!--end-->
                            <div class="ziti-item">
                                <div class="ziti-item-left">提货人：</div>
                                <div class="ziti-item-right">{{ $orderDetail['ziti']['ziti_contact'] or '' }} {{ $orderDetail['ziti']['ziti_phone'] or '' }}</div>
                            </div>
                            <a class="ziti-phone" href="tel:{{ $orderDetail['ziti']['orderZiti']['telphone'] or '' }}">
                                <i class="ziti-phone-icon"></i>
                                联系提货点
                            </a>
                        </div>
                    </div>
                </div>
                <div class="js-goods-list-container block block-list block-order ">
                    <div class="js-header header">
                        <a class="font-size-14" href='{{URL("/shop/index/$wid")}}'>{{ $orderDetail['shop_name'] }}</a>
                    </div>
                    <div class="js-goods-list goods-list">
                        @foreach($orderDetail['orderDetail'] as $item)
                        <div class="js-goods-item order-goods-item clearfix block-list">
                            <div class="name-card name-card-goods clearfix block-item">
                                <a href="javascript:;" class="thumb">
                                    <img class="js-view-image" src="{{ imgUrl($item['img']) }}" alt="{{$item['title']}}">
                                </a>
                                <div class="detail">
                                    <div class="clearfix detail-row">
                                        <div class="right-col text-right">
                                            <div class="price">￥
                                                 <span>
                                                    @if($item['after_discount_price']&&$item['after_discount_price']!="0.00")
                                                        {{$item['after_discount_price']}}
                                                    @else
                                                        {{$item['price']}}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="left-col">
                                            <a href="javascript:;">
                                                <h3 class="l2-ellipsis">{{$item['title']}}</h3>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="clearfix detail-row">
                                        <div class="right-col">
                                            <div class="num c-gray-darker">×
                                                <span class="num-txt">{{$item['num']}}</span></div>
                                        </div>
                                        <div class="left-col">
                                            <p class="c-gray-darker sku">{{$item['spec']}}</p>
                                        </div>
                                    </div>
                                    <div class="clearfix detail-row">
                                        <div class="right-col">
                                            <div class="goods-action"></div>
                                        </div>
                                        <div class="left-col"></div>
                                    </div>
                                </div>
                                @if(($item['refund_status'] == 0 || $item['refund_status'] == 5 || $item['refund_status'] == 9) && ($orderDetail['status'] == 1 || $orderDetail['status'] == 2 || $orderDetail['status'] == 3))
                                    <a data-prop-id="{{$item['product_prop_id']}}" data-pid="{{$item['product_id']}}" data-status="{{$item['refund_status']}}" class="btn btn_position applyRefundBtn">申请退款</a>
                                @endif
                                @if($item['refund_status'] == 1 || $item['refund_status'] == 2 || $item['refund_status'] == 6 || $item['refund_status'] == 7)
                                    <a href="/shop/order/refundDetailView/{{$wid}}/{{$orderDetail['id']}}/{{$item['product_id']}}/{{$item['product_prop_id']}}" class="btn btn-red c-red btn_position">退款处理中</a>
                                @elseif($item['refund_status'] == 3)
                                    <a href="/shop/order/refundDetailView/{{$wid}}/{{$orderDetail['id']}}/{{$item['product_id']}}/{{$item['product_prop_id']}}" class="btn btn-red c-red btn_position">退款中</a>
                                @elseif($item['refund_status'] == 4 || $item['refund_status'] == 8)
                                    <a href="/shop/order/refundDetailView/{{$wid}}/{{$orderDetail['id']}}/{{$item['product_id']}}/{{$item['product_prop_id']}}" class="btn btn-red c-red btn_position">退款成功</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="js-presale-delivery presale-delivery-panel hide"></div>
                    <div class="js-shop-ump info-panel block-item hide">
                        <span class="left-part">店铺活动</span>
                        <div class="ump-info arrow js-shop-ump-info">
                            <p class="ellipsis"></p>
                        </div>
                    </div>
                    <div class="js-hotel-info hotel-block-list block block-list border-top-0 hide"></div>
                    <div class="js-express-block block-item info-panel">
                        <span class="left-part">配送方式</span>
                        <div class="js-express-info right-part c-gray-darker">
                            @if($orderDetail['is_takeaway']) 
                            <p class="font-size-12">商家配送</p>
                            @else
                                @if($orderDetail['no_express'] == 1)
                                    无需物流
                                @else
                                    <p class="freight_judge">
                                        @if($orderDetail['freight_price'])
                                            ￥{{ $orderDetail['freight_price'] }}
                                        @else 0
                                        @endif
                                    </p>
                                    <p class="font-size-12">快递发货</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="hide block-item js-localdelivery-block  info-panel "></div>
                    <div class="hide block-item js-localdelivery-block-info"></div>
                 
                </div>
                <div class="block3">
                    <div class="block-item order-message clearfix js-order-message">
                        <span class="">买家留言：</span>
                        <div class="input-wrapper">
                            <textarea class="js-msg-container">{{$orderDetail['buy_remark']}}</textarea>
                        </div>
                    </div>
                    <div class="js-total block-item order-message border-none">
                        <div class="js-sum-price theme-price-color pro-total
                        pull-right
                        ">￥{{$orderDetail['products_price']}}</div>
                        <div class="pro-total-title">小计：</div>
                    </div>
                    <div class="js-empty-goods empty-goods hide">
                        <div class="empty-icon"></div>
                        <p class="empty-info center c-gray-dark">哎呀，当前没有可购买的商品，请重新选择～</p>
                        <div class="empty-action center">
                            <a class="btn btn-white font-size-14 c-gray-darker" href="javascript:history.back();">返回重新选择</a>
                        </div>
                    </div>
                </div>
                @if(!empty($coupon))
                <div class="js-used-coupon block block-item">
                    <div class="info-panel info-panel-big clearfix" style="padding-left: 10px;">
                        <h4 class="left-part">优惠</h4>
                        <div class="js-right-part right-part ">
                            {{$coupon['title']}}
                        </div>
                    </div>
                </div>
                @endif
                <div class="js-point-panel block block-item hide"></div>
                <div class="js-ecard-panel block block-item hide"></div>
                @if($orderDetail['groups_id'] == 0)
                    <div class="js-order-total block-item block order-total" style="border:none;">
                        <p>
                            <span>商品金额</span>
                            <span class="pull-right c-gray-darker">￥{{$orderDetail['productPrice']}}</span>
                        </p>
                        @if($orderDetail['discount']>0)
                        <p>
                            <span>满减</span>
                            <span class="pull-right c-gray-darker">-￥{{$orderDetail['discount']}}</span>
                        </p>
                        @endif
                        @if(!empty($orderDetail['coupon_price']))
                            <p>
                                <span>优惠</span>
                                <span class="pull-right c-gray-darker">- ￥{{$orderDetail['coupon_price']}}</span>
                            </p>
                        @endif
                        @if(!empty($orderDetail['change_price']))
                            <p>
                                <span>改价</span>
                                <span class="pull-right c-gray-darker">
                                    @if($orderDetail['change_price'] > 0)
                                        + ￥{{$orderDetail['change_price']}}
                                    @else
                                        - ￥{{$orderDetail['change_price']}}
                                    @endif
                                </span>
                            </p>
                        @endif
                        <p class="order-total-integral">
                            <span>积分抵现</span>
                            <span class="pull-right c-gray-darker">-￥{{$orderDetail['bonus_point_amount']??0}}</span>
                        </p>

                        @if($orderDetail['is_hexiao'] == 0) <!-- 核销订单不显示运费信息 -->
                        <p>
                            <span>运费</span>
                            <span class="pull-right c-gray-darker">+ ￥{{ $orderDetail['freight_price'] }}</span>
                        </p>
                        @endif
                        <p class="my-pay">
                            <span>实付金额</span>
                            <span class="pull-right"> ￥{{ $orderDetail['pay_price'] }}</span>
                        </p>
                    </div>  
                @else
                    <div class="js-order-total block-item block order-total" style="border:none;">
                        <p>
                            <span>商品金额</span>
                            <span class="pull-right c-gray-darker">￥{{$orderDetail['groupsProductPrice']}}</span>
                        </p>
                        <p>
                            <span>运费</span>
                            <span class="pull-right c-gray-darker">+ ￥{{ $orderDetail['freight_price'] }}</span>
                        </p>
                        @if($orderDetail['head_discount']>0)
                            <p>
                                <span>团长优惠</span>
                                <span class="pull-right c-gray-darker">- ￥{{ $orderDetail['head_discount'] }}
                                </span>
                            </p>
                        @endif
                        @if(!empty($orderDetail['change_price']) && $orderDetail['change_price'] != 0 )
                            <p>
                                <span>改价</span>
                                <span class="pull-right c-gray-darker">
                                @if($orderDetail['change_price'] > 0)
                                        + ￥{{$orderDetail['change_price']}}
                                @else
                                    - ￥{{$orderDetail['change_price']}}
                                @endif
                                </span>
                            </p>
                        @endif
                    </div>

                @endif
                

                @if($orderDetail['pay_way'] > 0)
                <div class="js-order-total block-item block order-total" style="border:none;padding: 0 10px 0 0;">
                    <p class="c-gray-darker">订单编号： {{ $orderDetail['oid'] }}
                        <span data-clipboard-text="{{ $orderDetail['oid'] }}" class="oid-copy js-oid-copy" style="cursor: pointer;">复制</span>
                    </p>
                    <p class="order-total-integral c-gray-darker">
                        <span>支付方式：</span>
                        <span>{{ $orderDetail['pay_way_name'] }}</span>
                    </p>
                </div>
                @endif
                <div class="js-invalid-goods invalid-goods hide"></div>
                <div class="js-order-total-pay order-total-pay bottom-fix timeIns" style="z-index:101">
                    <div class="pay-container clearfix">
                        <div class="pull-right pull-margin-up">
                            <span class="c-gray-darker font-size-16">合计：</span>
                            <span class="js-price c-red-f44 font-size-16 theme-price-color">￥{{ $orderDetail['pay_price'] }}</span>
                            @if($orderDetail['status'] == 0)
                                @if(isset($is_overdue) && $is_overdue == 1)
                                <a class="js-confirm btn btn-red-f44" href="javascript:;" onclick="alert('店铺打烊中，无法操作')">去支付</a>
                                @else
                                <a class="js-confirm btn btn-red-f44 commit-bill-btn" href="javascript:;">去支付</a>
                                @endif
                            @endif
                            </div>

                    </div>
                </div>
            </div>
            @else
            <!-- 卡密商品 -->
            <div class="app-inner inner-order" id="js-page-content">
                <div class="order_header">
                    <!-- <img src="{{ config('app.source_url') }}shop/images/order_header_bg.jpg"> -->
                    @if($orderDetail['status'] == 0)
                        <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order_wait_pay.png">
                        <div class="message-status order-state-topay timeIn">
                            <span class="status-text">等待买家付款</span>
                        </div>
                        <p class="font-size-12 timer">请于
                            <span class="js-time" data-seconds="{{ $orderDetail['order_create_time'] }}"></span>内付款，超时订单将自动关闭
                        </p>
                        @elseif($orderDetail['status'] == 1)
                        <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order_fahuo.png">
                        <div class="message-status order-state-topay">
                            <span class="status-text">等待商家发货</span>
                        </div>
                        @elseif($orderDetail['status'] == 3)
                        <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order_success.png">
                        <div class="message-status order-state-topay">
                            <span class="status-text">订单已完成</span>
                        </div>
                        @elseif($orderDetail['status'] == 4)
                        <img class="order_icon" src="{{ config('app.source_url') }}shop/images/order-close.png">
                        <div class="message-status order-state-topay">
                            <span class="status-text">交易关闭</span>
                        </div>
                    @endif
                </div>
                @if($orderDetail['status'] == 3)
                <!-- 卡密列表 -->
                <div class="order-top-info-block block block-list border-top-0">
                    <div class="js-express-block block-item info-panel border_bottom">
                        <span class="left-part">请注意查收您的账号</span>
                    </div>
                    @foreach($orderDetail['carmName'] as $key=>$cart)
                    <div class="card-list @if($key>5) card-list-more hide @endif">
                        <div class="js-express-block block-item info-panel card-item" style="padding-left: 0">
                            <span class="left-part">{{$cart['key']}}：<span id="acount_{{$key}}">{{$cart['value']}}</span></span>
                            <div class="js-express-info right-part c-gray-darker">
                                <!-- <p class="font-size-12">系统自动发货</p> -->
                                <a class="copy copy_{{$key}}" href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-target="#acount_{{$key}}">复制</a>
                            </div>
                        </div>
                        <div class="js-express-block block-item info-panel card-item"  style="padding-left: 0">
                            <span class="left-part">{{$orderDetail['carmAttr'][$key]['key']}}：<span id="acount_attr_{{$key}}">{{$orderDetail['carmAttr'][$key]['value']}}</span></span>
                            <div class="js-express-info right-part c-gray-darker">
                                <!-- <p class="font-size-12">系统自动发货</p> -->
                                <a class="copy_attr copy_attr_{{$key}}" data-clipboard-action="copy" data-clipboard-target="#acount_attr_{{$key}}" href="javascript:void(0);">复制</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @if(count($orderDetail['carmName']) > 5)
                    <div class="arrow_container">
                        <span data-status=0>查看更多</span><img src="{{ config('app.source_url') }}shop/images/arrow_bottom.png">
                    </div>
                    @endif
                </div>
                <!-- 卡密列表结束 -->
                <!-- 使用说明 -->
                <div class="order-top-info-block block block-list border-top-0 use-info">
                    <h3>使用说明</h3>
                    <div class="info">{{$orderDetail['userInstruction']}}</div>
                </div>
                <!-- 使用说明结束 -->
                @endif
                <div class="order-top-info-block block block-list block-list2 border-top-0">
                    <div class="confirm-important-message important-message important-message-order relative block-item border-0">
                       
                     @if($orderDetail['no_express'] == 1)   
                    <!-- 物流 -->
                    <div class="nonne_logistics"><img src="{{ config('app.source_url') }}shop/static/images/express@2x.png"/>无需物流</div>
                    @endif
                    <div class="block-item express border-0" id="js-logistics-container" style="margin-top: -1px;">
                        <div class="js-logistics-content js-express">
                            <div>
                                <div class="js-order-address express-panel js-edit-address address_list express-panel1">
                                    <ul class="express-detail" style="margin-left: 10px;">
                                        <li class="clearfix">
                                            <span class="name">收货人： {{ $orderDetail['address_name']  }}</span>
                                            <span class="tel">{{$orderDetail['address_phone']}}</span>
                                        </li>
                                    </ul>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="js-goods-list-container block block-list block-order ">
                    <div class="js-header header">
                        <a class="font-size-14" href='{{URL("/shop/index/$wid")}}'>{{ $orderDetail['shop_name'] }}</a>
                        <span class="card_mi">虚拟卡密</span>
                    </div>
                    <div class="js-goods-list goods-list">
                        @foreach($orderDetail['orderDetail'] as $item)
                        <div class="js-goods-item order-goods-item clearfix block-list">
                            <div class="name-card name-card-goods clearfix block-item">
                                <a href="javascript:;" class="thumb">
                                    <img class="js-view-image" src="{{ imgUrl($item['img']) }}" alt="{{$item['title']}}">
                                </a>
                                <div class="detail">
                                    <div class="clearfix detail-row">
                                        <div class="right-col text-right">
                                            <div class="price">￥
                                                 <span>
                                                    @if($item['after_discount_price']&&$item['after_discount_price']!="0.00")
                                                        {{$item['after_discount_price']}}
                                                    @else
                                                        {{$item['price']}}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="left-col">
                                            <a href="javascript:;">
                                                <h3 class="l2-ellipsis">{{$item['title']}}</h3>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="clearfix detail-row">
                                        <div class="right-col">
                                            <div class="num c-gray-darker">×
                                                <span class="num-txt">{{$item['num']}}</span></div>
                                        </div>
                                        <div class="left-col">
                                            <p class="c-gray-darker sku">{{$item['spec']}}</p>
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
                        @endforeach
                    </div>
                    <div class="js-presale-delivery presale-delivery-panel hide"></div>
                    <div class="js-shop-ump info-panel block-item hide">
                        <span class="left-part">店铺活动</span>
                        <div class="ump-info arrow js-shop-ump-info">
                            <p class="ellipsis"></p>
                        </div>
                    </div>
                    <div class="js-hotel-info hotel-block-list block block-list border-top-0 hide"></div>
                    <div class="js-express-block block-item info-panel">
                        <span class="left-part">配送方式</span>
                        <div class="js-express-info right-part c-gray-darker">
                            <p class="font-size-12">系统自动发货</p>
                        </div>
                    </div>
                    <div class="hide block-item js-localdelivery-block  info-panel "></div>
                    <div class="hide block-item js-localdelivery-block-info"></div>
                    
                </div>
                <div class="block3">
                    <div class="block-item order-message clearfix js-order-message">
                        <span class="">买家留言：</span>
                        <div class="input-wrapper">
                            <textarea class="js-msg-container">{{$orderDetail['buy_remark']}}</textarea>
                        </div>
                    </div>
                    <div class="js-empty-goods empty-goods hide">
                        <div class="empty-icon"></div>
                        <p class="empty-info center c-gray-dark">哎呀，当前没有可购买的商品，请重新选择～</p>
                        <div class="empty-action center">
                            <a class="btn btn-white font-size-14 c-gray-darker" href="javascript:history.back();">返回重新选择</a>
                        </div>
                    </div>
                </div>
                @if(!empty($coupon))
                <div class="js-used-coupon block block-item">
                    <div class="info-panel info-panel-big clearfix" style="padding-left: 10px;">
                        <h4 class="left-part">优惠</h4>
                        <div class="js-right-part right-part ">
                            {{$coupon['title']}}
                        </div>
                    </div>
                </div>
                @endif
                <div class="js-point-panel block block-item hide"></div>
                <div class="js-ecard-panel block block-item hide"></div>
                @if($orderDetail['groups_id'] == 0)
                    <div class="js-order-total block-item block order-total" style="border:none;">
                        <p>
                            <span>商品金额</span>
                            <span class="pull-right c-gray-darker">￥{{$orderDetail['productPrice']}}</span>
                        </p>
                        @if(!empty($orderDetail['coupon_price']))
                            <p>
                                <span>优惠</span>
                                <span class="pull-right c-gray-darker">- ￥{{$orderDetail['coupon_price']}}</span>
                            </p>
                        @endif
                        @if(!empty($orderDetail['change_price']))
                            <p>
                                <span>改价</span>
                                <span class="pull-right c-gray-darker">
                                    @if($orderDetail['change_price'] > 0)
                                        + ￥{{$orderDetail['change_price']}}
                                    @else
                                        - ￥{{$orderDetail['change_price']}}
                                    @endif
                                </span>
                            </p>
                        @endif
                        <p class="order-total-integral">
                            <span>积分抵现</span>
                            <span class="pull-right c-gray-darker">-￥{{$orderDetail['bonus_point_amount']??0}}</span>
                        </p>

                        @if($orderDetail['is_hexiao'] == 0) <!-- 核销订单不显示运费信息 -->
                        <p>
                            <span>运费</span>
                            <span class="pull-right c-gray-darker">+ ￥{{ $orderDetail['freight_price'] }}</span>
                        </p>
                        @endif
                        <p class="my-pay">
                            <span>实付金额</span>
                            <span class="pull-right"> ￥{{ $orderDetail['pay_price'] }}</span>
                        </p>
                    </div>  
                @else
                    <div class="js-order-total block-item block order-total" style="border:none;">
                        <p>
                            <span>商品金额</span>
                            <span class="pull-right c-gray-darker">￥{{$orderDetail['groupsProductPrice']}}</span>
                        </p>
                        <p>
                            <span>运费</span>
                            <span class="pull-right c-gray-darker">+ ￥{{ $orderDetail['freight_price'] }}</span>
                        </p>
                        @if($orderDetail['head_discount']>0)
                            <p>
                                <span>团长优惠</span>
                                <span class="pull-right c-gray-darker">- ￥{{ $orderDetail['head_discount'] }}
                                </span>
                            </p>
                        @endif
                        @if(!empty($orderDetail['change_price']) && $orderDetail['change_price'] != 0 )
                            <p>
                                <span>改价</span>
                                <span class="pull-right c-gray-darker">
                                @if($orderDetail['change_price'] > 0)
                                        + ￥{{$orderDetail['change_price']}}
                                @else
                                    - ￥{{$orderDetail['change_price']}}
                                @endif
                                </span>
                            </p>
                        @endif
                    </div>

                @endif
                @if($orderDetail['pay_way'] > 0)
                <div class="js-order-total block-item block order-total" style="border:none;padding: 0 10px 0 0;">
                    <p class="c-gray-darker">订单编号： {{ $orderDetail['oid'] }}
                        <span data-clipboard-text="{{ $orderDetail['oid'] }}" class="oid-copy js-oid-copy" style="cursor: pointer;">复制</span>
                    </p>
                    <p class="order-total-integral c-gray-darker">
                        <span>支付方式：</span>
                        <span>{{ $orderDetail['pay_way_name'] }}</span>
                    </p>
                </div>
                @endif
                <div class="js-invalid-goods invalid-goods hide"></div>
                <div class="js-order-total-pay order-total-pay bottom-fix timeIns" style="z-index:101">
                    <div class="pay-container clearfix">
                        <div class="pull-right pull-margin-up">
                            <span class="c-gray-darker font-size-16">合计：</span>
                            <span class="js-price c-red-f44 font-size-16 theme-price-color">￥{{ $orderDetail['pay_price'] }}</span>
                            @if($orderDetail['status'] == 0)
                                @if(isset($is_overdue) && $is_overdue == 1)
                                <a class="js-confirm btn btn-red-f44" href="javascript:;" onclick="alert('店铺打烊中，无法操作')">去支付</a>
                                @else
                                <a class="js-confirm btn btn-red-f44 commit-bill-btn" href="javascript:;">去支付</a>
                                @endif
                            @endif
                            </div>

                    </div>
                </div>
            </div>
            <!-- 卡密商品 -->

            @endif
            
            <div class="app-inner inner-order" style="display:none;padding-top:40px;" id="js-datetime-picker-poppage"></div>
            <div class="app-inner inner-order selffetch-address" style="display:none;padding-top:40px;" id="js-address-poppage"></div>
        </div>
        

        <div class="js-bottom-action">
            <div class="js-bottom bottom-fix">
                <div class="js-button-action result-actions">
                    @if($orderDetail['status']  == 2)
                    <input id="wid" type="hidden" value="{{session('wid')}}" />
                    <!-- 0 未满三天  1 满三天-->
                    <input type="hidden" class="three_day" value="0">
                    <button class="js-confirm-receive action-button tag tag-big tag-green pull-right received" data-kdtid="{{$orderDetail['id']}}">确认收货</button>
                    @if($orderDetail['is_hexiao'] == 0 && $orderDetail['is_takeaway'] == 0)
                    <button class="js-extend-receive action-button tag tag-big tag-white pull-right receiveDelay" data-kdtid="{{$orderDetail['id']}}">延长收货</button>
                    @endif
                    <span class="js-extend-receive action-button tag tag-big tag-white pull-right" data-kdtid="{{$orderDetail['id']}}"><a href="/shop/order/share/{{session('wid')}}/{{$orderDetail['id']}}">我要晒订单</a></span>
                    @endif
                    @if($orderDetail['status']  != 2 && $orderDetail['status']  != 0)
                    <span class="js-extend-receive action-button tag tag-big tag-white pull-right" data-kdtid="{{$orderDetail['id']}}"><a href="/shop/order/share/{{session('wid')}}/{{$orderDetail['id']}}">我要晒订单</a></span>
                    @endif
                    @if($orderDetail['groups_id'] != 0 && $orderDetail['status']  != 0)
                    <span class="js-extend-receive action-button tag tag-big tag-white pull-right" data-kdtid="{{$orderDetail['id']}}"><a href="/shop/grouppurchase/groupon/{{$orderDetail['groups_id']}}/{{session('wid')}}">查看拼团详情</a></span>
                    @endif
                </div>

            </div>
        </div>  
            
       
    </div>
</div>
<!-- 支付弹窗 -->
<div id="XkYNfCpz6p" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;" class="hide"></div>
<div id="ltFCNUqxmZ" class="pay-btn-popup popup hide" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: rgb(249, 249, 249); visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">
    <div style="margin-bottom: 10px;">
        <button type="button" data-pay-type="wxwappay" class="btn-pay btn btn-block btn-large btn-wxwappay  btn-green">微信支付</button></div>
</div>
<!-- 支付弹窗 -->
<!-- 微信支付点击弹窗 -->
<div id="M0OoCHUeQQ" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1; display: none"></div>
<div id="FiG1ldky0n" class="pay-popout popout-box-ios" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 290px; padding: 15px 0px 0px; opacity: 1;display: none">
    <div class="header center font-size-16 c-black">微信支付确认</div>
    <div class="content font-size-14 c-gray-dark">若您已付款成功，请点击“已完成支付”；若付款时遇到问题，可选择“其他支付方式”</div>
    <div class="action-container">
        <button class="btn btn-l c-black js-cancel-wechat-pay">其他支付方式</button>
        <button class="btn btn-l c-green js-ok-wechat-pay" >已完成支付</button>
    </div>
</div> 
<!-- 微信支付点击弹窗 -->

@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript"> 
        var status = "{{ $orderDetail['status'] }}";
        var refund_status = "{{ $orderDetail['refund_status'] }}";
        var wid = "{{$wid}}";
        var oid = "{{$orderDetail['id']}}";
        var pid = "{{$item['product_id']}}";
        var seckill_expire_seconds = "{{ $orderDetail['seckill_expire_seconds'] }}";
        var imgUrl = "{{ imgUrl() }}";
        var order_id = "{{ $orderDetail['id'] }}";
        var pay_price = "{{ $orderDetail['pay_price'] }}";
        var balance = "{{ $memberData['money']/100 }}"; //余额
        var remark_judge = "{{$orderDetail['buy_remark']}}"//买家留言
        var freight_judge = "{{ $orderDetail['freight_price'] }}"    
        var isHexiao = "{{ $orderDetail['is_hexiao'] }}";
        var order_type = "{{$orderDetail['type']}}";   
        var reqFrom = "{{ $reqFrom }}";
        var isTakeaway = "{{$orderDetail['is_takeaway']}}";//是否是外卖订单
        var created_at = "{{$orderDetail['created_at']}}" //订单创建时间
        var unpayMinite = "{{$configData['unpay_min']}}"//未支付多长时间取消订单(单位：分钟)
        var deliveryHour = "{{$configData['delivery_hour']}}"//发货后多长时间为自动收货(单位：小时)
        var seckill_id = "{{$orderDetail['seckill_id']}}";   //秒杀商品id
        var share_event_id = "{{$orderDetail['share_event_id']}}";  // 享立减商品id
    </script>
     @if($reqFrom == 'aliapp')
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
    @if($reqFrom == 'baiduapp')
		<script type="text/javascript" src="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan.js"></script>
	@endif
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/clipboard.min.js"></script>
    <!--当前页面js-->
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/shop.ljnzg7gp.js?v=1.0"></script>
@endsection