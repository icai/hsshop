@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_ecn2vit0.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_k5b4mg8h.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">模版市场</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!--模版市场 标题-->

    <!-- 导航模块 开始 -->
    <div class="nav_module clearfix">
        <!-- 左侧 开始 -->
        <div class="pull-left">
            <!-- （tab试导航可以单独领出来用） -->
            <!-- 导航 开始 -->
            <ul class="tab_nav">
                <li>
                    <a href="">模版市场</a>
                </li>
            </ul>
        </div>
        <div class="pull-right">
            <!-- 搜素框~~或者自己要写的东西 -->
            <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【模版市场】使用教程 
            </a>
        </div>
    </div>

    <div class="widget-list-filter clearfix">
        <div class="pull-right search_module">
            <label class="search_items">
                <input class="search_input" type="text" name="" value="" placeholder="搜索"/>   
            </label>
        </div>
    </div>
    <!-- 列表过滤部分 结束 -->

    <!--模版市场 图片列表-->
    <div class="img-item">  
        <a href="">
            <img src="{{ config('app.source_url') }}mctsource/images/img-item1.jpg" alt="" class="img">
            <h3 class="img-name">端午</h3>
            <div>
                <span class="price">价格 :</span>
                <span class="red currency">￥</span>
                <span class="red money">28元</span>
                <span class="btn-right">点击购买</span>
            </div>
        </a>
    </div>
    <div class="img-item">
        <a href="">
            <img src="{{ config('app.source_url') }}mctsource/images/img-item1.jpg" alt="" class="img">
            <h3 class="img-name">端午</h3>
            <div>
                <span class="price">价格 :</span>
                <span class="red currency">￥</span>
                <span class="red money">28元</span>
                <span class="btn-right">点击购买</span>
            </div>
        </a>
    </div>
    <div class="item-num">       
        <span>共2条，每页8条</span>
    </div>
</div>
@endsection
@section('page_js')
<!-- 图表插件 -->
<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/marketing_k5b4mg8h.js"></script>
@endsection