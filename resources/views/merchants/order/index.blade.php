@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前模块公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_llbq22x2.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_kg1fntrz.css" />
@endsection
@section('slidebar')
@include('merchants.order.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">订单概况（订单处理延时？不存在的。去开通<a href="/merchants/notification/settingListView">消息通知</a>，及时订单提醒）</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    @if(in_array('view_order_price',session('permission')??[]))
    <!--头部-->
    <div class="order_info_header margin_20">
        <div class="border_right">
            <a class="block" href="{{ URL('/merchants/order/orderList') }}?start_time={{ $sevenDayBeforeStr }} 00:00:00&end_time={{ $yestodayStr }} 23:59:59">{{ $orderLogSevenDayList[1]['countTotal'] or '0' }}</a>
            <p>7天下单笔数</p>
        </div>
        <div class="border_right">
            <a href="{{ URL('/merchants/order/orderList?status=1') }}" class="block">{{ $orderStatisticalList['status'][0]['count'] or '0' }}</a>
            <p>待付款</p>
        </div>
        <div class="border_right">
            <a href="{{ URL('/merchants/order/orderList?status=2') }}" class="block">{{ $orderStatisticalList['status'][1]['count'] or '0' }}</a>
            <p>待发货</p>
        </div>
        <div class="border_right">
            <a href="{{ URL('/merchants/order/orderList?status=6') }}" class="block">{{ $orderRefundCount or '0'}}</a>
            <p>积压维权</p>
        </div>
        <div>
            <a class="block">{{ $orderLogSevenDayIcome['income'] or '0' }}</a>
            <p>7天收入-<a href="{{ URL('/merchants/capital/billDetail?') }}">详情</a></p>
        </div>
    </div>
    <!-- 带左边线公共区块 -->
    <div class="common_top">
        <div class="common_line"></div>
        <div class="common_title">7天订单趋势</div>
        <ul class="common_link">
        </ul>
        <div class="common_right">
            <i class="glyphicon glyphicon-question-sign"></i>
            <div class="explain_items">
                <p><strong>下单笔数：</strong>所有用户的下单总数。</p>
                <p><strong>付款订单：</strong>已付款的订单总数；</p>
                <p><strong>发货订单：</strong>已发货的订单总数。</p>
            </div>
        </div>
    </div>
    <!--尾部-->
    <div class="footer_content ui-block-border clearfix">
        <ul class="widget-chart-overview">
            <li>
                <h5>
                    <a href="{{ URL('/merchants/order/orderList') }}?start_time={{ $yestodayStr }} 00:00:00&end_time={{ $yestodayStr }} 23:59:59">{{ $orderLogOneDayList[1]['countTotal'] or '0' }}</a>
                </h5>
                <h6>昨日下单笔数</h6>
            </li>
            <li>
                <h5>
                    <a href="{{ URL('/merchants/order/orderList?status=2') }}&start_time={{ $yestodayStr }} 00:00:00&end_time={{ $yestodayStr }} 23:59:59">{{ $orderLogOneDayIcome['income'] or '0' }}</a>
                </h5>
                <h6>昨日付款订单</h6>
            </li>
        </ul>
        <div class="widget-chart-content">
            <div class="js-body-chart chart-body" id="echarts">
                <div class="widget-chart-no-data">暂无数据</div>
            </div>
        </div>
    </div>
     @else
            <h1>您无权限访问</h1>
    @endif
</div>
@endsection
@section('page_js')
<!-- 图表插件 -->
<script src="{{ config('app.source_url') }}static/js/echarts/echarts-all.js"></script>
<!-- 后台数据 -->
<script type="text/javascript">
    // 日期数据
    var _dateList = {!! $sevenDayJson or '[]' !!};
    // 下单笔数
    var _createOrderList = {!! $sevenCreateOrderJson or '[]' !!};
    // 付款订单
    var _payOrderList = {!! $sevenpayOrderJson or '[]' !!};
</script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/order_kg1fntrz.js"></script>
@endsection
