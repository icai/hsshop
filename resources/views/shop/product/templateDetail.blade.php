@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/goods_62d5db3e3f0f2435e941566b8d882e5d.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <style type="text/css">
        .js-footer{margin-bottom:51px;}
        .swiper-wrapper img{width:100%}
    </style>
@endsection
@section('main')
    <div class="container wap-goods internal-purchase" id="container" style="min-height: 617px;">
        <div class="js-detail-container" style="margin-top: 10px;">
            <div class="js-tabber-container goods-detail" style="display: none;">
                <div class="js-tabber tabber tabber-n2 clearfix orange">
                    <button data-type="goods" class="active">商品详情</button>
                    <button data-type="reviews" class="">累计评价</button></div>
                <div class="js-tabber-content">
                    <div class="js-part js-trade-review-list trade-review-list hide" data-type="reviews">
                        <div class="js-review-tabber review-rate-tabber tabber tabber-n4 clearfix">
                            <span class="item">
                                <button class="js-rate-all rate js-cancal-disable-link active" data-reviewtype="all" data-rate="0">全部</button></span>
                            <span class="item">
                                <button class="js-rate-good js-cancal-disable-link" data-reviewtype="good" data-rate="30">好评</button></span>
                            <span class="item">
                                <button class="js-rate-middle js-cancal-disable-link" data-reviewtype="middle" data-rate="20">中评</button></span>
                            <span class="item">
                                <button class="js-rate-bad js-cancal-disable-link" data-reviewtype="bad" data-rate="10">差评</button></span>
                        </div>
                        <div class="js-review-tabber-content block block-list">
                            <div class="js-review-report-container report-detail-container block-item no-border hide pd0"></div>
                            <div class="js-review-part review-detail-container" data-reviewtype="all">
                                <div class="js-list b-list">
                                    <a href="/shop/product/evaluateDetail/" class="js-review-item review-item block-item">
                                        <div class="name-card">
                                            <div class="thumb">
                                                <img class="test-lazyload" data-original="" alt=""></div>
                                            <div class="detail">
                                                <h3>昵称</h3>
                                                <p class="font-size-12">created_at</p>
                                            </div>
                                        </div>
                                        <div class="item-detail font-size-14 c-gray-darker">
                                            <p>内容</p>
                                        </div>
                                        <div class="other">
                                            <span class="from">购买自：本店</span>
                                            <p class="pull-right">
                                                <span class="js-like like-item ">
                                                    <i class="like"></i>
                                                    <i class="js-like-num">agree_num</i></span>
                                                        <span class="js-add-comment">
                                                    <i class="comment"></i>
                                                    <i class="js-comment-num"></i>
                                                </span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="/shop/product/evaluateDetail/" class="js-review-item review-item block-item">
                                        <div class="name-card">
                                            <div class="thumb">
                                                <span class="center font-size-18 c-orange">匿</span>
                                            </div>
                                            <div class="detail">
                                                <h3>匿名</h3>
                                                <p class="font-size-12">created_at</p>
                                            </div>
                                        </div>
                                        <div class="item-detail font-size-14 c-gray-darker">
                                            <p>content</p>
                                        </div>
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
                                    <div class="list-finished more" data-status="0">加载更多</div>
                                </div>
                                <div class="list-finished">暂无评论</div>
                            </div>
                            <div class="js-review-part review-detail-container hide" data-reviewtype="good">
                                <a href="/shop/product/evaluateDetail" class="js-review-item review-item block-item">
                                    <div class="name-card">
                                        <div class="thumb">
                                            <img class="test-lazyload" data-original="" alt=""></div>
                                        <div class="detail">
                                            <h3>['member']['nickname']</h3>
                                            <p class="font-size-12">['created_at']</p></div>
                                    </div>
                                    <div class="item-detail font-size-14 c-gray-darker">
                                        <p>['content']</p>
                                    </div>
                                    <div class="other">
                                        <span class="from">购买自：本店</span>
                                        <p class="pull-right">
                                        <span class="js-like like-item ">
                                            <i class="like"></i>
                                            <i class="js-like-num">['agree_num']</i></span>
                                            <span class="js-add-comment">
                                            <i class="comment"></i>
                                            <i class="js-comment-num"></i>
                                        </span>
                                        </p>
                                    </div>
                                </a>
                                <a href="/shop/product/evaluateDetail" class="js-review-item review-item block-item">
                                    <div class="name-card">
                                        <div class="thumb">
                                            <span class="center font-size-18 c-orange">匿</span>
                                        </div>
                                        <div class="detail">
                                            <h3>匿名</h3>
                                            <p class="font-size-12">['created_at']</p>
                                        </div>
                                    </div>
                                    <div class="item-detail font-size-14 c-gray-darker">
                                        <p>['content']</p>
                                    </div>
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
                                <div class="list-finished more" data-status="1">加载更多</div>
                                <div class="list-finished">暂无好评论</div>
                            </div>
                            <div class="js-review-part review-detail-container hide" data-reviewtype="middle">
                                <a href="/shop/product/evaluateDetail" class="js-review-item review-item block-item">
                                    <div class="name-card">
                                        <div class="thumb">
                                            <img class="test-lazyload" data-original="" alt=""></div>
                                        <div class="detail">
                                            <h3>['member']['nickname']</h3>
                                            <p class="font-size-12">['created_at']</p></div>
                                    </div>
                                    <div class="item-detail font-size-14 c-gray-darker">
                                        <p>['content']</p>
                                    </div>
                                    <div class="other">
                                        <span class="from">购买自：本店</span>
                                        <p class="pull-right">
                                        <span class="js-like like-item ">
                                            <i class="like"></i>
                                            <i class="js-like-num">['agree_num']</i></span>
                                            <span class="js-add-comment">
                                            <i class="comment"></i>
                                            <i class="js-comment-num"></i>
                                        </span>
                                        </p>
                                    </div>
                                </a>
                                <a href="/shop/product/evaluateDetail/" class="js-review-item review-item block-item">
                                    <div class="name-card">
                                        <div class="thumb">
                                            <span class="center font-size-18 c-orange">匿</span>
                                        </div>
                                        <div class="detail">
                                            <h3>匿名</h3>
                                            <p class="font-size-12">['created_at']</p>
                                        </div>
                                    </div>
                                    <div class="item-detail font-size-14 c-gray-darker">
                                        <p>['content']</p>
                                    </div>
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
                                <div class="list-finished more" data-status="2">加载更多</div>
                                <div class="list-finished">暂中评论</div>
                            </div>
                            <div class="js-review-part review-detail-container hide" data-reviewtype="bad">
                                <a href="/shop/product/evaluateDetail/" class="js-review-item review-item block-item">
                                    <div class="name-card">
                                        <div class="thumb">
                                            <img class="test-lazyload" data-original="" alt="">
                                        </div>
                                        <div class="detail">
                                            <h3>大时代</h3>
                                            <p class="font-size-12">个复古风格</p>
                                        </div>
                                    </div>
                                    <div class="item-detail font-size-14 c-gray-darker">
                                        <p>地方东方饭店</p>
                                    </div>
                                    <div class="other">
                                        <span class="from">购买自：本店</span>
                                        <p class="pull-right">
                                        <span class="js-like like-item ">
                                            <i class="like"></i>
                                            <i class="js-like-num">13</i></span>
                                            <span class="js-add-comment">
                                            <i class="comment"></i>
                                            <i class="js-comment-num"></i>
                                        </span>
                                        </p>
                                    </div>
                                </a>
                                <a href="/shop/product/evaluateDetail/" class="js-review-item review-item block-item">
                                    <div class="name-card">
                                        <div class="thumb">
                                            <span class="center font-size-18 c-orange">匿</span>
                                        </div>
                                        <div class="detail">
                                            <h3>匿名</h3>
                                            <p class="font-size-12">VBVB</p>
                                        </div>
                                    </div>
                                    <div class="item-detail font-size-14 c-gray-darker">
                                        <p>大幅度</p>
                                    </div>
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
                                <div class="list-finished more" data-status="3">加载更多</div>
                                <div class="list-finished">暂无差评论</div>
                            </div>
                        </div>
                    </div>
                    <div class="js-part js-goods-detail goods-tabber-c" data-type="goods">
                        <!-- 商品的富文本  自定义组件的添加开始 -->
                        <div class="pc_product_setting">
                            <custom-template :lists= "lists"></custom-template>
                        </div>
                        <!-- 商品的富文本  自定义组件的添加结束 -->
                        <!-- 广告业添加开始 -->
                        <div class="pc_ad_setting" v-if= "productAdPosition == 2">
                            <custom-template :lists= "productAd"></custom-template>
                        </div>
                        <!-- 广告页添加结束 -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <!-- 加入购物车弹窗 -->
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <script type="text/javascript">
        var _host = "{{ config('app.source_url') }}";
        var imgUrl = "{{ imgUrl() }}";
        var wid = {{ $wid }};//店铺id
        var product = '{!! $productTemplate !!}';//发布商品详情
        var productDetailTemplate='{!! $productDetailTemplate !!}';//商品页模板
        var microPageNotice='{!! $microPageNotice !!}';//公共广告
    </script>
    <script src="{{ config('app.source_url') }}shop/js/templateDetail.js"></script>
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <!--懒加载插件-->
@endsection
