@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_ha8jbxjz.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/capital/myService') }}">我的服务</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/orderRecord') }}">订购记录</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
    <!-- 记录条 开始 -->
    <div class="section_common mgb30 f12">
        <div class="zent-alert-content">店铺已订购服务<span>0</span>个，即将到期服务<span class="red">0</span>个，已过期服务<span class="red">0</span>个</div>  
    </div>
    <!-- 记录条 结束 -->
    <!-- 导航 开始 -->
    <ul class="nav nav-tabs mgb30" role="tablist">
        <li role="presentation" class="active">
            <a href="javascript:void(0);">所有服务</a>
        </li>
        <li role="presentation">
            <a href="javascript:void(0);">即将到期</a>
        </li>
        <li role="presentation">
            <a href="javascript:void(0);">已过期</a>
        </li>
    </ul>
    <!-- 导航 结束 -->
    <!-- 列表 开始 -->
    <table class="table table-bordered table-hover">
        <tr class="active">
            <td>服务名称</td>
            <td>服务类型</td>
            <td>到期时间</td>
            <td>服务状态</td>
            <td>操作</td>
        </tr>
        <tr>
            <td colspan="5">
                没有相关服务记录哟～去
                <a class="blue_38f"　href="服务订购.html">服务市场</a>
                逛逛吧
            </td>
        </tr>
    </table>
    <!-- 列表 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_ha8jbxjz.js"></script>
@endsection