@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_i6lkinq9.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">不可用余额</div>
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
    <!-- 数据 开始 -->
    <table class="table table-condensed">
        <thead>
            <tr class="active">
                <td>业务类型</td>
                <td>交易流水</td>
                <td>金额(元)</td>
                <td>交易发生时间</td>
                <td>说明</td>
            </tr>
        </thead>
         <tr>
            <td>提现</td>
            <td>201213546987</td>
            <td>2124685</td>
            <td>2017-02-07</td>
            <td>asdjwqyi7jdskhfsudfybjhgkugogu</td>
        </tr>
    </table>
    
    <!-- 数据 结束 -->
    <!-- 流量趋势图表 开始 -->
    <div class="no_result">暂无数据</div>
    <!-- 流量趋势图表 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_i6lkinq9.js"></script>
@endsection