@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_cuigkop4.css" />
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
            <li>
                <a href="{{ URL('/merchants/currency/guarantee') }}">担保交易</a>
            </li>
            <li class="hover">
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
    <div class="content_title">
        <div class="title_left">
            <span id="">保证金账户金额<br/><small>(截至今日0点)</small></span>
            <p id="cash" style="font-size: 25px; color: #999;">0.00</p>元
        </div>
        <div class="title_center">目前无消费保障保证金。您可以通过充值或冻结店铺账户余额缴纳，加入担保金担保交易，享受全程消费担保。</div>
        <div class="title_right"><a href="##" style="color: #27f;">查看保证金记录</a></div>
    </div>
    <div class="content_body">
        <div class="content_top">
        <img src="{{ config('app.source_url') }}mctsource/images/14.png"/>待开通
            <span id="">保证金保障服务</span>
        </div>
        <div class="content_main">
            <span id="">什么是保证金</span>
            <p>保证金（全称：消费保障计划保证金）属于不可用余额，当店铺余额不足时，用于发生订单维权退款时及时垫付给买家，提升客户消费体验。</p>
            <p>＊保证金账户目前仅支持店铺余额冻结进行缴纳。缴纳后保证金保障仅对新订单生效。</p>
            <p>＊保证金并不是向会搜云付费，是可退回的，并且在订单/收入提现 - 不可用余额处可查到明细记录。</p>
            <a href="##" style="color: #27f;">更多说明：查看《保证金的一些事》</a>
            <span id="">开通保证金保障服务的好处</span>
            <p>加入担保交易并缴纳相应保证金，即可获得享受担保交易全部权益，包括：</p>
            <ul id="firstUl">
                <li>“担保交易”安全标示<p>店铺、商品页显示，让客户更放心</p></li>
                <li>全程消费保障<p>订单维权会搜云助力双向保障</p></li>
                <li>客户更多信任<p>支付交易会搜云担保放心更安全</p></li>
                <li>会搜云更多流量支持<p>活动入口优先供给头条热门全都有</p></li>
            </ul>
            <br />
            <p>还能获得：</p>
            <ul id="thirdUl">
                <li>快速结算服务<p>发货后立刻结算货款</p></li>
            </ul>
            <span id="">保障亮点</span>
            <ul id="secondUl">
                <li>结算周期短</li>
                <li>自动维权处理</li>
                <li>会搜云活动入口优先</li>
            </ul>
            <span id="">结算流程</span>
            <img src="{{ config('app.source_url') }}mctsource/images/15.png"/>
        </div>
        <div class="content_bottom">
            <label for="agreement">
                <input type="checkbox" name="" id="agreement" value="" checked="checked"/>我已经仔细阅读并同意
                <a href="##" style="color: #0000FF">查看《担保交易服务协议》</a>
            </label>
            <br />
            <button type="button" class="btn btn-success">缴纳开通</button>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<!-- 图表插件 -->
<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
<!-- 模版文件js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_v7mlznmp.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_cuigkop4.js"></script>
@endsection