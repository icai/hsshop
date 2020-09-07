@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_uey1am0t.css" />
@endsection
@section('slidebar')
@include('merchants.statistics.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="javascript:void(0);">页面转化数据</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/statistics/shops/dailyData') }}">按每天流量分析</a>
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
    <!-- 时间筛选 开始 -->
    <div class="screen_items mgb15 clearfix shop_top_title">
        <div class="pull-left font34">筛选日期：</div>
        <div id="start_time" class="input-group pull-left" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
            <input class="form-control shop_timer_input" type="text" value="" size="16">
            <span class="input-group-addon shop_timer">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
        <div class="pull-left font34"> 至 </div>
        <div id="end_time" class="input-group pull-left" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
            <input class="form-control shop_timer_input" type="text" value=""> 
            <span class="input-group-addon shop_timer">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
        <div class="pull-left shop_timer_btn">
            <input class="btn btn-primary btn-sm fastSelect_time" type="button" value="近7天"/>
            <input class="btn btn-primary btn-sm fastSelect_time" type="button" value="近30天"/>
        </div>
        <input class="btn btn-primary btn-sm" id="filter" type="button" value="筛选"/>
    </div>

    <!-- 时间筛选 结束 -->

    <!-- 数据呈现(表格) 开始-->
    <div class="screen_items row">
        <div class="col-md-3">
            <dl class="table_data_appear">
                <dt>
                访客数
                <!-- 提示呈现 开始 -->
                <div class="table_item_title">
                <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                    <p class="table_item_tips">
                        <em class="tip_mark"></em>筛选时间内，店铺所有页面（包括店铺主页、单品页、会员主页等）被访问的人数。一个人在同一天内访问多次只记为一人。</p>
                </div>
                <!-- 提示呈现 结束 -->
                </dt>
                <dd>暂无数据</dd>
            </dl>
        </div>
        
        <div class="col-md-3">
            <dl class="table_data_appear">
                <dt>
                浏览量
                <!-- 提示呈现 开始 -->
                <div class="table_item_title">
                <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                    <p class="table_item_tips">
                        <em class="tip_mark"></em>筛选时间内，店铺所有页面（包括店铺主页、单品页、会员主页等）被访问的次数，一个人在筛选时间内访问多次记为多次。</p>
                </div>
                <!-- 提示呈现 结束 -->
                </dt>
                <dd>暂无数据</dd>
            </dl>
        </div>

        <div class="col-md-3">
            <dl class="table_data_appear">
                <dt>
                商品访客数
                <!-- 提示呈现 开始 -->
                <div class="table_item_title">
                <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                    <p class="table_item_tips item_tips_right">
                        <em class="tip_mark"></em>筛选时间内，商品详情页被访问的人数。一个人在同一天内访问多次只记为一人。</p>
                </div>
                <!-- 提示呈现 结束 -->
                </dt>
                <dd>暂无数据</dd>
            </dl>
        </div>

        <div class="col-md-3">
            <dl class="table_data_appear">
                <dt>
                商品浏览量
                <!-- 提示呈现 开始 -->
                <div class="table_item_title item_tips_rightist">
                <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                    <p class="table_item_tips">
                        <em class="tip_mark"></em>筛选时间内，商品详情页被访问的次数，一个人在筛选时间内访问多次记为多次。</p>
                </div>
                <!-- 提示呈现 结束 -->
                </dt>
                <dd>暂无数据</dd>
            </dl>
        </div>
    </div>
    <!-- 数据呈现(表格) 结束-->

    <!-- 页面类型区域标题 开始 -->
    <div class="common_top">
        <span class="common_line"></span>
        <div class="common_title">
            页面类型
            <div class="table_item_title">
                <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                <p class="table_item_tips">
                    <em class="tip_mark"></em>筛选日期区间内用户访问各种页面类型的人数。
                </p>
            </div>
        </div>
        <div class="common_link"></div>
    </div>
    <!-- 页面类型区域标题 结束 -->

    <!-- 页面类型饼图 开始 -->

    <div id="pie_data"></div>

    <!-- 页面类型饼图 结束 -->

    <!-- 单页面流量数据标题 开始 -->
    <div class="common_top">
        <span class="common_line"></span>
        <div class="common_title">
            单页面流量数据
            <span class="f12 c_gray"></span>
        </div>
        <div class="common_link"></div>    
    </div>
    <!-- 单页面流量数据标题 结束 -->

    <!-- 单页面流量数据表格 开始 -->
    
    <div class="table_content clearfix">
        <div class="table_body">
            <ul class="thead">
                <!-- <li>页面名称</li> -->
                <li>页面类型</li>
                <li>浏览量</li>
                <li>访客数</li>
            </ul>
            <div class="clearfix" id="table_manage">
            </div>
        </div>


        <ul class="pagination pull-right">
            <!-- <li><a href="javascript:;">1</a></li> -->       
        </ul>
    </div>
    <!-- 单页面流量数据表格 结束 -->
</div>
@endsection
@section('page_js')

<!-- 获取后台数据 -->
<script>
    var userApp = "{{ config('app.dc_url')}}";
    var wid = "{{$wid}}";
</script>

<!-- 图表控件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/echarts/echarts-now.js"></script>
<!-- 时间控件js -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_uey1am0t.js"></script>
<!-- 获取后台数据 -->

@endsection


