@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_c8zrqijx.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/capital/serviceOrdering') }}">服务订购</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/bulkPurchase') }}">批量采购</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/cdkeyExchange') }}">激活码兑换</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!-- 标题 开始-->
    <p class="f20 mgb15">软件产品</p>
    <!-- 标题 结束 -->
    <!-- 产品轮播 开始 -->
    <div class="swiper_product swiper_items">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="display_box f12 mgb100">
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/6e1d7f8287399549c4d4179dc6eeb5ba.png" >
                            <p class="items_ellipsis mgb15">上百种营销组合玩法</p>
                            <p class="items_ellipisis2 gray_999">每月迭代2-3个玩法引领营销潮流</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bc30e020217295e514bd60b8fe7412d8.png" >
                            <p class="items_ellipsis mgb15">无缝对接移动社交平台</p>
                            <p class="items_ellipisis2 gray_999">打通微信、微博、QQ，购物体验顺畅</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/90aff2dc794b176790f860cb6cdec33f.png" >
                            <p class="items_ellipsis mgb15">智能匹配全渠道支付场景</p>
                            <p class="items_ellipisis2 gray_999">支持微信、支付宝、银行卡等收款方式</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="display_box f12 mgb100">
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/a0f4a0e64f5d9bb8714f0cbce7457963.png" />
                            <p class="items_ellipsis mgb15">3万笔/秒交易处理速度</p>
                            <p class="items_ellipisis2 gray_999">稳定、实时、高并发，不卡顿，不崩溃</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/142141e451332702846b44583a78c4cd.png" />
                            <p class="items_ellipsis mgb15">235家第三方合作伙伴</p>
                            <p class="items_ellipisis2 gray_999">64个开放接口对接ERP和商家自有系统</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/980a4e6fc236095a0936826c7420f19d.png" />
                            <p class="items_ellipsis mgb15">知名品牌的一致选择</p>
                            <p class="items_ellipisis2 gray_999">良品铺子、罗辑思维、好想你、幸福西饼</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 如果需要分页器 -->
            <div class="swiper-pagination"></div>
        </div>
        <!-- 订购按钮 开始  -->
        <a class="swiper_btn" href="{{ URL('/merchants/capital/chooseService') }}" target="_blank">订购会搜云微商城</a>
        <!-- 订购按钮 结束 -->
    </div>
    <!-- 产品轮播 结束 -->
    <!-- 我要购买 开始 -->
    <div class="buy_itmes display_box mgtb30">
        <div class="swiper_items box_flex1">
            <p class="items_ellipsis mgb15"><i class="glyphicon glyphicon-book"></i>我要购买会搜云微商城激活码</p>
            <a class="border_red" href="javascript:void(0);">批量采购激活码</a>
        </div>
        <div class="swiper_items box_flex1">
            <p class="items_ellipsis mgb15"><i class="glyphicon glyphicon-leaf"></i>我有会搜云微商城激活码</p>
            <a class="border_red" href="javascript:void(0);">使用激活码</a>
        </div>
    </div>
    <!-- 我要购买 结束 -->
    <!-- 标题 开始-->
    <p class="f20 mgb15">增值服务</p>
    <!-- 标题 结束 -->
    <!-- 增值服务轮播 开始 -->
    <div class="swiper_service swiper_items">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="display_box f12 mgb100">
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bc104789a2f6a75edde08db6574b7ca6.png" >
                            <p class="items_ellipsis mgb15">上百种营销组合玩法</p>
                            <p class="items_ellipisis2 gray_999">每月迭代2-3个玩法引领营销潮流</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/cc75b92fdd241e3dc9d0066ca2bec5d5.png" >
                            <p class="items_ellipsis mgb15">无缝对接移动社交平台</p>
                            <p class="items_ellipisis2 gray_999">打通微信、微博、QQ，购物体验顺畅</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/96e1a2493a235014b1338135750b574f.png" >
                            <p class="items_ellipsis mgb15">智能匹配全渠道支付场景</p>
                            <p class="items_ellipisis2 gray_999">支持微信、支付宝、银行卡等收款方式</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="display_box f12 mgb100">
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bcb0b1769957326f46a2a0746f1fa8b4.png" />
                            <p class="items_ellipsis mgb15">3万笔/秒交易处理速度</p>
                            <p class="items_ellipisis2 gray_999">稳定、实时、高并发，不卡顿，不崩溃</p>
                        </div>
                        <div class="box_flex1">
                            <img class="img_icon" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/e3f9d22318da59b860ab343515542eca.png" />
                            <p class="items_ellipsis mgb15">235家第三方合作伙伴</p>
                            <p class="items_ellipisis2 gray_999">64个开放接口对接ERP和商家自有系统</p>
                        </div>
                        <div class="box_flex1">   
                        </div>
                    </div>
                </div>
            </div>
            <!-- 如果需要分页器 -->
            <div class="swiper-pagination"></div>
        </div>
        <!-- 订购按钮 开始  -->
        <a class="swiper_btn" href="javascript:void(0);" target="_blank">订购会搜云微商城</a>
        <!-- 订购按钮 结束 -->
    </div>
    <!-- 增值服务轮播 结束 -->
    <!-- 标题 开始 -->
    <p class="f20 mgb15">插件应用 <a class="blue_00f" href="javascript:void(0);" target="_blank">营销中心</a></p>
    <!-- 标题 结束 -->
</div>
@endsection
@section('page_js')
<!-- 轮播插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_c8zrqijx.js"></script>
@endsection