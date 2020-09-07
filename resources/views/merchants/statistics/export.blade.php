@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_yj3k2kwa.css" />
@endsection
@section('slidebar')
@include('merchants.statistics.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li>
                <a href="" style="color:#3396fb">交易分析</a>
            </li>
            <li>
                <a href="javascript:void(0);" style="color:#333333">商品分析</a>
            </li>
        </ul>
        <!-- 普通导航 结束 -->
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
    <div class="export_data_item">
        <div class="data_title clearfix">数据导出请求时间：
            <span class="item_date">2018-01-22</span>&nbsp;&nbsp;<span class="item_time">12:00:00</span>
            <p class="data_export_user pull-right">导出人<span>13000000000</span>&nbsp;&nbsp;<span class="data_export_person">13000000000</span></p>
        </div>
        <div class="export_data_content">
            <p>
            数据筛选时间：<span>2018-01-22</span>&nbsp;至&nbsp;<span>2018-3-18</span>
            </p>
            <div class="clearfix">
            <span class="pull-left">数据导出项：</span>
            <ul class="clearfix pull-left">
                <li>商品名称</li>
                <li>商品价格（单位：元）</li>
                <li>曝光次数</li>
                <li>曝光人数</li>
                <li>浏览次数</li>
                <li>浏览人数</li>
                <li>付款商品件数</li>
                <li>付款金额（单位：元）</li>
            </ul>
            </div>
        </div>
        <div class="data_download">
            <input type="button" value="下载数据报表"/>
        </div>
    </div>
    
    <div class="loading_more">
        <p><a href="javascript:;">加载更多</a></p>
    </div>
</div>
@endsection
@section('page_js')



<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_yj3k2kwa.js"></script>
<!-- 获取后台数据 -->

@endsection


