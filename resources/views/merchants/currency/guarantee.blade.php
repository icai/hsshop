@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_phqchvcx.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/currency/guarantee') }}">担保交易</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/margin') }}">保证金</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
    </div>   
    <!-- 三级导航 结束 -->

    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div class="borderShow">
        <div class="content_top">
            <img src="{{ config('app.source_url') }}mctsource/images/12.png"/>已加入
            <span id="">担保交易</span>
        </div>
        <div class="content_main">
            <span id="">什么是担保交易</span>
            <p>会搜云担保交易是一项保护买卖双方的消费保障计划，享受交易全额承保。</p>
            <p>＊加入担保后保障仅对新增订单有效</p>
            <span id="">加入担保交易的好处 会搜云全力协助担保交易商户，加入即可获得：</span>
            <ul id="firstUl">
                <li>“担保交易”安全标示<p>店铺、商品页显示，让客户更放心</p></li>
                <li>全程消费保障<p>订单维权会搜云助力双向保障</p></li>
                <li>客户更多信任<p>支付交易会搜云担保放心更安全</p></li>
                <li>会搜云更多流量支持<p>活动入口优先供给头条热门全都有</p></li>
            </ul>
            <span id="">保障亮点</span>
            <ul id="secondUl">
                <li>加入门槛低</li>
                <li>无需冻结资金</li>
                <li>结算周期自动维权处理</li>
            </ul>
            <span id="">结算流程</span>
            <img src="{{ config('app.source_url') }}mctsource/images/13.png"/>
        </div>
        <div class="content_bottom">
            <a href="##">结算周期长？开通保证金保障服务，发货后立即结算 ></a>
            <a href="##" style="color: #0000FF">查看《担保交易服务协议》</a>
            <button type="button" class="btn btn-default">退出担保交易</button>
        </div>
        <br />
        <br />
    </div>
</div>
@endsection
@section('page_js')
<!-- 图表插件 -->
<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
<!-- 模版文件js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_v7mlznmp.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_phqchvcx.js"></script>
@endsection