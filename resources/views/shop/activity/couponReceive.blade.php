@extends('shop.common.marketing')
@section('head_css')
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/coupon_result_47c36b8795e6187809b2061c328b54b2.css">
<style type="text/css">
    .coupon-mini .coupon-value>span {
        display: inline-block;
        font-size: 30px;
        vertical-align: middle;
        margin-right: 2px;
    }
    .coupon-mini .coupon-value>i {
        font-size: 12px;
        padding-right: 14px;
        position: relative;
        top: 6px;
        left: -4px;
    }
    .coupon-mini .coupon-info .hid_z{
        font-size:12px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    .coupon-validity .end_date{
        padding-left: 40px;
    }
    .coupon-container .coupon-msg, .coupon-container .coupon-validity {
        text-align: center;
        font-size: 12px;
        color: #fff;
        margin-bottom: 5px;
        margin-top: 10px;
    }
</style>
@endsection
@section('main')
<body class=" coupon-result">
    <div class="container ">
        <div class="promocard-result">
            <input type="hidden" name="wid" value="{{$wid}}">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="coupon-success-msg">
                <figure class="bg-pic circle-bg-pic">
                    <div class="bg-pic-content"></div>
                </figure>
                <p class="msg">
                    我领到<span class="shop_name"></span>优惠券啦～
                </p>
            </div>
            <!-- 优惠券信息 start -->
            <!-- 只在领取成功后显示 -->
            <div class="coupon-container">
                <div class="coupon-mini">
                    <div class="coupon-type">优惠券</div>
                    <div class="coupon-info">
                        <div class="coupon-value">
                            <span class="receive_amount"></span>
                            <i>元</i>
                        </div>
                        <p class="hide hid_z">订单满<span class="is_limited"></span>元可用
                        </p>
                        <p class="hide hid_z">不限制</p>
                        <!-- 优惠券失效样式 -->
                        <h3 class="coupon-error ellipsis err-tip2"></h3>
                        <p class="coupon-error ellipsis err-tip3"></p>
                    </div>
                </div>
                    <div class="coupon-msg">
                        <span>该券已放入你的账户</span>
                        <i><span class="mobile"></span></i>
                    </div>
                    <div class="coupon-validity">有效期：
                        <i><span class="start_at"></span></i>至<br />
                        <div class="end_date"><span class="end_at"></span></div>
                    </div>
                    <div class="coupon-actions hide flag-for">
                        <a href="javascript:void(0);" class="js-tj-use btn btn-block btn-main-action">立即使用</a>
                    </div>
                    <div class="coupon-actions hide flag-in">
                        <a href="javascript:void(0);" class="js-tj-use btn btn-block btn-main-action">进店逛逛</a>
                    </div>

            </div>
            <!-- 优惠券信息 end -->
            <div class="cloud"></div>
        </div>
        <div class="opt">
            <a href="javascript:void(0);" id="addCard" class="btn btn-block btn-green" data-id="" data-err="">同步到微信卡包</a>
        </div>
        <div class="promocard-others">
            <div class="action-container"></div>
            <a href="/shop/member/coupons/{{$wid}}/1" class="block-link name-card name-card-3col name-card-promocard name-card-my">
                <figure class="thumb">
                    <img src=""></figure>
                <div class="detail">
                    <h3>
                        <strong>我领到的优惠券总额</strong>
                    </h3>
                </div>
                <div class="right-col">
                    <div class="price">
                    <span class="sum">0</span>元</div>
                </div>
            </a>
            <div class="line-block line-block-gray">
                <div class="lineblock-title">
                    <span class="lineblock-font">看看其他朋友</span>
                </div>
            </div>
        </div>
    </div>
     <!-- 页面加载开始 -->
    <div class="pageLoading">
        <img src="{{ config('app.source_url') }}shop/images/loading.gif">
    </div>
    <!-- 页面加载结束 --> 
</body>
@include('shop.common.footer') 
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/couponReceive.js"></script>
@endsection