@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_i8y27jkv.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">

   <!-- <link rel="stylesheet" type="text/css" href="public/hsadmin/css/酒店预订.css" /> -->
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
                    <a href="javascript:void(0)">酒店预订</a>
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
        <!-- 筛选模块 开始 -->
        <div class="screen_module">
            <button class="batch_btn btn btn-primary" type="button">批量设置</button>
            <!-- 时间筛选 开始 -->
            <div class="screen_items box_start">
                <a class="screen_day btn btn-default" href="javascript:void(0);" data-day="-10"><前10天</a>
                <div class="form-group box_flex1">
                    <div class='input-group date' id='screen_day'>
                        <input id="select_day" class="form-control" type='text' value="" data-day="2016-12-11" />
                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                    </div>
                </div>
                <a class="screen_day btn btn-default" href="javascript:void(0);" data-day="10">后10天></a>
            </div>
            <!-- 时间筛选 结束 -->
        </div>
        <!-- 筛选模块 结束 -->
        <!-- 表格模块 开始 -->
        <table class="table table-hover f12">
            <thead>
            <tr class="active">
                <td class="w200">房间名</td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
                <td class="day_th">
                    12-15<br/>周四
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="house_name">
                    <input type="checkbox" name="" value="" />
                    <a class="base_name blue_38f" href="javascript:void(0);">测试产品</a>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 3.00</p><p>4</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
            </tr>
            <tr>
                <td class="house_name">
                    <input type="checkbox" name="" value="" />
                    <a class="base_name blue_38f" href="javascript:void(0);">测试产品3</a>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
                <td class="base_cell active">
                    <p>关</p><p>¥ 2.00</p><p>2</p>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- 表格模块 结束 -->
    </div>

@endsection

@section('page_js')
    <!--主要内容js文件-->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_discount.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/marketing_lry0qllf.js"></script>
@endsection