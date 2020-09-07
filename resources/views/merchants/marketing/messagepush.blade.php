@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_nj9u3ofm.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0);">消息推送</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>   
        <!-- 三级导航 结束 -->
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
    <!-- 消息推送头部 开始 -->
    <div class="ad_header mgb15">
        <strong class="f16 mgb15">消息推送</strong>
        <p class="f12">消息推送功能可以让您通过短信和微信公众号，给买家推送交易和物流相关的提醒消息，包括订单催付、发货、签收、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。支付成功、供应商订单、采购单、维权的短信目前仍由会搜云免费发送</p>
    </div>
    <!-- 消息推送头部 结束 -->
    <!-- 横幅 开始 -->
    <div class="ad_banner red mgb15 f12">
        您店铺当前剩余短信条数为0啦，赶快去充值吧！ <a class="blue_38f" href="{{URL('/merchants/marketing/msgrecharge')}}">立即充值</a>
    </div>
    <!-- 横幅 结束 -->
    <!-- 导航模块 开始 -->
    <div class="display_box mgb15">
        <!-- 导航 开始 -->
        <ul class="module_nav">
            <li class="hover">
                <a>消息推送</a>
            </li>
            <li>
                <a href="{{URL('/merchants/marketing/pushstatistics')}}">推送统计</a>
            </li>
            <li>
                <a href="{{URL('/merchants/marketing/msgrecharge')}}">短信充值</a>
            </li>
        </ul>
        <!-- 导航 结束 -->
        <!-- 消息说明 开始 -->
        <a class="message_tip f12 box_flex1 blue_38f" href="javascript:void(0);" target="_blank">
            <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程 
        </a>
        <!-- 消息说明 结束 -->
    </div>
    <!-- 导航模块 结束 -->
    <!-- 消息推送模块 开始 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">加入流程</p>
        <div class="common_link f12">
            <span class="gray_ccc">(更新时间：{{$msgpush_total_stat['updated_at']}})</span>
            <span class="upload_btn blue_38f">刷新</span>
        </div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 数量展示 开始 -->
    <ul class="show_items mgb15">
        <li>
            <p class="blue_38f f22">{{$msgpush_total_stat['total_send']}}</p>
            <p class="f12">总发送条数</p>
        </li>
        <li>
            <p class="f22">{{$msgpush_total_stat['total_achieve']}}</p>
            <p class="f12">成功到达量</p>
        </li>
        <li>
            <p class="red f22">{{$msgpush_total_stat['total_fee']}}</p>
            <p class="f12">计费数量</p>
        </li>
        <li>
            <p class="f22">{{$msgpush_total_stat['total_left']}}</p>
            <p class="f12">剩余短信</p>
        </li>
        <li>
            <a class="recharge_btn btn btn-green" href="{{URL('/merchants/marketing/msgrecharge')}}">短信充值</a>
        </li>
    </ul>
    <!-- 数量展示 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">交易物流提醒</p>
        <div class="common_link"></div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items">
        <li>
            <a href="{{URL('/merchants/marketing/expediting')}}">
                <p class="f18 blue_38f mgb10">订单催付</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/paysuccess')}}">
                <p class="f18 blue_38f mgb10">付款成功通知</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">已启用 <span>（免费赠送）</span> </p>
                    <span class="list_set blue_38f">设置</span>
                </div>
                <div class="list_tip">
                    买家付款成功后的短信提醒，目前由会搜云<span class="orange_f70">免费发送</span>。此部分短信不计费。
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/sendnotice')}}">
                <p class="f18 blue_38f mgb10">发货提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/signnotice')}}">
                <p class="f18 blue_38f mgb10">签收提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/agreerefund')}}">
                <p class="f18 blue_38f mgb10">商家同意退款</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/disagreerefund')}}">
                <p class="f18 blue_38f mgb10">商家拒绝退款</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/takeorder')}}">
                <p class="f18 blue_38f mgb10">接单提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/distakeorder')}}">
                <p class="f18 blue_38f mgb10">拒绝接单提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/verifynotice')}}">
                <p class="f18 blue_38f mgb10">核销提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">营销关怀提醒</p>
        <div class="common_link"></div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items">
        <li>
            <a href="{{URL('/merchants/marketing/getvipcard')}}">
                <p class="f18 blue_38f mgb10">获得会员卡提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/vipupgrade')}}">
                <p class="f18 blue_38f mgb10">会员卡升级提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/salemanrelation')}}">
                <p class="f18 blue_38f mgb10">销售员关系通知</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/salemanorder')}}">
                <p class="f18 blue_38f mgb10">销售员订单通知</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/viprecharge')}}">
                <p class="f18 blue_38f mgb10">会员储值成功提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
        <li>
            <a href="{{URL('/merchants/marketing/banlancechange')}}">
                <p class="f18 blue_38f mgb10">储值余额变动提醒</p>
                <div class="display_box f12 red">
                    <p class="box_flex1">未启用</p>
                    <span class="list_set blue_38f">设置</span>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    <!-- 消息推送模块 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/marketing_nj9u3ofm.js"></script>
@endsection