@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/goods_62d5db3e3f0f2435e941566b8d882e5d.css"> 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/group_oi83u2yq.css">  
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css"> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <style type="text/css">
		.js-footer{margin-bottom:51px;} 
		.swiper-container {
			width:100%;height:375px;
		}
		.swiper-slide{text-align:center}
		.swiper-slide img{width:100%;height:375px;}
		.goods-activity .goods-price .price-title {z-index: 2;} 
		[v-cloak] { display: none!important; }
    </style>
@endsection
@section('main')
    <div class="container wap-goods internal-purchase" id="container" style="min-height: 617px;">
    <div class="header">
    </div>
    <div class="content no-sidebar">
        <div class="content-body">
            <div class="swiper-container">
			    <div class="swiper-wrapper">
                    @forelse($product['product']['productImg'] as $val)
                        <div class="swiper-slide">
                            <img src="{{ imgUrl($val['img']) }}"/>
                        </div> 
                    @endforeach
			    </div>
			    <!-- 如果需要分页器 -->
			    <div class="swiper-pagination"></div>
			</div> 
            <div class="goods-header goods-activity clearfix">
                <div class="goods-price clearfix">
                    <div class="activity-price current-price">
                        <span class="price-title">
                            {{$seckill['tag']}}
                        </span>
                        <span class="js-goods-price price">
                            <div class="price-wrapper">
                                <span>
                                    ¥
                                </span> 
                                <i>
                                    {{$seckill['price_range']}}
                                </i>
                            </div>
                        </span>
                    </div>
                    <div class="original-price">
                        {{$seckill['oprice_range']}}
                    </div>
                    <div class="overview-countdown">
                        <div class="countdown-title">
                            距结束仅剩
                        </div>
                        <div class="js-time-count-down countdown">
                        	<span class="js-span-d">
                                
                            </span>
                        	<i class="js-i-d" style="font-size:12px;display: none;">天</i> 
                            <span class="js-span-h">
                                
                            </span>
                            时
                            <span class="js-span-m">
                                
                            </span>
                            分
                            <span class="js-span-s">
                                
                            </span>
                            秒
                        </div>
                    </div>
                </div>
                <div class="goods-title">
                    <h2 class="title" style="width: 82%;">
                        {{$seckill['title']}}
                    </h2>
                    <!--add by 韩瑜 2018-9-4 收藏按钮-->
	                <span class="collect" @click="collect" v-if="!isFavorite" v-cloak>
	                	<img src="{{ config('app.source_url') }}shop/images/nofavorite.png"/>
	                </span>
	                <span class="collect" @click="collectcancel" v-if="isFavorite" v-cloak>
	                	<img src="{{ config('app.source_url') }}shop/images/isfavorite.png"/>
	                </span>
	                <!--end-->
                </div>
                
                <div class="stock-detail">
                    <dl>
                        <dt>运费:</dt>
                        <dd class="js-postage-desc" data-postage="免运费">{{$product['defaultFreight']}}</dd>
                    </dl>
                    <dl>
                        <dt>剩余:</dt>
                        <dd>{{$seckill['stock_sum']}}</dd>
                    </dl> 
                </div>
            </div>
            @if($product['product']['sku_flag'] == 1)
                <div class='selectSku'>
                    <div>选择规格</div>
                    <div class='skuArrow'></div>
                </div>
            @endif
            <div class="js-store-info">
                <div class="block block-list goods-store">
                    <div class="custom-store block-item ">
                        <a class="custom-store-link clearfix" href="/shop/index/{{session('wid')}}">
                            <div class="custom-store-img">
                            </div>
                            <div class="custom-store-name">
                                {{$__weixin['shop_name']}}
                            </div>
                            <span class="custom-store-enter"></span>
                            <span class="go-custom">进入店铺</span>
                        </a>
                    </div>
                    @if($__storeNumber__>0)
                    <a class="offline-store block-item js-retail-store hide" href="/shop/store/getStore">
                        <span class="offline-store-img">
                        </span>
                        <span class="offline-store-name">
                            线下门店
                        </span>
                        <div class="offline-store-branch js-retail-store-name">
                        </div>
                    </a>
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
            <div class="js-detail-container" style="margin-top: 10px;">
                <div class="js-tabber-container goods-detail">
                    <div class="js-tabber tabber tabber-n2 clearfix orange">
                        <button data-type="goods" class="active">
                            商品详情
                        </button>
                        <button data-type="reviews">
                            累计评价
                        </button>
                    </div>
                    <div class="js-tabber-content">
                        <div class="js-part js-trade-review-list trade-review-list hide" data-type="reviews">
                            <div class="js-review-tabber review-rate-tabber tabber tabber-n4 clearfix">
                                <span class="item">
                                    <button class="js-rate-all rate js-cancal-disable-link active" data-reviewtype="all"
                                    data-rate="0">
                                        全部
                                    </button>
                                </span>
                                <span class="item">
                                    <button class="js-rate-good js-cancal-disable-link" data-reviewtype="good"
                                    data-rate="30">
                                        好评
                                    </button>
                                </span>
                                <span class="item">
                                    <button class="js-rate-middle js-cancal-disable-link" data-reviewtype="middle"
                                    data-rate="20">
                                        中评
                                    </button>
                                </span>
                                <span class="item">
                                    <button class="js-rate-bad js-cancal-disable-link" data-reviewtype="bad"
                                    data-rate="10">
                                        差评
                                    </button>
                                </span>
                            </div>
                            <div class="js-review-tabber-content block block-list">
                                <div class="js-review-avatar-container review-profile block-item">
                                    <div class="js-review-avatar review-avatar-container">
                                        <p class="loading">
                                        </p>
                                    </div>
                                </div>
                                <div class="js-review-report-container report-detail-container block-item no-border hide pd0">
                                </div>
                                <div class="js-review-part review-detail-container" data-reviewtype="all">
                                	<div class="js-list b-list"> 
                                         <!-- 这里是评论内容  和商品详情页一样进行修改时可去复制 --> 
                                    </div>
                                	<div class="list-finished">all暂无评论</div>
                                </div>
                                <div class="js-review-part review-detail-container hide" data-reviewtype="good">
                                	<div class="js-list b-list"></div>
                                	<div class="list-finished">good暂无评论</div>
                                </div>
                                <div class="js-review-part review-detail-container hide" data-reviewtype="middle">
                                	<div class="js-list b-list"></div>
                                	<div class="list-finished">middle暂无评论</div>
                                </div>
                                <div class="js-review-part review-detail-container hide" data-reviewtype="bad">
                                	<div class="js-list b-list"></div>
                                	<div class="list-finished">bad暂无评论</div>
                                </div>
                            </div>
                        </div>
                        <div class="js-part js-goods-detail goods-tabber-c" data-type="goods">  
                            <!-- 商品的富文本  自定义组件的添加开始 -->
                            <div class="pc_product_setting">
                                <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
                            </div>
                            <!-- 商品的富文本  自定义组件的添加结束 --> 
                        </div>
                    </div>
                </div>
            </div>
            @if (!empty($product['more']))
            <div class="groupon-goods-list-container">
                <div class="u-like-title">
                    <div class="u-like-line"></div> 
                    <div class="u-like-icon"></div> 
                    <p class="u-like-tips">为您推荐</p> 
                    <div class="u-like-line"></div>
                </div>
                <div class="js-waterfall">
                    <div class="js-list groupon-goods-list clearfix">
                        @forelse($product['more'] as $val)
                        <div class="name-card-wrap">
                            <a style='border: none' class="name-card-vertical" href="/shop/product/detail/{{$product['shop']['id']}}/{{$val['id']}}">
                                <div class="thumb-wrap">
                                    <img class="thumb lazyload" src="{{ imgUrl($val['img']) }}">
                                </div>
                                <div class="detail">
                                    <h3 class="goods-name">
                                       {{$val['title']}}
                                    </h3>
                                    <p class="goods-info c-gray-dark font-size-12">
                                        <span class="c-red">
                                            ¥
                                        </span>
                                        <span class="price c-red font-size-14">
                                            {{$val['price']}}
                                        </span> 
                                    </p>
                                </div>
                            </a>
                        </div> 
                        @endforeach
                    </div>
                </div>
                <div class="allgroups-action-container center">
                    <a class="tag tag-all-groupons" href="/shop/index/{{$product['shop']['id']}}">
                        进店铺看看
                    </a>
                </div>
                <div style="height:50px;"></div>
            </div>
            @endif
            <div class="js-bottom-opts js-footer-auto-ele bottom-fix">
                <div class="responsive-wrapper">
                    <div class="big-btn-2-1 bottom_footer">
                        <div class="tf_lBut" style="width: 60px;">
                            <a href="/shop/index/{{ session('wid') }}">
                                <img src="{{ config('app.source_url') }}shop/images/sy@2x.png?t=123" />
                            </a>
                        </div>
                        <div class="tf_lBut">
                            <div class=" btn_bottom">
                                @if($reqFrom == 'aliapp')
                                <a href="{{config('app.chat_url')}}/zfb/kefu?productName={{urlencode($product['product']['title'])}}&productImg={{ imgUrl($product['product']['img']) }}&productPrice={{$product['product']['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$seckill['id'].'&type=7')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}&timestp={{time()}}">
                                @else
                                <a href="{{config('app.chat_url')}}/#/kefu?productName={{urlencode($product['product']['title'])}}&productImg={{ imgUrl($product['product']['img']) }}&productPrice={{$product['product']['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$seckill['id'].'&type=7')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}&timestp={{time()}}">
                                @endif
                                    @if($reqFrom == 'aliapp')
                                    <img src="{{ config('app.source_url') }}shop/images/alikf.png" />
                                    @else
                                    <img src="{{ config('app.source_url') }}shop/images/kf@2xx.png?t=123" />
                                    @endif
                                </a>
                                <span class="news-num hide"></span>
                            </div>
                        </div>
                        {{--<a href="{{config('app.chat_url')}}/zfb/kefu?productName={{$product['product']['title']}}&productImg={{ imgUrl($product['product']['img']) }}&productPrice={{$product['product']['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$seckill['id'].'&type=7')}}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}" class="big-btn new-btn">--}}
                            {{--客服--}}
                        {{--</a>--}}
                        <button class="big-btn btn-red js-panic-buy-it">
                            立即抢购
                        </button>
                    </div>
                </div>
                <div class="responsive-wrapper hide">
                    <div class="big-btn-1-1">
                        <button class="big-btn orange-btn js-buy-it">
                            原价购买
                        </button>
                    </div>
                </div>
            </div>
            <div id="js-share-guide" class="js-fullguide fullscreen-guide tuan-fullscreen-guide hide"
            style="font-size: 16px; line-height: 35px; color: #fff; text-align: center;">
                <div class="guide-arrow">
                </div>
                <div class="action-button center">
                    <button class="tag tag-red tag-big font-size-16">
                        我知道啦
                    </button>
                </div>
            </div>
        </div>
        <div id="shop-nav">
        </div>
        <!--add by 韩瑜 2018-9-6 收藏提示-->
		<div class='collecttip iscollecttip'>
	        <div >收藏成功</div>
	    </div>
	    <div class='collecttip nocollecttip'>
	        <div>取消成功</div>
	    </div>
	    <!--end-->
    </div>
</div>

@endsection
@section('page_js')
    <script type="text/javascript">
        var product = {!! json_encode($product) !!};
        var seckill = {!! json_encode($seckill) !!};  
        var product_sku = product.sku ? JSON.parse(product.sku) : {}; 
        var nowtime = "{!! str_replace('-','/',$seckill['now_at']) !!}";
        var NowTime = new Date(nowtime).getTime();
        var overtime = "{!! str_replace('-','/',$seckill['end_at']) !!}"; 
        var stime = "{!! str_replace('-','/',$seckill['start_at']) !!}"; 
        var status = seckill.status; //秒杀活动状态 1:未开时 2:进行中 3:已结束 4:失效  
        var wid = "{{session('wid')}}";  
        var _host = "{{ config('app.source_url') }}"; 
        var imgUrl = "{{ imgUrl() }}";
        var host = "{{config('app.url')}}";
        var videoUrl = "{{ videoUrl() }}";
        var shop_id = "{{$product['shop']['id']}}";
        var isBind = {{$__isBind__}};
        console.log(product);
    </script>
    <!-- 加入购物车弹窗 -->
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script> 
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/socket.io.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}shop/js/seckill_oi83u2yq.js"></script>
    <script>
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
        })
    </script>
@endsection
