@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
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
                <a href="{{ URL('/merchants/statistics/shops/index') }}">页面转化数据</a>
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
</div>
@endsection
@section('content')
<div class="content">
    <!-- 时间筛选 开始 -->
    <div class="screen_items mgb15 clearfix shop_top_title">
        <div class="pull-left font34">筛选日期：</div>
        <div id="date_start_time" class="input-group pull-left" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
            <input class="form-control shop_timer_input" type="text" value="" size="16">
            <span class="input-group-addon shop_timer">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
        <div class="pull-left font34"> 至 </div>
        <div id="date_end_time" class="input-group pull-left" data-date="12-02-2012" data-date-format="dd-mm-yyyy">
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
    <!-- 区域标题 开始 -->
    <div class="common_top">
        <span class="common_line"></span>
        <div class="common_title">
            按每天流量查看
            <div class="table_item_title">
                <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                <div class="title_tips">
                    <em class="item_tips_mark"></em>
                    <p class="table_item_tips">
                        <strong>浏览量：</strong>筛选时间内，店铺所有页面（包括店铺主页、单品页、会员主页等）被访问的次数，一个人在筛选时间内访问多次记为多次。
                    </p>
                    <p class="table_item_tips">
                        <strong>访客数：</strong>筛选时间内，店铺所有页面（包括店铺主页、单品页、会员主页等）被访问的人数。一个人在同一天内访问多次只记为一人。
                    </p>
                    <p class="table__item_tips">
                        <strong>商品浏览量：</strong>筛选时间内，商品详情页被访问的次数，一个人在筛选时间内访问多次记为多次。
                    </p>
                    <p class="table__item_tips">
                        <strong>商品访客数：</strong>筛选时间内，商品详情页被访问的人数。一个人在同一天内访问多次只记为一人。
                    </p>
                </div>
            </div>
        </div>
        <div class="common_link"></div>
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
    <div class="table_content clearfix">
        <div class="table_body clearfix">
            <ul class="thead clearfix">
                <li>
                    日期
                </li>
                <li>
                    浏览量
                    <div class="table_item_title">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <div class="title_tips">
                            <em class="item_tips_mark"></em>
                            <p class="table_item_tips">
                                <strong>浏览量：</strong>筛选时间内，店铺所有页面（包括店铺主页、单品页、会员主页等）被访问的次数，一个人在筛选时间内访问多次记为多次。
                            </p>
                        </div>
                    </div>
                </li>
                <li>
                    访客数
                    <div class="table_item_title">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <div class="title_tips">
                            <em class="item_tips_mark"></em>
                            <p class="table_item_tips">
                                <strong>访客数：</strong>筛选时间内，店铺所有页面（包括店铺主页、单品页、会员主页等）被访问的人数，日去重多天累加。一个人在同一天内访问多次只记为一人。
                            </p>
                        </div>
                    </div>
                </li>
                <li>
                    商品浏览量
                    <div class="table_item_title">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <div class="title_tips">
                            <em class="item_tips_mark"></em>
                            <p class="table_item_tips">
                                <strong>商品浏览量：</strong>筛选时间内，商品详情页被访问的人数，日去重多天累加。一个人在同一天内访问多次只记为一人。
                            </p>
                        </div>
                    </div>
                </li>
                <li>
                    商品访客数
                    <div class="table_item_title">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <div class="title_tips">
                            <em class="item_tips_mark"></em>
                            <p class="table_item_tips">
                                <strong>商品访客数：</strong>筛选时间内，商品详情页被访问的人数，日去重多天累加。一个人在同一天内访问多次只记为一人。
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="daily_table_content">
                
            </div>
        </div>


        <!-- <ul class="pagination pull-right">
            <li>
            <a href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
            </li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li>
            <a href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
            </li>
        </ul> -->
    </div>
    <!-- 单页面流量数据表格 结束 -->
</div>
    <!-- 表格 结束 -->
</div>
<script>
    var userApp = "{{ config('app.dc_url')}}";
    var wid = "{{$wid}}";
</script>

@endsection
@section('page_js')
<!-- 图表控件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/echarts/echarts-now.js"></script>
<!-- 时间控件js -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 图片剪切插件 -->
<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_8ig3tnqp.js"></script>
@endsection