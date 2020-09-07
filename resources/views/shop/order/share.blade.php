@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_share_4c41a770d8e6930ca9b8f319b924d665.css">
@endsection
@section('main')
<div class="container ">
    <div class="content">
        <div class="header order-share-back center" style="visibility: hidden;">
            <div class="circular nested circular-56">
                <img class="circular" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/556e3fc8c9e2851785f67c58c109e31f.png">
            </div>
            <p class="meta" style='color: #F72F37'>
                <!-- 积分兑换 -->
                \(^o^)/ 哦耶！在买买买的路上越战越勇~</p>
            </div>
        <!-- 订单简略 -->
        <div class="block">
            <!-- 分两种情况：订单中只有一类商品，订单中有两类商品 -->
            <div class="order-warp">
                <div class="order-scroll more">

                    <div class="order" style="visibility: visible;">
                        @forelse($orderDetail as $val)
                        <div class="order-item" style="display:block;border-radius: 8px; overflow: hidden">
                            <a class="goods-url" href="/shop/product/detail/{{session('wid')}}/{{$val['product_id']}}">
                                <div class="banner" style="background: url({{ imgUrl($val['img']) }});"></div>
                                <h1 class="title">{{$val['title']}}</h1>
                                <p class="price" style="color: #F72F37">￥{{$val['price']}}</p>
                            </a>
                            <div class="hr"></div>
                            <p class="shop-name center"></p>
                            <div class="center">
                                <a style="color: #F72F37;border-color: #F72F37;" href="/shop/index/{{$val['order']['wid']}}" class="tag tag-orange tag-home">进店逛逛</a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
        <!-- 分享遮罩 -->
        <div id="js-share-guide" class="js-fullguide fullscreen-guide tuan-fullscreen-guide" style="font-size: 16px; line-height: 35px; color: #fff; text-align: center; @if(!empty(request('_pid_'))) display:none @endif">
            <!-- 自定义了箭头 -->
            <div class="guide-arrow"></div>
            <div class="guide-inner">
                <div class="circular nested circular-56">
                    <img class="circular" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/556e3fc8c9e2851785f67c58c109e31f.png">
                </div>
                <p class="meta">\(^o^)/YES! 买到一件好货!
                    <br>快去分享给小伙伴吧！</p>
            </div>
        </div>
    </div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/order_share.js"></script>
@endsection
