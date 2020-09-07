@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_z5zddpqi.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">应用订购</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
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
        <!-- 套餐模块 开始 -->
        <div class="package_module display_box mgb30">
            <div class="img_wrap">
                <img src="{{ config('app.source_url') }}mctsource/images/pintuan-together.png" />
            </div>
            <div class="box_flex1 mgl30">
                <strong class="f14 mgb10">多人拼团</strong>
                <p class="mgb15">多人拼团是会搜云微商城推出的一款付费营销应用，活动基于多人组团形式，鼓励买家发起拼团，邀请好友以折扣价格购买优质商品，同时给店铺带来更好的传播效果。</p>
                <!-- 价格 -->
                <div class="display_box mgb10">
                    <p class="gray_999">服务价格：</p>
                    <p class="server_price box_flex1 orange_f60 f20">￥468.00 - ￥618.00</p>
                </div>
                <!-- 套餐 -->
                <div class="version_modal display_box mgb30">
                    <p class="gray_999">应用版本：</p>
                    <div class="version_items box_flex1">
                        <a href="javascript:void(0);" data-price="468.00" data-unitprice="78.00">6个月</a>
                        <a href="javascript:void(0);" data-price="618.00" data-unitprice="51.50">12个月</a>
                    </div>
                </div>
                <button type="button" class="order_btn btn btn-primary" data-toggle="modal" >立即订购</button>
            </div>
        </div>
        <!-- 套餐模块 结束 -->
        <!-- 导航模块 开始 -->
        <div class="display_box mgb15">
            <!-- 导航 开始 -->
            <ul class="module_nav">
                <li class="hover">
                    <a href="javascript:void(0);">应用详情</a>
                </li>
                <li>
                    <a href="javascript:void(0);">使用教程</a>
                </li>
            </ul>
            <!-- 导航 结束 -->
            <!-- 消息说明 开始 -->
            <a class="message_tip f12 box_flex1 gray_999" href="javascript:void(0);" target="_blank">拼团服务专线：0571-87796692，服务时间：10:00-18:00</a>
            <!-- 消息说明 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- 详情 开始 -->
        <div class="detail_des">
            图文详情
        </div>
        <!-- 详情 结束 -->
    </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection