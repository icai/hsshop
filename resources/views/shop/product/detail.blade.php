@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/goods_62d5db3e3f0f2435e941566b8d882e5d.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/product_detail.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?t=123">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <style type="text/css">
        .js-footer{margin-bottom:51px;}
        .swiper-wrapper img{width: 100%;}
        .c-bg-gray{
            background:#c9c9c9 !important;
        }
        .c-bg-gray::after{border-top:none;}
        .hexiao{position:absolute;right:10px;width:60px !important;top: 20px;}
        /*客服弹窗*/
        .weui-mask {
            background: rgba(0,0,0,.6);
        }
        .weui-mask, .weui-mask_transparent {
            position: fixed;
            z-index: 1000;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
        }
        .weui-mask {
            z-index: 1000;
        }
        .weui-mask.weui-mask--visible {
            opacity: 0.5;
            visibility: visible;
        }
        .weui-actionsheet {
            position: fixed;
            left: 0;
            bottom: 0;
            -webkit-transform: translateY(100%);
            transform: translateY(100%);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            z-index: 5000;
            width: 100%;
            background-color: #e6e6e6;
            -webkit-transition: -webkit-transform .3s;
            transition: -webkit-transform .3s;
            transition: transform .3s;
            transition: transform .3s,-webkit-transform .3s;
        }
        .weui-actionsheet_toggle {
            -webkit-transform: translate(0);
            transform: translate(0);
        }
        .weui-actionsheet {
            z-index: 200000;
        }
        .weui-actionsheet .weui-actionsheet__title {
            padding: 8px 0;
            text-align: center;
            font-size: 16px;
            background-color: #fff;
            position: relative;
            height: 34px;
            color: #4c4c4c;
            line-height: 34px;
        }
        .weui-actionsheet .weui-actionsheet__title:after {
            content: " ";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1px;
            border-top: 1px solid #d9d9d9;
            color: #d9d9d9;
            -webkit-transform-origin: 0 100%;
            transform-origin: 0 100%;
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }
        .weui-actionsheet__menu {
            background-color: #fff;
        }
        .weui-actionsheet__cell {
            position: relative;
            padding: 10px 0;
            text-align: center;
            font-size: 18px;
            height: 30px;
            line-height: 30px;
        }
        .weui-actionsheet__cell a{
        	color: #1a1a1a;
        }
        .color-primary {
            color: #04BE02;
        }
        .color-warning {
            color: #f60;
        }
        .weui-actionsheet__cell:before {
            content: " ";
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            height: 1px;
            border-top: 1px solid #d9d9d9;
            color: #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            -webkit-transform: scaleY(.5);
            transform: scaleY(.5);
        }
        .color-danger, .color-error {
            color: #f6383a;
        }
        .weui-actionsheet__action {
            margin-top: 6px;
            background-color: #fff;
        }

        /* 此页面弹窗修改 */
        .sku-box-shadow{overflow:visible}
        .no-bg{
        	background: #999999;
        }
        .orange-btn:active{
        	background: #999999;
        }
        /* 满减开始 add by 黄新琴 */
        .discount-icon{
            position: absolute;
            left: 0;
            top: 16px;
            width: 30px;
            height: 15px;
            line-height: 15px;
            text-align: center;
            border: 1px solid #FF3232;
            color: #FF3232;
            font-size: 12px;
            border-radius: 2px;
        }
        .disaount-amount {
            font-size: 12px;
            color: #999;
            padding: 6px 20px 6px 30px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .discount-popup{
            display:none;
            position: fixed;
            top:0;
            left: 0;
            z-index: 1000;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        .discount-wraper {
            position: absolute;
            width: 100%;
            left: 0;
            bottom: 0;
            background-color: #fff;
        }
        .discount-title {
            text-align: center;
            color: #000;
            height: 44px;
            line-height: 44px;
            border-bottom: 1px solid #e5e5e5;
        }
        .discount-close {
            float: right;
            width: 15px;
            height: 15px;
            margin-top: 14px;
            margin-right: 15px;
            background: url({{ config('app.source_url') }}shop/images/discount-close.png) no-repeat center;
            background-size: 100% 100%;
        }
        .disaount-list {
            height: 300px;
            padding-top: 5px;
            font-size: 0;
            overflow-y: auto;
        }
        .discount-item{
            display: inline-block;
            box-sizing: border-box;
            height: 30px;
            margin: 15px 0 0 10px;
            text-align: center;
            line-height: 30px;
            border: 1px solid #FF9A40;
            color: #333;
            font-size: 15px;
            border-radius: 15px;
            padding: 0 6px;
        }
        .discount-timer {
            margin: 20px 0 10px 0;
            color: #999;
            font-size: 12px;
            padding-left: 10px;
        }
        /* 满减结束 */
        [v-cloak] { display: none!important; }
        .fade-enter-active, .fade-leave-active {
			transition: opacity .5s;
		}
		.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
			opacity: 0;
        }
        .swiper-pagination-bullet {
            background: #000;
            opacity: .4;
        }
        .swiper-pagination-bullet-active {
            opacity: 1;
            background-color: #000;
        }
        .my2-collect{
        	position: absolute;
        	bottom: 52px;
        	right: 5px;
        }
        /* 分享弹框 */
        .shareDialog .shareDialogBoard {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 100;
            }
        .shareDialog .shareDialogDiv {
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 100;
            text-align: center;
            width:100%;
            }
            .shareDialogDiv Span{
               display:block;
               width:100%;
               height:46px;
               background:#FFF;
               font-size:18px;
               line-height:46px;
               text-align:center;
               color:#000000;
               background-color:#F8F8F8;
            }
            /* 分享给好友弹框 */
            .shareDialog_firend .shareDialogBoard_firend {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 100;
            }
            .shareDialog_firend .shareDialogDiv_firend {
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 100;
            text-align: center;
            width:100%;
            }
            .shareDialogDiv_firend .shre_friend{
            position: fixed;
            top:25%;
            left:10%;
            margin:0 auto;
            text-align:100%;
            }
            .shareDialogDiv_firend .shre_friend img{
            border:0px;
            position:fixed;
            left:1%;
            width:98%;
            top:2%;
            border:0px;
            }
            .shre_friend_top{
                height:80px;
                text-align:left;
                margin:20px 20px 0px 20px;
                font-size:15px;
                border-bottom: 1px solid #ececed;
            }
            .shre_sent{
                display:block;
                padding-bottom:10px
            }
            .shre_friend_top img{
                width:40px;
                height:40px;
                padding:0px 10px 0px 0px;
            }
            .share_friend_body img{
                height:165px;
                width:165px;
                border:1px solid #ececed;
                margin-top:20px;
            }
            .shre_friend_bto{
                position:absolute;
                bottom:0px;
                width:100%;
                border-top:1px solid #ececed;
            }
            .shre_friend_bto button{
                background:none;
                width:49%;
                height:42px;
                border:none;
            }
            /* 生成卡片 */
            .shareDialog_card .shareDialogBoard_card {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 100;
            }
            .share-btn-confirm{
                color:#0bb20c
            }
            .shre_card{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index:100;
            }
            .share_card_body{
            position:absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            }
            .share-img{
            height:450px;
            width:332px;
            outline: none;
            border: none;
            }
            .share_card_body .share_card_waite {
            height:40px;
            width:40px;
            outline: none;
            border: none;
            }
            .share_card_save,.share_close{
            width:100%;
            text-align: center;
            line-height:50px;
            font-size:12px;
            color:#FFFFFF;
            }
            
    </style>
@endsection
@section('main')
    <div class="container wap-goods internal-purchase" id="container" style="min-height: 617px;">
        <input id="wid" type="hidden" value="{{$shop['id']}}">
        <input id="pid" type="hidden" value="{{$product['id']}}" >
        <!-- 广告业添加开始 -->
        <div class="pc_ad_setting" v-if= "productAdPosition == 1">
            <custom-template :lists= "productAd" :host="host" :sid="shopId"></custom-template>
        </div>
        <!-- 广告页添加结束 -->
        <div class="content no-sidebar">
            <div class="content-body">
                <div class="swiper-container">
                	@if($product['is_hexiao'] == 1)
                	<div class="ziti_tips">自提</div>
                	@endif
                    <div class="swiper-wrapper">
                        @forelse($product['productImg'] as $value)
                        <div class="swiper-slide" style="text-align:center"> 
                            <img class="" src="{{ imgUrl($value['img']) }}">
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <!-- add by 魏冬冬 2018-7-16 预售倒计时 -->
                <div class="timeout" v-if="sale_time_flag == 2 && showPreSell">
                    <p class="countdown">距开始还剩：
                        <span v-text="preSell.days"></span>天&nbsp;<span v-text="preSell.hours"></span>时&nbsp;<span v-text="preSell.minutes"></span>分&nbsp;<span v-text="preSell.seconds"></span>秒
                    </p>
                </div>
                <!-- end -->
                <div class="goods-header">
                    <h2 class="title shop_good_title">{{$product['title']}}</h2>
                    <span class="hide js-add-wish js-wish-animate wish-add  font-size-12 tag tag-redf30 pull-right">喜欢</span>
                    
                    <div class="goods-price ">
                        @if(empty($product['is_price_negotiable']))
                          @if(!empty($product['is_vip']))
	                        <div class="current-price abatement">
                            <!-- update by 倪凯嘉 会员折扣样式 2019-10-21 -->
                            <span class="huiyuanjia">￥{{$product['showPrice']}}</span>
                            <span class="huiyuanzhekou" style='vertical-align:middle;'>会员折扣</span><br />
                            <!-- end -->
                            <i class="js-goods-price price">
                              <s style='font-size:12px;color:#bbb;vertical-align:bottom;'>零售价:￥{{$product['price']}}</s>
                            </i>
                            @if( $product['oprice'] > 0 )
                            <span class="original-price">市场价:￥{{$product['oprice']}}</span>
                            @endif
                          </div>
                          @endif
                          @if(empty($product['is_vip']) && !empty($product['bestCardPrice']))
	                        <div class="current-price abatement">
                            <span class="huiyuanjia">￥{{$product['showPrice']}}</span>
                            <span class="member-mark"><span>会员折扣</span><span>￥{{$product['bestCardPrice']}}</span></span><br />
                            @if( $product['oprice'] > 0 )
                            <span class="original-price">市场价:￥{{$product['oprice']}}</span>
                            @endif
                          </div>
                          @endif
                          @if(empty($product['is_vip']) && empty($product['bestCardPrice']))
	                        <div class="current-price abatement">
                            <span class="huiyuanjia">￥{{$product['showPrice']}}</span>
                            @if( $product['oprice'] > 0 )
                            <span class="original-price">市场价:￥{{$product['oprice']}}</span>
                            @endif
                          </div>
                          @endif
                          <span class="btn btn-blue btn-retail hide js-retail-btn">门店有售</span>
                          <!--add by 韩瑜 2018-9-4 收藏按钮-->
                          <span class="collect" @click="collect" v-if="!isFavorite" v-cloak>
                              <img src="{{ config('app.source_url') }}shop/images/nofavorite.png"/>
                              <p class="collect-word">收藏</p>
                          </span>
                            <!-- 崔源 by 2018.11.2 -->
                            <span class="share"  >
                              <img src="{{ config('app.source_url') }}shop/images/_share.png" v-on:click="dialogShow" v-cloak>
                              <p class="share-word">分享</p>
                          </span>         
                          <span class="collect" @click="collectcancel" v-if="isFavorite" v-cloak>
                              <img src="{{ config('app.source_url') }}shop/images/isfavorite.png"/>
                              <p class="collectcancel-word">已收藏</p>
                          </span>
                          <!--end-->
	                        <span class="btn btn-blue btn-retail hide js-retail-btn">门店有售</span>
                        @else
	                        <!--updata by 邓钊 2018-7-13 价格面议-->
                            @if($product['negotiable_type'] == 0)
	                            <div style="color:#E5333A;">价格面议</div>
	                            <!--add by 韩瑜 2018-9-12 面议收藏按钮-->
					                <span class="my-collect my2-collect" @click="collect" v-if="!isFavorite" v-cloak>
					                	<img src="{{ config('app.source_url') }}shop/images/nofavorite.png"/>
					                </span>
					                <span class="my-collect my2-collect" @click="collectcancel" v-if="isFavorite" v-cloak>
					                	<img src="{{ config('app.source_url') }}shop/images/isfavorite.png"/>
					                </span>
					                <!--end-->
                            @elseif($product['negotiable_type'] == 1)
                                <div style='display: flex;justify-content: space-between;'>
                                    <div style="color:#E5333A; font-size: 16px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">咨询电话:{{$product['negotiable_value']}}</div>
                                    <div class="detail-call-wrap">
                                        <a class='btn_a' href="tel:{{$product['negotiable_value']}}">拨打电话</a>
                                        <!--add by 韩瑜 2018-9-12 面议收藏按钮-->
                                        <span class="my-collect" @click="collect" v-if="!isFavorite" v-cloak>
                                            <img src="{{ config('app.source_url') }}shop/images/nofavorite.png"/>
                                        </span>
                                        <span class="my-collect" @click="collectcancel" v-if="isFavorite" v-cloak>
                                            <img src="{{ config('app.source_url') }}shop/images/isfavorite.png"/>
                                        </span>
                                        <!--end-->
                                    </div>
                                </div>
                            @else
                                <div style='display: flex;justify-content: space-between;'>
                                    <div style="color:#E5333A; font-size: 16px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">咨询微信:{{$product['negotiable_value']}}</div>
                                    <div class="detail-call-wrap">
                                        <a class='btn_a' id="copy_btn" href="javascript:;void(0)" data-id="{{$product['negotiable_value']}}">复制微信</a>
                                        <!--add by 韩瑜 2018-9-12 面议收藏按钮-->
                                        <span class="my-collect" @click="collect" v-if="!isFavorite" v-cloak>
                                            <img src="{{ config('app.source_url') }}shop/images/nofavorite.png"/>
                                        </span>
                                        <span class="my-collect" @click="collectcancel" v-if="isFavorite" v-cloak>
                                            <img src="{{ config('app.source_url') }}shop/images/isfavorite.png"/>
                                        </span>
                                    <!--end-->
                                    </div>
                                </div>
                            @endif
                            <!--end-->
                            
                        @endif
                    </div>
                    <hr class="with-margin-l">
                    <div class="stock-detail">
                        <dl>
                            <dt>运费:</dt>
                            <dd class="js-postage-desc" data-postage="¥ 0.00~15.00">¥{{$product['freight_string']}}</dd>
                        </dl>
                        @if(!$product['stock_show'])
                            <dl>
                                <dt>库存:</dt>
                                <dd>{{$product['stock']}}</dd>
                            </dl>
                        @endif
                        <dl>
                            <dt>销量:</dt>
                            <dd>{{$product['sold_num']}}</dd>
                        </dl>
                    </div>
                    <hr class="with-margin-l">
                    {{--<div class="promotion-status goods-promotion js-promotion-status">--}}
                    {{--<div class="promotion-info hide-part-text js-promotion-info">--}}
                    {{--<span class="tag tag-red pull-left">包邮</span>--}}
                    {{--<span class="tag tag-red pull-left">优惠</span>--}}
                    {{--<span>满169元包邮；满299元送299积分，包邮，--}}
                    {{--<a class="link" href="https://h5.youzan.com/v2/showcase/goods?alias=2xf4su4y8bxde">送【17.03会员日】满299元赠品赠品</a></span>--}}
                    {{--</div>--}}
                    {{--<span class="js-arrow-down arrow"></span>--}}
                    {{--</div>--}}
                </div>
                @if($product['sku_flag'] == 1)
                    <div class='selectSku'>
                        <div>选择规格</div>
                        <div class='skuArrow'></div>
                    </div>
                @endif
                <div class="js-store-info">
                    <div class="block block-list goods-store">
                        @if($discount)
                        <div class="custom-store block-item J_discount">
                            <div>
                                <span class="discount-icon">满减</span>
                                <p class="disaount-amount">{{$discount['str']}}</p>
                            </div>
                        </div>
                        @endif
                        <div class="custom-store block-item ">
                            <a class="custom-store-link clearfix" href="/shop/index/{{session('wid')}}">
                                <div class="custom-store-img"></div>
                                <div class="custom-store-name">{{$shop['shop_name']}}</div>
                                <span class="custom-store-enter"></span>
                                <span class="go-custom">进入店铺</span>
                            </a>

                            @if(isset($distribute['tag']) && $distribute['tag']==1)
                            <a class="custom-store-link  eye-a" href="/shop/index/{{$shop['id']}}">
                                <div class="custom-store-img eye-img"></div>
                                <div class="custom-store-name eye-caifu" style="max-width: 600px;width: 85%;height: auto;">
                                    <p class="eye-p0">财富：</p>
                                    <div class="eye-price eye-p0">
                                        <div>
                                            一级分销员最多赚：<span style="color:#F72F37;" v-text="(rate*maxPrice/100).toFixed(2) + '元'"></span>
                                        </div>
                                        <div>
                                            二级分销员最多赚：<span style="color:#F72F37;" v-text="(rateSec*maxPrice/100).toFixed(2) + '元'"></span>
                                        </div>
                                    </div>
                                    <!-- <p class="eye-p0">财富：{{$distribute['price']}}元</p> -->
                                    <!-- <p style="font-size:12px;color:#ccc;width: 100%;white-space: pre-wrap;word-break: break-all;">只要你分享了这款产品，你的小伙伴购买了，你将得到最多{{$distribute['price']}}元的奖励哦！</p> -->
                                </div>
                                <span class="custom-store-enter eye-enter"></span>
                            </a>
                            @endif
                        </div>
                        @if($shop['is_ziti_on'] == 1 && $product['is_hexiao'] == 1)
                        <div class="custom-store block-item ">
                            <a class="custom-store-link clearfix" href="/shop/store/shopZiti">
                                <div class="offline-store-img"></div>
                                <div class="custom-store-name">自提点</div>
                                <span class="custom-store-enter"></span>
                            </a>
                        </div>
                        @endif
                        <div class="renzheng block-item">
                            <!-- <span class="js-rz-item-alert rz-item" data-type="team_certificate_company">
                                <span class="rz-name font-size-12 c-gray-darker">企业认证</span>
                            </span>
                            <span class="js-rz-item-alert rz-item" data-type="is_secured_transactions">
                                <span class="rz-name font-size-12 c-gray-darker">担保交易</span>
                            </span> -->
                            @if($__storeNumber__>0)
                            <span class="js-rz-item-alert rz-item" data-type="is_secured_transactions">
                                <span class="rz-name font-size-12 c-gray-darker">线下门店</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <a class="js-package-buy-block hide"></a>
                <div class="js-detail-container" style="margin-top: 10px;">
                    <div class="js-tabber-container goods-detail tab1">
                        <div class="js-tabber tabber tabber-n2 clearfix orange">
                            @if($reqFrom !== 'aliapp')
                            <button data-type="goods" class="active">商品详情</button>
                            <button data-type="reviews" class="">累计评价</button>
                            @else
                            <button data-type="goods" style="width:100%">商品详情</button>
                            @endif
                        </div>
                        <div class="js-tabber-content">
                            <div class="js-part js-trade-review-list trade-review-list hide" data-type="reviews">
                                <div class="js-review-tabber review-rate-tabber tabber tabber-n4 clearfix">
                                    <span class="item">
                                        <button class="js-rate-all rate js-cancal-disable-link active" data-reviewtype="all" data-rate="0">全部({{$number['all']}})</button></span>
                                    <span class="item">
                                        <button class="js-rate-good js-cancal-disable-link" data-reviewtype="good" data-rate="30">好评({{$number['good']}})</button></span>
                                    <span class="item">
                                        <button class="js-rate-middle js-cancal-disable-link" data-reviewtype="middle" data-rate="20">中评({{$number['middle']}})</button></span>
                                    <span class="item">
                                        <button class="js-rate-bad js-cancal-disable-link" data-reviewtype="bad" data-rate="10">差评({{$number['bad']}})</button></span>
                                </div>
                                <div class="js-review-tabber-content block block-list">
                                    <div class="js-review-report-container report-detail-container block-item no-border hide pd0"></div>
                                    <div class="js-review-part review-detail-container" data-reviewtype="all">
                                        <div class="js-list b-list">
                                            @foreach($evaluate['data'] as $val)
                                                @if($val['is_hiden']==0)
                                                    <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                        <div class="name-card">
                                                            <div class="thumb">
                                                                <img class="test-lazyload" src="{{$val['member']['headimgurl']}}" alt="">
                                                            </div>
                                                            <div class="detail">
                                                                <h3>{{$val['member']['nickname']}}</h3>
                                                                <p class="font-size-12">{{$val['created_at']}}</p></div>
                                                        </div>
                                                        <div class="item-detail font-size-14 c-gray-darker">
                                                            <p>{{$val['content']}}</p>
                                                        </div>
                                                        <!-- 商家回复 by 崔源 2018.11.20 -->
                                                        @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                        <div class="other">
                                                            <span class="from">购买自：本店</span>
                                                            <p class="pull-right">
                                                                <span class="js-like like-item ">
                                                                    <i class="like"></i>
                                                                    <i class="js-like-num">{{$val['agree_num']}}</i></span>
                                                                        <span class="js-add-comment">
                                                                    <i class="comment"></i>
                                                                    <i class="js-comment-num"></i>
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </a>
                                                @else
                                                    <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                        <div class="name-card">
                                                            <div class="thumb">
                                                                <span class="center font-size-18 c-orange">匿</span>
                                                            </div>
                                                            <div class="detail">
                                                                <h3>匿名</h3>
                                                                <p class="font-size-12">{{$val['created_at']}}</p>
                                                            </div>
                                                        </div>
                                                        <div class="item-detail font-size-14 c-gray-darker">
                                                            <p>{{$val['content']}}</p>
                                                        </div>
                                                        <!-- 商家回复 by 崔源 2018.11.20 -->
                                                        @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                        <div class="other">
                                                            <span class="from">购买自：本店</span>
                                                            <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">0</i></span>
                                                                <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                            </p>
                                                        </div>
                                                    </a>
                                                @endif
                                            @endforeach
                                            @if(!empty($evaluate['data']))
                                                <div class="list-finished more" data-status="0">加载更多</div>
                                            @endif
                                        </div>
                                        @if(empty($evaluate['data']))
                                            <div class="list-finished">暂无评论</div>
                                        @endif
                                    </div>
                                    <div class="js-review-part review-detail-container hide" data-reviewtype="good">
                                        @foreach($data['good']['data'] as $val)
                                            @if($val['is_hiden']==0)
                                                <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                    <div class="name-card">
                                                        <div class="thumb">
                                                            <img class="test-lazyload" src="{{$val['member']['headimgurl']}}" alt=""></div>
                                                        <div class="detail">
                                                            <h3>{{$val['member']['nickname']}}</h3>
                                                            <p class="font-size-12">{{$val['created_at']}}</p></div>
                                                    </div>
                                                    <div class="item-detail font-size-14 c-gray-darker">
                                                        <p>{{$val['content']}}</p>
                                                    </div>
                                                    <!-- 好评商家回复 by 崔源 2018.11.20 -->
                                                         @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                    <div class="other">
                                                        <span class="from">购买自：本店</span>
                                                        <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">{{$val['agree_num']}}</i></span>
                                                            <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                        </p>
                                                    </div>
                                                </a>
                                            @else
                                                <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                    <div class="name-card">
                                                        <div class="thumb">
                                                            <span class="center font-size-18 c-orange">匿</span>
                                                        </div>
                                                        <div class="detail">
                                                            <h3>匿名</h3>
                                                            <p class="font-size-12">{{$val['created_at']}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="item-detail font-size-14 c-gray-darker">
                                                        <p>{{$val['content']}}</p>
                                                    </div>
                                                    <!-- 好评匿名商家回复 by 崔源 2018.11.20 -->
                                                    @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                    <div class="other">
                                                        <span class="from">购买自：本店</span>
                                                        <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">0</i></span>
                                                            <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                        </p>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach
                                            @if(!empty($data['good']['data']))
                                                <div class="list-finished more" data-status="1">加载更多</div>
                                            @endif
                                            @if(empty($data['good']['data']))
                                                <div class="list-finished">暂无评论</div>
                                            @endif
                                    </div>
                                    <div class="js-review-part review-detail-container hide" data-reviewtype="middle">
                                        @foreach($data['middle']['data'] as $val)
                                            @if($val['is_hiden']==0)
                                                <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                    <div class="name-card">
                                                        <div class="thumb">
                                                            <img class="test-lazyload" src="{{$val['member']['headimgurl']}}" alt="">
                                                        </div>
                                                        <div class="detail">
                                                            <h3>{{$val['member']['nickname']}}</h3>
                                                            <p class="font-size-12">{{$val['created_at']}}</p></div>
                                                    </div>
                                                    <div class="item-detail font-size-14 c-gray-darker">
                                                        <p>{{$val['content']}}</p>
                                                    </div>
                                                    <!-- 中评商家回复 by 崔源 2018.11.20 -->
                                                         @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                    <div class="other">
                                                        <span class="from">购买自：本店</span>
                                                        <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">{{$val['agree_num']}}</i></span>
                                                            <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                        </p>
                                                    </div>
                                                </a>
                                            @else
                                                <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                    <div class="name-card">
                                                        <div class="thumb">
                                                            <span class="center font-size-18 c-orange">匿</span>
                                                        </div>
                                                        <div class="detail">
                                                            <h3>匿名</h3>
                                                            <p class="font-size-12">{{$val['created_at']}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="item-detail font-size-14 c-gray-darker">
                                                        <p>{{$val['content']}}</p>
                                                    </div>
                                                    <!-- 中评匿名商家回复 by 崔源 2018.11.20 -->
                                                        @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                    <div class="other">
                                                        <span class="from">购买自：本店</span>
                                                        <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">0</i></span>
                                                            <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                        </p>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach
                                            @if(!empty($data['middle']['data']))
                                                <div class="list-finished more" data-status="2">加载更多</div>
                                            @endif
                                            @if(empty($data['middle']['data']))
                                                <div class="list-finished">暂无评论</div>
                                            @endif
                                    </div>
                                    <div class="js-review-part review-detail-container hide" data-reviewtype="bad">
                                        @foreach($data['bad']['data'] as $val)
                                            @if($val['is_hiden']==0)
                                                <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                    <div class="name-card">
                                                        <div class="thumb">
                                                            <img class="test-lazyload" src="{{$val['member']['headimgurl']}}" alt="">
                                                        </div>
                                                        <div class="detail">
                                                            <h3>{{$val['member']['nickname']}}</h3>
                                                            <p class="font-size-12">{{$val['created_at']}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="item-detail font-size-14 c-gray-darker">
                                                        <p>{{$val['content']}}</p>
                                                    </div>
                                                    <!-- 差评商家回复 by 崔源 2018.11.20 -->
                                                        @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                    <div class="other">
                                                        <span class="from">购买自：本店</span>
                                                        <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">{{$val['agree_num']}}</i></span>
                                                            <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                        </p>
                                                    </div>
                                                </a>
                                            @else
                                                <a href="/shop/product/evaluateDetail/{{$shop['id']}}/?eid={{$val['id']}}" class="js-review-item review-item block-item">
                                                    <div class="name-card">
                                                        <div class="thumb">
                                                            <span class="center font-size-18 c-orange">匿</span>
                                                        </div>
                                                        <div class="detail">
                                                            <h3>匿名</h3>
                                                            <p class="font-size-12">{{$val['created_at']}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="item-detail font-size-14 c-gray-darker">
                                                        <p>{{$val['content']}}</p>
                                                    </div>
                                                    <!-- 差评商家回复 by 崔源 2018.11.20 -->
                                                        @if($val['seller_reply'])
                                                        <div class="business-reply">
                                                            <span>【商家回复】{{$val['seller_reply']}}</span>
                                                        </div>
                                                        @endif
                                                    <div class="other">
                                                        <span class="from">购买自：本店</span>
                                                        <p class="pull-right">
                                                        <span class="js-like like-item ">
                                                            <i class="like"></i>
                                                            <i class="js-like-num">0</i></span>
                                                            <span class="js-add-comment">
                                                            <i class="comment"></i>
                                                            <i class="js-comment-num"></i>
                                                        </span>
                                                        </p>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach

                                        @if(!empty($data['bad']['data']))
                                            <div class="list-finished more" data-status="3">加载更多</div>
                                        @endif
                                        @if(empty($data['bad']['data']))
                                            <div class="list-finished">暂无评论</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="js-part js-goods-detail goods-tabber-c" data-type="goods">
                                <!-- 商品的富文本  自定义组件的添加开始 -->
                                <div class="pc_product_setting">
                                    <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
                                </div>
                                <!-- 商品的富文本  自定义组件的添加结束 -->
                                <!-- 广告业添加开始 -->
                                <div class="pc_ad_setting" v-if= "productAdPosition == 2">
                                    <custom-template :lists= "productAd" :host="host" :sid="shopId"></custom-template>
                                </div>
                                <!-- 广告页添加结束 -->
                            </div>
                        </div>

                    </div>
                    <div class="tab2" style="display: none">
                        <div class="custom-store block-item">
                            <a class="custom-store-link clearfix" href="/shop/product/showTemplateDetail/{{session('wid')}}/{{$product['id']}}">
                                <div class="custom-store-name" style="padding-left: 0;">查看商品详情</div>
                                <span class="custom-store-enter"></span>
                            </a>
                        </div>
                        <div class="custom-store block-item" style="border-bottom: 1px solid #eee;">
                            <a class="custom-store-link clearfix" href="/shop/index/{{session('wid')}}">
                                <div class="custom-store-name" style="padding-left: 0;">查看累计评价</div>
                                <span class="custom-store-enter"></span>
                            </a>
                            <div class="js-review-tabber-content block block-list" style="border: 0;">
                                <div class="js-review-report-container report-detail-container block-item no-border hide pd0"></div>
                                <div class="js-review-part review-detail-container" data-reviewtype="all">
                                    <div class="js-list b-list" v-cloak>
                                        <a v-for="list in commentList" :href="'/shop/product/evaluateDetail/'+wid+'?eid='+list['id']" class="js-review-item review-item block-item">
                                            <div class="name-card">
                                                <div v-if= "list['is_hiden'] != 0" class="thumb">
                                                    <span class="center font-size-18 c-orange">匿</span>
                                                </div>
                                                <div v-else class="thumb">
                                                    <img class="test-lazyload" :src="list['member']['headimgurl']" alt="">
                                                </div>
                                                <div class="detail">
                                                    <h3 v-if= "list['is_hiden'] != 0">匿名</h3>
                                                    <h3 v-else>[[list.member.nickname]]</h3>
                                                    <p class="font-size-12">[[list.created_at]]</p>
                                                </div>
                                            </div>
                                            <div class="item-detail font-size-14 c-gray-darker">
                                                <p>[[list.content]]</p>
                                            </div>
                                            <div class="other">
                                                <span class="from">购买自：本店</span>
                                                <p class="pull-right">
                                            <span class="js-like like-item ">
                                                <i class="like"></i>
                                                <i class="js-like-num">[[list.agree_num]]</i></span>
                                                    <span class="js-add-comment">
                                                <i class="comment"></i>
                                                <i class="js-comment-num"></i>
                                            </span>
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="js-bottom-opts js-footer-auto-ele bottom-fix">
	                <div class="responsive-wrapper">
	                	@if(empty($product['is_price_negotiable']))
		                    <div class="@if(config('app.chat_url')) mini-btn-3-1 @else  mini-btn-2-1 @endif">
                                <a href="/shop/index/{{$shop['id']}}" class="new-btn" style="border-right: 1px #e6e6e6 solid;box-sizing: border-box;border-top: 1px #e6e6e6 solid;">
                                    <img src="{{ config('app.source_url') }}shop/images/go_index1.png" class="bottom_cart"/>
                                </a>
                                @if(config('app.chat_url'))
                                @if($reqFrom == 'aliapp')
                                <a href="{{config('app.chat_url')}}/zfb/kefu?productName={{urlencode($product['title'])}}&productImg={{ imgUrl($product['img']) }}&productPrice={{$product['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$product['id'].'&type=4')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{urlencode($shop['shop_name'])}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}&timestp={{time()}}" class="js-im-icon new-btn service">
                                @else
                                <a href="{{config('app.chat_url')}}/#/kefu?productName={{urlencode($product['title'])}}&productImg={{ imgUrl($product['img']) }}&productPrice={{$product['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$product['id'].'&type=4')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{urlencode($shop['shop_name'])}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}&timestp={{time()}}" class="js-im-icon new-btn service">
                                @endif
                                    @if($reqFrom == 'aliapp')
                                    <i class="iconfont icon-service icon_ser_img icon_ser_img_aliapp"></i>
                                    @else
                                    <i class="iconfont icon-service icon_ser_img"></i>
                                    @endif
                                    <span class="news-num hide"></span>
                                    {{--<span class="desc">客服</span>--}}
                                </a>
		                        @endif
		                        <a id="global-cart" href="javascript:void(0);" class="new-btn buy-cart" data-id="{{$shop['id']}}">
		                            <!-- <i class="iconfont icon-shopping-cart icon_img_cart"></i>
		                            <span class="desc">购物车</span> -->
                                    <img src="{{ config('app.source_url') }}shop/images/cart@2x.png?t=123" class="bottom_cart"/>
		                            <span class="goods-num">{{$cartNum}}</span>
		                        </a>
		                    </div>
		                    <div class="big-btn-2-1">
                                @if($product['wholesale_flag'] == 0 && $product['cam_id'] <= 0)
		                        <a href="javascript:;" class="big-btn orange-btn vice-btn @if($product['is_hexiao'] == 0) js-add-cart @else no-bg @endif">加入购物车
                               </a>
                                @endif
		                        <a href="javascript:void(0);" class="js-buy-it big-btn red-btn main-btn" v-if="!showPreSell">立即购买</a>
                                <a href="javascript:void(0);" class="big-btn gray_btn main-btn" v-cloak v-if="showPreSell">立即购买</a>
		                    </div>
	                    @else
		                    <!--价格面议客服-->
                            @if(config('app.chat_url'))
                            @if($reqFrom == 'aliapp')
                            <a href="{{config('app.chat_url')}}/zfb/kefu?productName={{urlencode($product['title'])}}&productImg={{ imgUrl($product['img']) }}&productPrice={{$product['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$product['id'].'&type=4')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}&timestp={{time()}}" style="display: flex;justify-content: center;align-items: center;padding: 5px 0;height: 40px;" class="js-im-icon">
                            @else
			                <a href="{{config('app.chat_url')}}/#/kefu?productName={{urlencode($product['title'])}}&productImg={{ imgUrl($product['img']) }}&productPrice={{$product['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$product['id'].'&type=4')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}&timestp={{time()}}" style="display: flex;justify-content: center;align-items: center;padding: 5px 0;height: 40px;" class="js-im-icon">
                            @endif
                                <div class="face-product">
                                    <img src="{{ config('app.source_url') }}shop/images/previewblade_kefu.png" alt="" style="width:30px;height: 30px;"/>
                                    <i style="margin-left: 3px;font-size: 16px;">咨询客服</i>
                                    <span class="news-num hide"></span>
                                </div>
                            </a>
                            @endif
	               		@endif
	                </div>
            </div>
            @if(!empty($more))
            <div class="js-recommend">
                <div class="u-like-title">
                    <div class="u-like-line"></div> 
                    <div class="u-like-icon"></div> 
                    <p class="u-like-tips">为您推荐</p> 
                    <div class="u-like-line"></div>
                </div>
                <div class="js-recommend-goods-list">
                    <ul class="js-goods-list sc-goods-list pic clearfix size-1 " data-size="1" data-showtype="card" style="visibility: visible;">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        @forelse($more as $value)
                            <li class="js-goods-card goods-card small-pic card ">
                                <a style='border: none;' href="/shop/product/detail/{{$shop['id']}}/{{$value['id']}}" class="js-goods link clearfix"  data-goods-id="330500316" title="{{$value['title']}}">
                                    <div class="photo-block" data-width="0" data-height="0">
                                        <img class="goods-photo js-goods-lazy test-lazyload" src="{{ imgUrl($value['img']) }}">
                                    </div>
                                    <div class="info clearfix info-title info-price btn0">
                                        <p class=" goods-title ">{{$value['title']}}</p>
                                        @if(empty($value['is_price_negotiable']))
                                            <p class="goods-price">
                                                <em>￥{{$value['price']}}</em>
                                            </p>
                                            <p class="goods-price-taobao ">￥{{$value['oprice']}}</p>
                                        @else
                                            <p class="goods-price">
                                                <em>价格面议</em>
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            </li>
                            @endforeach
                    </ul>
                </div>
                <p class="center" style="margin: 10px 0 20px;">
                    <a href="/shop/index/{{$shop['id']}}" class="center btn btn-white btn-xsmall font-size-14 " style="padding:8px 26px;">进店逛逛&gt;</a>
                </p>
            </div>
            @endif
        </div>
        <div id="shop-nav"></div>
        <!-- 分享弹窗 by 崔源 2018.11.5-->
        <div v-show='shareDialogShow' class='shareDialog' v-on:click='shareCancle' v-cloak>
	    	<div class='shareDialogBoard' v-cloak></div>
		    <div class='shareDialogDiv' v-cloak>
                <span class="shareDialogSpan_friend" v-on:click='share_friend' v-cloak>分享给好友</span>
                <span class="shareDialogSpan_card"   v-on:click='share_card' v-cloak>生成卡片保存分享</span>
                <span class="shareDialogSpan_cancle" v-on:click='shareCancle' v-cloak>取消</span>
			</div>
		</div>
        <!-- 分享弹窗end-->
            
        <!-- 分享给好友弹窗 -->
        <div v-show='shareDialog_friend' class='shareDialog_firend' v-cloak>
            <div class='shareDialogBoard_firend'  v-on:click='shareCancle_friend' v-cloak></div>
            <div class="shareDialogDiv_firend" v-cloak>
                <div class="shre_friend" v-cloak>
                    <div class="shre_box" v-cloak>
                        <img src="{{ config('app.source_url') }}shop/images/share.png" alt="" v-cloak>
                    </div>
                    <!-- <div class="close_share" v-on:click="bgClick">X</div> -->
                </div>
            </div>
        </div>
        <!-- 分享给好友弹窗end -->
            
        <!-- 生成卡片 -->
        <div v-show='shareDialog_card' class='shareDialog_card' v-cloak>
            <div class='shareDialogBoard_card'  v-on:click='shareCancle_card' v-cloak></div>
                <div class="shre_card" v-on:click='shareCancle_card' v-cloak>
                    <div class="share_card_body" v-cloak>
                      <img  class="share_card_waite" v-show="state_wait"  src="{{ config('app.source_url') }}shop/images/3t3yloading.gif">
                      <img  class="share-img" v-if="state_end" :src='cardurl' alt="" @click.stop v-cloak>
                      <div  class="share_card_save"  v-if="state_end"  @click.stop v-cloak>长按图片保存</div>
                      <div class="share_close" v-on:click='shareCancle_card' ><img class="close_share_img" style="width:40px;height:40px;" src="{{ config('app.source_url') }}shop/images/x.png" alt=""></div>
                    </div>
                </div>
        </div>
        <!-- 生成卡片end -->
        </div>
    </div>
    <div id="shop-nav"></div>
    
    <div class="search-bar" style="display:none;">
        <form class="search-form" action="/v2/search" method="GET">
            <input type="search" class="search-input" placeholder="搜索商品" name="q" value="">
            <input type="hidden" name="kdt_id" value="5174760">
            <a class="js-search-cancel search-cancel" href="javascript:;">取消</a>
            <span class="search-icon"></span>
            <span class="close-icon hide"></span>
        </form>
        <div class="history-wrap center">
            <ul class="history-list search-recom-list js-history-list clearfix"></ul>
            <a class="tag tag-clear js-tag-clear c-gray-darker hide" href="javascript:;">清除历史搜索</a></div>
    </div>
    <div id="right-icon" class="js-right-icon hide">
        <div class="js-right-icon-container right-icon-container clearfix">
            <a class="js-show-more-btn icon show-more-btn hide"></a>
        </div>
    </div>
    <!--积分弹窗-->
    <div class="jifen_tc">
    	<div><img class="test-lazyload" src="{{ config('app.source_url') }}shop/images/jifentc.png" width="53px" height="55px" /></div>
    	<p>积分+<span>5</span></p>
    </div>
     <!-- 客服弹窗 -->
    <div class="weui-mask weui-actions_mask weui-mask--visible hide"></div>
    <div class="weui-actionsheet  weui-actionsheet_toggle hide">
        <div class="weui-actionsheet__title">选择操作</div>
        <div class="weui-actionsheet__menu">
            <div class="weui-actionsheet__cell color-primary  changeColor">
                <a href="http://wpa.qq.com/msgrd?v=3&uin={{$product['qq']}}&site=qq&menu=yes">联系客服QQ</a>
            </div>
            <div class="weui-actionsheet__cell color-warning changeColor">
                <a href="tel:{{$product['telphone']}}">联系客服电话</a>
            </div>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell weui-actionsheet_cancel color-primary" @click="hideKeFu">取消</div>
        </div>
    </div>
    <!-- 客服弹窗 -->
    <!-- 满减弹窗 -->
    <div class="discount-popup">
        <div class="discount-wraper">
            <div class="discount-title">
                {{$discount['title']??''}}
                <span class="discount-close"></span>
            </div>
            <div class="disaount-list">
                <div>
                    @forelse($discount['detail']??[] as $val)
                    <div class="discount-item">{{$val}}</div>
                        @endforeach
                </div>
                <p class="discount-timer">活动时间：{{$discount['start_time']??''}} 至 {{$discount['end_time']??''}} </p>
            </div>
        </div>
    </div>
    <!-- 满减弹窗 -->
    <div class='tipshow'>
        <div>复制成功</div>
    </div>
    <!--add by 韩瑜 2018-9-6 收藏提示-->
	<div class='collecttip iscollecttip'>
        <div >收藏成功</div>
    </div>
    <div class='collecttip nocollecttip'>
        <div>取消成功</div>
    </div>
    <!--end-->
    @include('shop.common.footer')
@endsection
@section('page_js')
    <!-- 加入购物车弹窗 -->
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/socket.io.js"></script>
    <script type="text/javascript">
        var host = "{{config('app.url')}}";
        var videoUrl = "{{ videoUrl() }}";
        var shop_id = "{{$shop['id']}}";
        var product = {!! json_encode($product) !!};
        var micro_page_notice= {!! $micro_page_notice !!};//公共广告
        var productModel = {!! json_encode($template) !!};   //商品页模板
        var source = '{{ imgUrl() }}';
        var wid = $("#wid").val();
        var _host = "{{ config('app.source_url') }}";
        var cartNum = {{$cartNum}};
        var imgUrl = "{{ imgUrl() }}";
        var sku = {!! $sku !!};//规格字段
        var isBind = {{$__isBind__}};
        var rate = {{$rate}};
        var rateSec = {{$rateSec}};
        if(cartNum == 0){$(".goods-num").hide()}
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-lazyload.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/product_detail.js"></script>
    <script type="text/javascript">
        //是批发价的时候
        var big_btn_array = $('.big-btn')
        if(big_btn_array.length == 1){
            big_btn_array.css({'width':'100%'})
        }

        //商品简介
        var proIntro = product["summary"]?product["summary"]:'移动电商，会搜云享-{{ $title or '' }}';
        // 懒加载
    	$('.test-lazyload').picLazyLoad({
		    threshold: 200,
			effect : "fadeIn"
		});
    	//微信分享
  		$(function(){
            // 初始化消息数量socket
            tool.initSocket({
                shopId:"{{session('wid')}}",
                userId:"{{session('mid')}}",
                joinWay:'',
                sign:"{{md5(session('wid').session('mid').'huisou')}}",
                msgCallBack:function(res) {
                    if (res > 0 && res <= 99) {
                        $('.news-num').html(res).removeClass('hide');
                    } else if (res > 99) {
                        $('.news-num').html('99+').addClass('big-num').removeClass('hide');
                    } else {
                        $('.news-num').html('').removeClass('big-num').addClass('hide');
                    }
                }
            })	  	
  			var $jifen_tc = $('.jifen_tc');	
  			function jifentcShow(data){
	    		$jifen_tc.find('p').find('span').html(data);
				$jifen_tc.show();
	    	}	
	    	function jifenAjax(){
	    		$.ajax({
					type:"get",
					data:{},
					url:"/shop/point/addShareRecord/"+wid,
					dataType:"json",
					headers:{
						'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
					},
					success:function(data){
						if(data.errCode == 3 || data.errCode == 1 || data.errCode == 2){
							return false;
						}else{
							jifentcShow(data.data);
							setTimeout(function(){
								$jifen_tc.hide();
							},3000)
						}
					},
					error:function(data){
						tool.tip(data.errMsg);
					}
				});
	    	}
		})
    </script>
    <script type="text/javascript">
    	$('.changeColor').on('touchstart',function(){
    		$(this).css('background','#f8f8f8')
    	})
    	$('.changeColor').on('touchend',function(){
    		$(this).css('background','#fff')
        })
    </script>
@endsection
