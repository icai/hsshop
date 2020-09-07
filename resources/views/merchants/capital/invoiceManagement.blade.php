@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_b6bgn0vk.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">我的收入</div>
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
    <!-- 个人中心 开始 -->
    <div class="personal_center mgb15">
        <!-- 个人信息 开始 -->
        <div class="personal_items display_box">
            <!-- logo 开始 -->
            <div class="img_wrap">
                <img src="{{ config('app.source_url') }}mctsource/images/1.jpg" />
            </div>
            <!-- logo 结束 -->
            <!-- 信息 开始 -->
            <div class="personal_content box_flex1 mgl10">
                <div class="info_list display_box f12">
                    <span class="info_name">店铺名称：</span>
                    <p class="items_title box_flex1">lkasl</p>
                </div>
                <div class="info_list display_box f12">
                    <span class="info_name">认证类型：</span>
                    <p class="items_title box_flex1">企业认证 <span>（待认证 审核中 认证失败）</span></p>
                </div>
                <div class="info_list display_box f12">
                    <span class="info_name">认证信息：</span>
                    <p class="items_title box_flex1"> 陈陈陈有限公司 <a class="blue_38f" href="立即认证.html">立即认证</a><a class="blue_38f" href="店铺认证.html">查看进度</a></p>
                </div>
            </div>
            <!-- 信息 结束 -->
        </div>
        <!-- 个人信息 结束 -->
        <!-- 个人资金 开始 -->
        <ul class="money_items display_box f12">
            <li class="box_flex1">
                <p class="items_title gray_999">7天收入（截至今日0点）</p>
                <p class="money_bottom"><span class="orange_f60 f20">0.00</span> 元</p>
            </li>
            <li class="box_flex1">
                <p class="items_title gray_999">待结算（担保交易）</p>
                <p class="money_bottom"><span class="orange_f60 f20">0.00</span> 元</p>
            </li>
            <li class="box_flex1">
                <p class="items_title gray_999">可用余额</p>
                <p class="money_bottom"><span class="orange_f60 f20">0.00</span>元</p>
            </li>
        </ul>
        <!-- 个人资金 结束 -->
    </div>
    <!-- 个人中心 结束 -->
    <!-- 下载 开始 -->
    <div class="down_items mgb15 f12">
        <p class="mgb30 mgb15">微信扫一扫，下载会搜云微商城APP，进入“店铺营收”，即可索取发票 <a class="blue_38f" href="javascript:void(0);" target="_blank">会搜云发票开票须知</a></p>
        <!-- 扫码下载 开始 -->
        <div class="down_content display_box">
            <div class="box_flex1">
                <p>IOS版</p>
                <a class="img_wrap" href="javascript:void(0);" target="_blank">
                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/b23670bf30772e24776d5db99c7043f1.png" />
                </a>
                <a class="blue_38f" href="javascript:void(0);" target="_blank">App Store</a>
            </div>
            <div class="box_flex1 mgl10">
                <p>Android版</p>
                <a class="img_wrap" href="javascript:void(0);" target="_blank">
                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/b23670bf30772e24776d5db99c7043f1.png" />
                </a>
                <a class="blue_38f" href="javascript:void(0);" target="_blank">直接下载</a>
            </div>
        </div>
        <!-- 扫码下载 结束 -->
    </div>
    <!-- 下载 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">发票索取记录</p>
        <div class="common_link"></div>
        <div class="common_right">
            <a class="blue_38f" href="javascript:void(0);">会搜云不加收手续费的说明 </a>
            <a class="blue_38f" href="javascript:void(0);">| 支付方式设置</a>
        </div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 流量趋势图表 开始 -->
    <div class="no_result mgb15">暂无数据</div>
    <!-- 流量趋势图表 结束 -->
</div>
@endsection
@section('page_js')
<!-- 图片剪切插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_b6bgn0vk.js"></script>
@endsection