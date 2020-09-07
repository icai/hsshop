@extends('merchants.default._layouts')
@section('head_css')
<!-- 公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_kwvhib03.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_4l02qa8b.css" />
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/product/importGoods') }}">外部商品导入</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/product/importMaterial') }}">导入商品素材</a>
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
    <div class="widget-app-board ui-box1">
        <div class="widget-app-board-info">
            <div>
                <p>把您的淘宝店、天猫店、京东店等其他平台商品，一键导入会搜云微商城。
                    <a href="javascript:void(0);" target="_blank">点击查看教程</a>
                </p>
            </div>
        </div>
    </div>
    <div class="step_progress">
        <div class="order_progress">
            <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
            <div class="stepIco stepIco1" id="create">
                1<div class="stepText step" id="createText">选择服务商</div>
            </div>
            <div class="stepIco stepIco2" id="check">
                2<div class="stepText step" id="checkText">授权</div>
            </div>
            <div class="stepIco stepIco3" id="produce">
                3<div class="stepText step" id="produceText">批量导入商品</div>
            </div>
            <div class="stepIco stepIco4" id="delivery">
                4<div class="stepText step" id="deliveryText">完成</div>
            </div>
        </div>
    </div>
    <div class="ui-box" style="background: rgb(248, 248, 248); padding: 20px; margin-top: 20px;">
        <div class="widget-goods-klass">
            <div class="widget-goods-klass-item current">
                <span class="widget-goods-klass-name">软香蕉</span>
            </div>
            <div class="widget-goods-klass-item">
                <span class="widget-goods-klass-name">云商店</span>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button class="zent-btn zent-btn-primary">下一步</button>
    </div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_4l02qa8b.js"></script>
@endsection
