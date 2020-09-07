<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="utf-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_admin_with_components_99562062d4cc8282402cd99c65db38a1.css" />
    <!--当前页面css-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/seckill_401d688e63de381b6d2f18a971555d81.css" /> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css" />
</head>
<body class="body-fixed-bottom"> 
    <div class="content " id="container">
        <div class="content-body"> 
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @forelse($product['product']['productImg'] as $val)
                        <div class="swiper-slide">
                            <img src="{{ imgUrl($val['img']) }}" />
                        </div>
                        @endforeach
                </div>
                <!-- 如果需要分页器 -->
                <div class="swiper-pagination"></div> 
            </div> 
            <div class="goods-header goods-activity">
                <div class="goods-price clearfix">
                    <div class="activity-price current-price">
                        <span class="price-title">
                            {{$seckill['tag']}}
                        </span>
                        <i class="js-goods-price price">
                            <div class="price-wrapper">
                                <span>¥</span><i>{{$seckill['price_range']}}</i>
                            </div>
                        </i>
                    </div>
                    <div class="original-price">
                        {{$seckill['oprice_range']}}
                    </div>
                    <div class="overview-countdown">
                        <div class="countdown-title">
                            距结束仅剩
                        </div>
                        <div class="js-time-count-down countdown">
                            <span class="js-span-d"></span>
                            <i class="js-i-d" style="font-size:12px;display: none;">天</i> 
                            <span class="js-span-h"></span>时
                            <span class="js-span-m"></span>分
                            <span class="js-span-s"></span>秒
                        </div>
                    </div>
                </div>
                <div class="goods-title">
                    <h2 class="title">
                        {{$seckill['title']}}
                    </h2>
                </div>
                <hr class="with-margin-l">
                <div class="stock-detail">
                    <dl>
                        <dt>
                            运费:
                        </dt>
                        <dd class="js-postage-desc" data-postage="免运费">
                            {{$product['defaultFreight']}}
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            剩余:
                        </dt>
                        <dd>
                            {{$seckill['stock_sum']}}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="js-store-info">
                <div class="block block-list goods-store">
                    <div class="custom-store block-item ">
                        <a class="custom-store-link clearfix" href="/shop/index/{{session('wid')}}">
                            <div class="custom-store-img"></div>
                            <div class="custom-store-name">{{$product['shop']['shop_name']}}</div>
                            <span class="custom-store-enter"></span>
                        </a>
                    </div>
                    <div class="renzheng block-item">
                        <!-- <span class="js-rz-item-alert rz-item" data-type="team_certificate_company">
                                <span class="rz-name font-size-12 c-gray-darker">企业认证</span>
                            </span>
                        <span class="js-rz-item-alert rz-item" data-type="is_secured_transactions">
                            <span class="rz-name font-size-12 c-gray-darker">
                                担保交易
                            </span>
                        </span> -->
                    </div>
                </div>
            </div>
            <div class="js-detail-container" style="margin-top: 10px;">
                <div class="js-tabber-container goods-detail">
                    <div class="js-tabber-content">
                        <div class="js-part js-goods-detail goods-tabber-c" data-type="goods">
                            <div class="js-components-container components-container">
                                <div class="custom-richtext js-lazy-container js-view-image-list">
                                    <!-- 商品的富文本  自定义组件的添加开始 -->
                                    <div class="pc_product_setting">
                                        <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
                                    </div>
                                    <!-- 商品的富文本  自定义组件的添加结束 --> 
                                </div>
                                <div class="price-intro">
                                    <h4>
                                        划线价格说明
                                        <i class="icon-arrow">
                                        </i>
                                    </h4>
                                    <p>
                                        划线价格：划线的价格可能是商品的专柜价、吊牌价、正品零售价、指导价、曾经展示过的销售价等，仅供您参考。
                                    </p>
                                    <p>
                                        未划线价格：未划线的价格是商品的销售标价，具体的成交价格可能因会员使用优惠券、积分等发生变化，最终以订单结算价格为准。
                                    </p>
                                    <p>
                                        *此说明仅当出现价格比较时有效。若这件商品针对划线价格进行了特殊说明，以特殊说明为准。
                                    </p>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="js-bottom-opts js-footer-auto-ele bottom-fix ">
                <div class="js-bottom-opts js-footer-auto-ele bottom-fix">
                    <div class="responsive-wrapper">
                        <div class="big-btn-1-1">
                            <button class="big-btn red-btn js-buy-it">
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
                    <div class="responsive-wrapper hide">
                        <div class="mini-btn-2-1 " style="width:70px;"> 
                            <a id="global-cart" href="javascript:;"
                            class="new-btn buy-cart">
                                <i class="iconfont icon-shopping-cart">
                                </i>
                                <span class="desc">购物车</span>
                                <span class="goods-num">2</span>
                            </a>
                        </div>
                        <div class="big-btn-2-1">
                            <button class="big-btn orange-btn js-buy-it">
                                加入购物车
                            </button>
                            <button class="big-btn red-btn js-buy-it">
                                立即购买 
                            </button>
                        </div>
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

            @if(!empty($product['more']))
                <div class="js-recommend">
                    <p class="center font-size-14 text-cancel" style="padding: 5px 0;margin-top: 10px;">更多精选商品</p>
                    <div class="js-recommend-goods-list">
                        <ul class="js-goods-list sc-goods-list pic clearfix size-1 " data-size="1" data-showtype="card" style="visibility: visible;">
                            <!-- 商品区域 -->
                            <!-- 展现类型判断 -->
                            @forelse($product['more'] as $value)
                                <li class="js-goods-card goods-card small-pic card ">
                                    <a href="/shop/product/detail/{{$product['shop']['id']}}/{{$value['id']}}" class="js-goods link clearfix"  data-goods-id="330500316" title="{{$value['title']}}">
                                        <div class="photo-block" data-width="0" data-height="0">
                                            <img class="goods-photo js-goods-lazy lazyload" data-original="{{ imgUrl($value['img']) }}">
                                        </div>
                                        <div class="info clearfix info-title info-price btn0">
                                            <p class=" goods-title ">{{$value['title']}}</p>
                                            <p class="goods-price">
                                                <em>￥{{$value['price']}}</em></p>
                                            <p class="goods-price-taobao ">市场价：￥{{$value['oprice']}}</p>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                        </ul>
                    </div>
                    <p class="center" style="margin: 10px 0 20px;">
                        <a href="/shop/index/{{$product['shop']['id']}}" class="center btn btn-white btn-xsmall font-size-14 " style="padding:8px 26px;">进店逛逛&gt;</a>
                    </p>
                </div>
            @endif

        </div>
        <div class="content-sidebar">
            <div id="js-qrcode-container" class="sidebar-section qrcode-container">
                <div class="section-detail">
                    <h3 class="shop-detail">
                        手机扫码购买
                    </h3>
                    <p class="text-center weixin-title">
                        微信“扫一扫”立即购买
                    </p>
                    <p class="text-center qr-code">
                        {!! QrCode::size(120)->generate(URL("/shop/seckill/detail/" . $seckill['wid'] . '/' . $seckill['id'])) !!}
                    </p>
                </div>
            </div>
        </div>
        
        <div id="shop-nav">
        </div>
    </div>
    <script type="text/javascript">
        var APP_HOST = "{{ config('app.url') }}"
        var APP_IMG_URL = "{{ imgUrl() }}"
        var APP_SOURCE_URL = "{{ config('app.source_url') }}"
    </script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script> 
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <script type="text/javascript">
        var product = {!! json_encode($product) !!};
        var seckill = {!! json_encode($seckill) !!};  
        var product_sku = product.sku ? JSON.parse(product.sku) : {}; 
        var nowtime = "{!! str_replace('-','/',$seckill['now_at']) !!}";
        var nowTime = new Date(nowtime).getTime();
        var overtime = "{!! str_replace('-','/',$seckill['end_at']) !!}"; 
        var stime = "{!! str_replace('-','/',$seckill['start_at']) !!}"; 
        var status = seckill.status; //秒杀活动状态 1:未开时 2:进行中 3:已结束 4:失效  
        var wid = "{{session('wid')}}";  
        var _host = "{{ config('app.source_url') }}"; 
        var imgUrl = "{{ imgUrl() }}";
        var host = "{{config('app.url')}}";
        var shop_id = "{{$product['shop']['id']}}";


        var mySwiper = new Swiper('.swiper-container', {
            autoplay: 3000,//可选选项，自动滑动
            loop : true,
            pagination : '.swiper-pagination'
        });
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}shop/js/seckill_401d688e.js"></script>
</body>
</html>