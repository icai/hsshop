@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_9mi13xv6.css" />
@endsection
@section('slidebar')
@include('merchants.statistics.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">交易分析</div>
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
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">交易概况</p>
        <div class="common_link">
            <!-- <a class="blue_38f order_export" href="">导出</a> -->
            <!-- <a class="blue_38f" href="javascript:void(0);" data-toggle="modal" data-target="#myModal">高级导出</a> -->
            <!-- 高级导出弹框 开始 -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">高级导出</h4>
                        </div>
                        <div class="modal-body">
                            图表
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal2" data-dismiss="modal">导出数据</button>
                            <a class="btn btn-default" href="{{ URL('/merchants/statistics/shops/export') }}" >查看已生成报表</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 高级导出弹框 结束 -->
            <!-- 高级导出弹框 开始 -->
            <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">高级导出</h4>
                        </div>
                        <div class="modal-body">
                            图表
                        </div>
                        <div class="modal-footer">>
                            <a href="javascript:void(0);" class="btn btn-default">查看已生成报表</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 高级导出弹框 结束 -->
        </div>


        <div class="zent-block__header--right clearfix">
            <div class="zent-popover-wrapper zent-select date-head__select " style="display: inline-block;">
                <div class="zent-select-text">
                    <select class="zent-select-text flow_select time_select" name="">
                        <option value="0">自然天</option>
                        <option value="1">自然月</option>
                        <option value="2">自定义</option>
                    </select>
                </div>
            </div>
            <div class="date-range pull-right">
                <div class="zent-datetime-picker ">
                    <div class="zent-popover-wrapper">
                        <div class="picker-input picker-input--filled">
                            <div class="zent-input-wrapper flow_input_time">
                                <!--天选择-->
                                <input type="text" id="flow_timeone" class="zent-input laydate-icon now" placeholder="请选择日期" />
                                <!--月份选择-->
                                <input type="text" id="flow_timetwo" class="zent-input laydate-icon now hidden" placeholder="请选择月份" />
                                <!--自定义选择-->
                                <div class="time_custom hidden zent-input">
                                    <input type="text" id="flow_timethr_1" class="laydate-icon now" placeholder="请选择日期" />
                                    -
                                    <input type="text" id="flow_timethr_2" class="laydate-icon now" placeholder="请选择日期" />
                                </div>
                            </div>
                            <span class="zenticon zenticon-calendar-o"></span><span class="zenticon zenticon-close-circle"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 区域标题 结束 -->

    <!-- 交易概况 开始 -->
    <div class="trade_items mgb15">
        <!-- 交易表格 开始 -->
        <div class="trade_table">
            <table class="table">
                <tr>
                    <td>
                        <p class="items_title">访客数</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <p class="items_title">下单人数</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td>
                        <p class="items_title">下单笔数</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td>
                        <p class="items_title">下单金额</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <p class="items_title">付款人数</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td>
                        <p class="items_title">付款订单数</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td>
                        <p class="items_title">付款金额</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td>
                        <p class="items_title">付款件数</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                    <td>
                        <p class="items_title">客单价</p>
                        <p class="itmes_num items_arr"></p>
                        <p class="items_gray c_gray items_form">较前一天</p>
                        <p class="items_gray items_per_arr"></p>
                    </td>
                </tr>
            </table>
        </div>
        <!-- 交易表格 结束 -->
        <!-- 交易图 开始 -->
        <div class="trade_pic">
            <div class="transaction_data">
                <p class="items_title">访问-下单转换率</p>
                <p class="items_visited_order"></p>
            </div>
            <div class="transaction_data">
                <p class="items_title">访问-付款转化率</p>
                <p class="items_visited_payed"></p>
            </div>
            <div class="transaction_data">
                <p class="items_title">下单-付款转化率</p>
                <p class="items_order_payed"></p>
            </div>
        </div>
        <!-- 交易图 结束 -->
    </div>
    <!-- 交易概况 结束 -->



    <!-- 交易概况图表  开始 -->
    <div id="trade_chart" class="chart_items mgb15">
        
    </div>
    <!-- 交易概况图表 结束 -->
@endsection
@section('page_js')
<script>
    const pageUrl = `{{ config('app.dc_url')}}`;
    const wid = "{{$wid}}"
</script>

<!-- 下拉框美化插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 图表插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/echarts/echarts-now.js"></script>
<!-- 选择时间插件 -->
<!-- 时间控件js -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_9mi13xv6.js"></script>
@endsection