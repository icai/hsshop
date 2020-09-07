@extends('merchants.default._layouts') @section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/statistics_uynh7ai2.css" /> @endsection @section('slidebar') @include('merchants.microforum.slidebar') @endsection @section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="javascript:;">社区统计</a>
            </li>
        </ul>
    </div>
</div>
@endsection @section('content')
<div class="content">
    <div class="content-title">社区当月用户统计</div>
    <div class="content-tips">
        <label>活跃数：</label>
        当天访问量总数
    </div>
    <div class="content-search">
        按月份查看
        <select class="form-control iblock w100" id="year">
            <option value="2014">2014</option>
        </select>
        <select class="form-control iblock w100" id="month">
            <option value="1">1月</option>
            <option value="2">2月</option>
            <option value="3">3月</option>
            <option value="4">4月</option>
            <option value="5">5月</option>
            <option value="6">6月</option>
            <option value="7">7月</option>
            <option value="8">8月</option>
            <option value="9">9月</option>
            <option value="10">10月</option>
            <option value="11">11月</option>
            <option value="12">12月</option>
        </select>
    </div>
    <div class="widget-chart-content">
        <div class="js-body-chart chart-body" id="echarts">
        </div>
    </div>
</div>
@endsection @section('page_js')
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<!-- 图表插件 -->
<script src="{{ config('app.source_url') }}static/js/echarts/echarts-all.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/statistics_list.js"></script>
@endsection