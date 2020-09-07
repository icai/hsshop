@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/goods_62d5db3e3f0f2435e941566b8d882e5d.css"> 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/group_oi83u2yq.css">  
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?v=111"> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <style type="text/css">
        .js-footer{margin-bottom:51px;} 
        .custom-image-swiper {
		    width: 100%;
		    position: relative;
		} 
		.swiper-container {
			width:100%;
			height:auto;
		}
		.swiper-slide img{width:100%;height:auto;}
    </style>
@endsection
@section('main') 
    <div class="container wap-goods internal-purchase" style="min-height: 617px;">
    <div class="header">
    </div>
    <div class="content no-sidebar">
        <div class="content-body">
            <div class="swiper-container">
			    <div class="swiper-wrapper">
                    @forelse($rule['img'] as $val)
                        <div class="swiper-slide">
                            <img class="" src="/{{$val['img']}}" />
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
                            {{$rule['groups_num']}}人拼团价
                        </span>
                        <span class="js-goods-price price">
                            <div class="price-wrapper">
                                <span>
                                    ¥
                                </span>
                                {{$rule['min']}}
                                <i>
                                    @if($rule['max'] && $rule['min'] != $rule['max'])～{{$rule['max']}}@endif
                                </i>
                            </div>
                            <div style="font-size:12px;">
                                &nbsp;&nbsp;原价：<s>{{$rule['product']['price'] }}</s>
                            </div>
                        </span> 
                    </div>
                    {{--<div class="original-price">--}}
                        {{--¥0.20--}}
                    {{--</div>--}}
                    <div class="overview-countdown">
                        <div class="countdown-title">
                            距结束仅剩
                        </div>
                        <div class="js-time-count-down countdown">
                            <span class="js-span-d" style="display: none;"></span>

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
                    <h2 class="title">
                        {{$rule['title']}}
                    </h2>
                </div>
                <div class="block border-0 block-groupon-intro activity-intro">
                    <a class="block-item store-arrow-right" href="/shop/grouppurchase/guide">
                        <h3 class="title">
                            拼团玩法
                        </h3>
                        <span class="view-detail pull-right c-gray-dark">
                            玩法详情
                        </span>
                    </a>
                    <div class="block-item desc c-gray-dark">
                        <div class="ui three overview overview-groupon">
                            <div class="item">
                                <span class="steps">
                                    1
                                </span>
                                <p class="step-info">
                                    选择商品，
                                    <br>
                                    付款开团/参团
                                </p>
                            </div>
                            <div class="item">
                                <span class="steps">
                                    2
                                </span>
                                <p class="step-info">
                                    邀请并等待好
                                    <br>
                                    友支付参团
                                </p>
                            </div>
                            <div class="item">
                                <span class="steps">
                                    3
                                </span>
                                <p class="step-info">
                                    达到人数，
                                    <br>
                                    顺利成团
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stock-detail">
                    <dl>
                        <dt>
                            运费:
                        </dt>
                        <dd class="js-postage-desc" data-postage="免运费">
                            免运费
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            剩余:
                        </dt>
                        <dd>
                            {{$rule['product']['stock']}}
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            销量:
                        </dt>
                        <dd>
                            {{$rule['product']['sold_num']}}
                        </dd>
                    </dl>
                </div>
            </div>}
            <div class="js-store-info">
                <div class="block block-list goods-store">
                    <div class="custom-store block-item ">
                        <a class="custom-store-link clearfix" href="/shop/index/{{session('wid')}}">
                            <div class="custom-store-img">
                            </div>
                            <div class="custom-store-name">
                                {{$__weixin['shop_name']}}
                            </div>
                            <span class="custom-store-enter">
                                进入店铺
                            </span>
                        </a>
                    </div>
                    <a class="offline-store block-item js-retail-store hide">
                        <span class="offline-store-img">
                        </span>
                        <span class="offline-store-name">
                            线下门店
                        </span>
                        <div class="offline-store-branch js-retail-store-name">
                        </div>
                    </a>
                    <div class="renzheng block-item">
                        <span class="js-rz-item-alert rz-item" data-type="is_secured_transactions">
                            <span class="rz-name font-size-12 c-gray-darker">
                                担保交易
                            </span>
                        </span>
                    </div>
                </div>
                @if($rule['is_open'] == 1)
                    <div class="block border-top-0 block-joingroup">
                        <div class="block-item">
                            <h3 class="title">
                                懒人凑团挤一挤
                            </h3>
                        </div>
                        @forelse($groups as $val)
                        <div class="block-item name-card name-card-3col joingroup-name-card">
                            <div class="thumb">
                                <img class="circular" src="{{$val['headimgurl']}}">
                            </div>
                            <div class="detail">
                                <h3 class="joingroup-title">
                                    {{$val['nickname']}}
                                    <span class="c-gray-dark title-tip">
                                        开团
                                    </span>
                                </h3>
                                <div class="joingroup-desc js-joingroup-desc">
                                    剩余
                                    <div class="js-joingroup-countdown joingroup-countdown" data-seconds="72444">
                                        <span class="c-red">
                                            {{$val['end_time']}}
                                        </span>
                                    </div>
                                    结束，仅差
                                    <span class="c-red">
                                        {{$val['num']}}
                                    </span>
                                    人
                                </div>
                            </div>
                            <div class="right-col">
                                <a href="/shop/grouppurchase/groupon/{{$val['id']}}" class="tag tag-red tag-joingroup">
                                    去凑团
                                </a>
                            </div>
                        </div>
                        @endforeach
                        <div class="all-joingroups center">
                            <a class="joingroups-link arrow-right" href="javascript:;">
                                查看更多凑团
                            </a>
                        </div>
                    </div>
                @endif
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
                            <div class="pc_product_setting">
                                <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="groupon-goods-list-container">
                <h3 class="title">
                    更多拼团商品
                </h3>
                <div class="js-waterfall">
                    <div class="js-list groupon-goods-list clearfix">
                        @forelse($more as $val)
                        <div class="name-card-wrap">
                            <a class="name-card-vertical" href="/shop/grouppurchase/detail/{{$val['id']}}">
                                <div class="thumb-wrap">
                                    <img class="thumb lazyload" data-original="/{{$val['product']['img']}}">
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
                                            {{$val['min']}}@if($val['max'] && $val['min'] != $val['max'])～{{$val['max']}}@endif
                                        </span>
                                        <span>
                                            ／件
                                        </span>
                                        <span class="groupon-tag">
                                            {{$val['groups_num']}}人团
                                        </span>
                                    </p>
                                </div>
                            </a>
                        </div>
                      @endforeach
                    </div>
                </div>
                <div class="allgroups-action-container center">
                    <a class="tag tag-red tag-all-groupons" href="/shop/grouppurchase/index">
                        查看全部拼团商品
                    </a>
                </div>
            </div>
            <div class="js-bottom-opts js-footer-auto-ele bottom-fix">
                <div class="responsive-wrapper">
                    <div class="mini-btn-2-1" style="width:70px;">
                        <a id="global-cart" href="/shop/cart/index/{{session('wid')}}"
                        class="new-btn buy-cart">
                            <i class="iconfont icon-shopping-cart">
                            </i>
                            <span class="desc">购物车</span>
                            <span class="goods-num" @if($num == 0)style="display: none;"@endif>{{$num}}</span>
                        </a>
                    </div>
                    <div class="big-btn-2-1">
                        <button class="big-btn orange-btn js-buy-it" data-btn-type="single_buy">
                            ¥ {{$rule['product']['price']}}单买
                        </button>
                        <button class="big-btn red-btn js-buy-it" data-btn-type="open_group">
                            ¥{{$rule['min']}} 开团
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
    </div>
</div>

@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var overtime = "{!! str_replace('-','/',$rule['end_time']) !!}";
        var nowTime = "{!! $nowTime !!}";
        var ntime = new Date(nowTime).getTime();
        var rule = {!!json_encode($rule)!!}; 
        var wid = "{{ session('wid') }}";//店铺ID
        var pid = rule.pid; //商品ID
        var _host = "{{ config('app.source_url') }}";
        var imgUrl = "{{ imgUrl() }}";
        var pdetail ="";
        @if($rule["product"]["content"])
            pdetail = {!! $rule["product"]["content"]!!};  
        @endif

        var product = rule.product; 
        var host = "{{config('app.url')}}";
        var shop_id = "{{session('wid')}}";
    </script>
    <!-- 加入购物车弹窗 -->
    <script src="{{ config('app.source_url') }}shop/js/until.js?v=1.00"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}shop/js/group_oi83u2yq.js"></script>
@endsection
