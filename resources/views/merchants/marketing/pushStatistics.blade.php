@extends('merchants.default._layouts')
@section('head_css')
 <!-- 时间插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_l480swem.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0);">消息推送</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>   
        <!-- 三级导航 结束 -->
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
                <!-- 巨幕 开始 -->
                <div class="faceplate_module mgb15">
                    <!-- 巨幕 结束 -->
                    <div class="faceplate_module mgb15">
                        <div class="container-fluid">
                            <!-- 巨幕内容 开始 -->
                            <div class="faceplate_content col-sm-9">
                                <strong class="f16 mgb15">消息推送</strong>
                                <p class="f12">消息推送功能可以让您通过短信和微信公众号，给买家推送交易和物流相关的提醒消息，包括订单催付、发货、签收、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。支付成功、供应商订单、采购单、维权的短信目前仍由会搜云免费发送</p>
                            </div>
                            <!-- 巨幕内容 结束 -->
                        </div>
                    </div>
                </div>
                <!-- 横幅 开始 -->
                <div class="message_notice_warning red">
                    您店铺当前剩余短信条数为0啦，赶快去充值吧！ <a class="blue_38f" href="{{URL('/merchants/marketing/msgrecharge')}}">立即充值</a>
                </div>
                <!-- 横幅 结束 -->
                <!-- 导航模块 开始 -->
                <div class="nav_module clearfix">
                    <!-- 左侧 开始 -->
                    <div class="pull-left">
                        <!-- 导航 开始 -->
                        <ul class="tab_nav">
                            <li>
                                <a href="{{URL('/merchants/marketing/messagepush')}}">消息推送</a>
                            </li>
                            <li class="hover">
                                <a>推送统计</a>
                            </li>
                            <li>
                               <a href="{{URL('/merchants/marketing/msgrecharge')}}">短信充值</a>
                            </li>
                        </ul>
                        <!-- 导航 结束 -->
                    </div>
                    <!-- 左侧 结算 -->
                    <!-- 右边 开始-->
                    <div class="pull-right">
                        <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                            <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程
                        </a>
                    </div>
                    <!-- 右边 结束 -->
                </div>
                <!-- 导航模块 结束 -->
                <!-- 数据检索 开始 -->
                <div class="screen_module">
                    <form class="form-horizontal f12" action="" method="post">
                        <!-- 验证时间： -->
                        <div class="form-group">
                            <label class="col-sm-1 control-label">筛选日期：</label>
                            <div class="col-sm-3 center_start">
                                <!-- 开始时间 -->
                                <div id="start_time" class="input-group">
                                    <input class="form-control" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                &nbsp;&nbsp;至
                            </div>
                            <div class="col-sm-3">
                                <!-- 结束时间 -->
                                <div id="end_time" class="input-group">
                                    <input class="form-control" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <span class="f12">快速查询:</span>&nbsp;&nbsp;
                                <a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="7">最近7天</a>
                                &nbsp;<a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="30">最近30天</a>
                            </div>
                        </div>
                        <!--  状态 -->
                        <div class="form-group">
                            <label class="col-sm-1 control-label"></label>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-primary">筛选</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 数据检索 结束 -->
                <table class="table table-bordered table-hover">
                    <tr class="active">
                        <td>日期</td>
                        <td>总发送量</td>
                        <td>成功到达量</td>
                        <td>计费量</td>
                        <td>成功到达来源</td>
                    </tr>
                    @if($msgpush_day_stat)
                        @foreach($msgpush_day_stat as $v)
                        <tr>
                            <td>{{substr($v['created_at'],0,10)}}</td>
                            <td>{{$v['day_total_send']}}</td>
                            <td>{{$v['day_total_achieve']}}</td>
                            <td>{{$v['day_total_fee']}}</td>
                            <td>{{$v['achieve_source'] == 1 ? '短信': '微信模板'}}</td>
                        </tr>
                        @endforeach
                    @endif
                </table>
                @if(!$msgpush_day_stat)
                <div class="no_result">暂无数据</div>
                @else
                 <div class="js-has-company">
                        <div class="pagenavi js-pagenavi">
                           {{ $pageLinks }}
                        </div>
                  </div>
                @endif
            </div>
@endsection
@section('page_js')
    <!-- 时间插件 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment/moment.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment/locales.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.js"></script>
    <!-- 时间插件 文件 -->
    <script src="{{ config('app.source_url') }}static/js/laydate/laydate.js"></script>
    <!-- 核心 base.js JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/marketing_l480swem.js"></script>

@endsection