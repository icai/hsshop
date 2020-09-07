@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/css/payFinish.css"/>
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="javascript:void(0);">续费服务</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/fee/order/list') }}">我的订购</a>
            </li>
        </ul>
        <!-- 二级导航三级标题 结束 -->
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
    <div class="top-wrap">
        <div class="top">
            <ul>
                <li class="schedule">1.选择所需服务</li>
                <li>-----</li>
                <li class="schedule">2.确认续费订单信息</li>
                <li>-----</li>
                <li class="schedule">3.续费服务支付</li>
                <li>-----</li>
                <li class="schedule checked">4.完成续费</li>
            </ul>
        </div>
    </div>
    <div class="article-wrap">
        <div class="payment-complete schedule-item">
            <div class="complete-pay">
                <div class="complete-top">
                    <img src="{{ config('app.source_url') }}static/images/remitcomplete.png" alt="complete" class="state1"/>
                    <img src="{{ config('app.source_url') }}static/images/waitReview.png" alt="review" class="state2"/>
                    <span class="state1">你已<span class="red">成功</span>续费<span  class="paytype-3 serviceVersion"></span></span>
                    <span class="state2">订单已提交待平台<span class="red">审核</span></span>
                    <p class="complete-top-tip state2">审核通过为您开通店铺续费，请耐心等待！</p>
                </div>
                <div class="complete-bottom">
                    <p>续费店铺：<span class="wid"></span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    续费服务：<span class="serviceVersion"><span></span></span>
                    &nbsp;&nbsp;&nbsp;
                    <span class="serviceTime"></span></p>
                    <p class="over-nav"><a href="tencent://message/?uin=1658349770&Site=&Menu=yes">在线客服</a>
                    <a href="{{ URL('/merchants/capital/fee/order/list') }}">我的订购</a>
                    <a href="/home/index/detail/791/help">帮助中心</a></p>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
@section('page_js')
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/payFinish.js"></script>
@endsection