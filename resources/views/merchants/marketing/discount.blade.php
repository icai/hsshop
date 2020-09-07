@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_discount.css" />
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
                    <a href="javascript:void(0)">限时折扣</a>
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
        <!--顶部导航内容-->
        <div class="content_top">
            <ul>
                <li class="active">所有促销</li>
                <li>未开始</li>
                <li>进行中</li>
                <li>已结束</li>
            </ul>
            <a href="##" class="tutorial"><span id="icon">?</span> 查看【限时折扣】使用帮助</a>
        </div>
        <!--中部主要内容-->
        <div class="content_center">
            <a href="{{ URL('/merchants/marketing/discountAdd') }}" class="btn btn-success" style="color: white;">新建限时折扣</a>
            <input type="text" name="" id="search" class="form-control" placeholder="搜索" />
            <div id="FDimg"></div>
        </div>
        <!--下部主要内容-->
        <div class="content_bottom B_active" id="bottom_1">
            <p>还没有相关数据1</p>
        </div>
        <div class="content_bottom" id="bottom_2">
            <p>还没有相关数据2</p>
        </div>
        <div class="content_bottom" id="bottom_3">
            <p>还没有相关数据3</p>
        </div>
        <div class="content_bottom" id="bottom_4">
            <p>还没有相关数据4</p>
        </div>
    </div>

@endsection

@section('page_js')
    <!--主要内容js文件-->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_discount.js" type="text/javascript" charset="utf-8"></script>
@endsection