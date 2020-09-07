@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_d6b3fbcx.css" />
    <!-- 二级分销 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_lkyabtwf.css" />
    <!-- 商品列表-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_nt4so0w7.css" />
    <!-- 推广效果 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_jxq53ucj.css" />
    <!-- 推广结算 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_hfakvjhz.css" />
    <!-- 关系查询 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_bd91kf6o.css" />
    <!-- 招募计划 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_kra6c86e.css" />
    <!-- 设置 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_sd8hmani.css" />


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
                    <a href="#&status=2">销售员</a>
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
        <!-- 巨幕模块 开始 -->
        <div class="faceplate_module mgb15">
            <div class="container-fluid">
                <!-- 巨幕内容 开始 -->
                <div class="faceplate_content col-sm-9">
                    <strong class="f18 gray_333 mgb10">销售员（免费版）</strong>
                    <p class="mgb10 f12">销售员是有商城推出的一款可帮助商家拓宽推广渠道的应用营销工具，分为“免费版”和“高级版”。商家通过制定推广计划招募买家加入推广队伍，并在其成功推广后给予奖励，以此给店铺带来更多传播和促进销量提升。</p>
                    <p class="mgb10 box_start">
                        <a class="blue_38f f12" href="应用订购.html" target="_blank">应用详情</a>
                        <a class="blue_38f f12" href="javascript:void(0);" target="_blank">使用教程</a>
                    </p>
                    <a class="btn btn-primary" href="应用订购.html" target="_blank">立即订购</a>
                </div>
                <!-- 巨幕内容 结束 -->
                <!-- 按钮 开始 -->
                <div class="col-sm-2">
                    <div class="switch_items">
                        <input type="checkbox" checked name="" value="" />
                        <label></label>
                    </div>
                </div>
                <!-- 按钮 结束 -->
            </div>
        </div>
        <!-- 巨幕模块 结束 -->
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix">
            <!-- 左侧 开始 -->
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li @if( $list == 'salesman' ) class="hover" @endif data="salesman">
                        <a href="{{URL('/merchants/marketing/salesman/salesman')}}">销售员</a>
                    </li>
                    <li @if( $list == 'second' ) class="hover" @endif data="second" >
                        <a href="{{URL('/merchants/marketing/salesman/second')}}" >二级销售</a>
                    </li>
                    <li @if( $list == 'goods' ) class="hover" @endif  data="goods"  >
                        <a href="{{URL('/merchants/marketing/salesman/goods')}}">商品列表</a>
                    </li>
                    <li @if( $list == 'result' ) class="hover" @endif data="result"  >
                        <a href="{{URL('/merchants/marketing/salesman/result')}}">推广效果</a>
                    </li>
                    <li @if( $list == 'balance' ) class="hover" @endif data="balance" >
                        <a href="{{URL('/merchants/marketing/salesman/balance')}}">推广结算</a>
                    </li>
                    <li @if( $list == 'relation' ) class="hover" @endif data="relation" >
                        <a href="{{URL('/merchants/marketing/salesman/relation')}}">关系查询</a>
                    </li>
                    <li @if( $list == 'plan' ) class="hover" @endif data="plan" >
                        <a href="{{URL('/merchants/marketing/salesman/plan')}}">招募计划</a>
                    </li>
                    <li @if( $list == 'set' ) class="hover" @endif data="set">
                        <a href="{{URL('/merchants/marketing/salesman/set')}}">设置</a>
                    </li>
                    <li @if( $list == 'poster' ) class="hover" @endif data="poster" >
                        <a href="{{URL('/merchants/marketing/salesman/poster')}}">海报管理</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【销售员】使用手册
                </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <div class="list" >
            @include('merchants.marketing.salesman.salesman')
        </div>
   </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_base_ezsv71hg.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_4wmaevks.js"></script>
    <!-- 商品列表 -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_7v4mxplx.js"></script>
    <!--关系查询 -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_ljs5c0g4.js"></script>
    <!-- 招募计划 -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_mxhw8jg9.js"></script>
    <!-- 设置 -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_fsuiyoqq.js"></script>
    <!-- 海报管理 -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_qk63b5fp.js"></script>
    <script type="text/javascript">
        
    </script>
@endsection