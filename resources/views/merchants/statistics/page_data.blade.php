@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
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
                <a href="{{ URL('/merchants/statistics/daystraffic') }}">按每天流量分析</a>
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
    <div class="screen_items mgb15"></div>
    <!-- 时间筛选 结束 -->
    <!-- 浏览量展示 开始 -->
    <div class="pageviews_items">
        <a class="w50 fon20" href="javascript:void(0);">
            基础数据
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span class="blue_38f">2</span>微面页数
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a class="empty" href="javascript:void(0);">
            <span>暂无</span>平均停留时间
        </a>
        <a class="empty" href="javascript:void(0);">
            <span>暂无</span>平均访问深度
        </a>
    </div>
    <div class="pageviews_items mgb15">
        <a class="w50 fon20" href="javascript:void(0);">
            转化数据
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span class="blue_38f">2</span>微面页数
        </a>
        <a href="javascript:void(0);">
            <span>0</span>昨日浏览PV
        </a>
        <a href="javascript:void(0);">
            <span>暂无</span>平均停留时间
        </a>
        <a href="javascript:void(0);">
            <span>暂无</span>平均访问深度
        </a>
    </div>
    <!-- 浏览量展示 结束 -->
    <!-- 页面类型&访问来源 开始 -->
    <div class="type_source mgb15">
        <!-- 页面类型 开始 -->
        <div class="type_items">
            <!-- 区域标题 开始 -->
            <div class="common_top mgb15">
                <span class="common_line"></span>
                <p class="common_title">页面类型</p>
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
            <div id="type_chart" class="chart_items"></div>
            <!-- 流量趋势图表 结束 -->
        </div>
        <!-- 页面类型 结束 -->
        <!-- 访问来源 开始 -->
        <div class="source_items">
            <!-- 区域标题 开始 -->
            <div class="common_top mgb15">
                <span class="common_line"></span>
                <p class="common_title">访问来源</p>
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
            <div id="source_chart" class="chart_items"></div>
            <!-- 流量趋势图表 结束 -->
        </div>
        <!-- 访问来源 结束 -->
    </div>
    <!-- 页面类型&访问来源 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">数据趋势</p>
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
        <div class="no_result">暂无数据</div>
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
    <table class="table table-bordered">
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
        <tr><td colspan="6">暂无数据</td></tr>
    </table>
    <!-- 表格 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">访客地域分布</p>
        <div class="common_link">
            <a class="blue_00f" href="javascript:void(0);" target="_blank">详细》</a>
        </div>
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
    <div id="visitor_chart" class="chart_items mgb15"></div>
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
    <!-- 流量趋势图表 开始 -->
    <div id="detail_chart" class="chart_items mgb15"></div>
    <!-- 流量趋势图表 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_uey1am0t.js"></script>
@endsection