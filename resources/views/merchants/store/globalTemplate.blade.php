@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_x1tza27i.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">全店风格</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <form name="colorForm">
        <div class="select_nav">
            <span>选择配色方案：</span>
            <ul class="project clearfix"></ul>
        </div>
        <!-- 当前风格开始 -->
        <div class="view">
            <span>当前风格示例：</span>
            <div class="view_img">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/1.png">
            </div>
        </div>
        <!-- 当前风格结束 -->
        <!-- 保存按钮开始 -->
        <div class="btn_group">
            <a class="btn btn-primary">保存</a>
        </div>
        <input class='inp' type="hidden" name="wid" value="">
        {{ csrf_field() }}
        <!-- 保存按钮结束 -->
    </form>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_x1tza27i.js"></script>
<script>
   console.log( $(".inp").val())
</script>
@endsection