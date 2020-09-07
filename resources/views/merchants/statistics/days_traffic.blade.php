@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_8ig3tnqp.css" />
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
                <a href="{{ URL('/merchants/statistics/pagedata') }}">页面转化数据</a>
            </li>
            <li class="hover">
                <a href="javascript:void(0);">按每天流量分析</a>
            </li>
        </ul>
        <!-- 普通导航 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
    <!-- 进阶 开始 -->
    <a class="advanced_items blue_38f" href="javascript:void(0);">
        <i class="glyphicon glyphicon-question-sign"></i>从数据分析深入了解店铺运营（进阶）
    </a>
    <!-- 进阶 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!-- 时间筛选 开始 -->
    <div class="screen_items mgb15">
        筛选
    </div>
    <!-- 时间筛选 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">按天流量查看</p>
        <div class="common_link"></div>
        <div class="common_right">
            <i class="glyphicon glyphicon-question-sign"></i>
            <!-- 规则说明 开始 -->
            <div class="explain_items">
                <p class="explain_info">浏览UV：是指你微店铺店铺所有页面的访问人数;</p>
                <p class="explain_info">浏览PV：是指你微店铺所有页面的访问次数之和;</p>
                <p class="explain_info">到店UV：是指你店铺的商品详情页（即单品页）的访问人数;</p>
                <p class="explain_info">到店PV：是指你店铺的商品详情页（即单品页）的访问次数。</p>
            </div>
            <!-- 规则说明 结束 -->
        </div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 流量趋势图表 开始 -->
    <div id="data_chart" class="chart_items mgb15">
    </div>
    <!-- 流量趋势图表 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">详细数据</p>
        <div class="common_link"></div>
        <div class="common_right">
            <i class="glyphicon glyphicon-question-sign"></i>
            <!-- 规则说明 开始 -->
            <div class="explain_items">
                <p class="explain_info">浏览UV：是指你微店铺店铺所有页面的访问人数;</p>
                <p class="explain_info">浏览PV：是指你微店铺所有页面的访问次数之和;</p>
                <p class="explain_info">到店UV：是指你店铺的商品详情页（即单品页）的访问人数;</p>
                <p class="explain_info">到店PV：是指你店铺的商品详情页（即单品页）的访问次数。</p>
            </div>
            <!-- 规则说明 结束 -->
        </div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 表格 开始 -->
    <table class="table table-bordered table-hover table-striped">
        <tr class="active">
            <td>页面名称</td>
            <td>浏览UV/PV</td>
            <td>外部分享UV/PV</td>
            <td>导出UV/PV</td>
            <td>平均停留时间</td>
            <td>平均访问深度</td>
        </tr>
        <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
        </tr>
        <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
        </tr>
        <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
        </tr>
    </table>
    <!-- 表格 结束 -->
</div>
@endsection
@section('page_js')
<!-- 图表插件 -->
<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
<!-- 图片剪切插件 -->
<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_8ig3tnqp.js"></script>
@endsection