@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/css/orderList.css" />
@endsection
@section('slidebar')
    @include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{ URL('merchants/capital/fee/serviceList') }}">续费服务</a>
            </li>
            <li class="hover">
                <a href="javascript:void(0);">我的订购</a>
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
    <div class="table">
        <ul class="table-head clearfix">
            <li>续费时间</li>
            <li>续费店铺</li>
            <li>续费版本</li>
            <li>续费年限</li>
            <li>费用(元)</li>
            <li>支付方式</li>
            <li>状态</li>
            <li>操作</li>
        </ul>
        <p class="noData hide">暂无数据</p>
        <div class="table-body">
            
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script>
    var host = "{{ config('app.source_url') }}";
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/orderList.js"></script>
@endsection