@extends('shop.common.template')
@section('head_css')
    <!--当前页面css引入-->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_detail.css"/>
@endsection
@section('main')
    <!-- 中间内容开始 -->
    <div class="datails">
        <!-- 标题开始 -->
        <div class="title">
            <h2>这是标题</h2>
        </div>
        <!-- 标题结束 -->
        <!-- 副标题开始 -->
        <div class="subhead">
            <span class="set_time">{{ $detail['title'] }}</span>
            <span class="author">{{ $detail['author'] }}</span>
            <a class="mySpace" href="javascript:void(0);">{{ $weixinInfo['shop_name'] }}</a>
        </div>
        <!-- 副标题结束 -->
        <!-- 内容开始 -->
        <div class="page_content">
            <div class="img_content">
                <img src="{{ $detail['cover'] }}">
            </div>
            <div class="main_content">
                <p>{!! $detail['content'] !!}</p>
            </div>
            <div class="read_all">
                <a href="{$detail['content_source_url']}">阅读原文</a>
            </div>
        </div>
        <!-- 内容结束 -->
    </div>
    <!-- 中间内容结束 -->
    
    <!-- 点击我的空间二维码显示 -->
    <div class="show_code">
        <div class="codeTwo">
            <p>
                通过微信【扫一扫】功能
                <br/>
                扫描二维码关注我们
            </p>
            <img src="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=730492331,2975856557&fm=21&gp=0.jpg">
        </div>
    </div>
    <!-- 点击我的空间二维码显示 -->
    <!-- 右侧内容开始 -->
    <div class="right_code">
        <div class="control">
            <div class="code">
                <img src="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=730492331,2975856557&fm=21&gp=0.jpg">
                <p>
                    微信扫一扫
                    <br/>
                    获得更多内容
                </p>
            </div>
        </div>
    </div>
    <!-- 右侧内容结束 -->
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/wechat_detail.js"></script>
@endsection
