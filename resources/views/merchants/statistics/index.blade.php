@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_mqnsz72x.css" />
@endsection
@section('slidebar')
@include('merchants.statistics.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">数据概况</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div>
        <div class="common_top">
            <span class="common_line"></span>
            <div class="common_title">
                实时概况&nbsp;&nbsp;
                <div class="common_right">
                    <i class="glyphicon glyphicon-question-sign"></i>
                    <!-- 规则说明 开始 -->
                    <div class="explain_items">
                        <p class="explain_info">今日实时数据的统计时间均为今日零时截至当前更新时间。</p>
                        <p class="explain_info">付款金额(元)：统计时间内，所有付款订单金额之和。</p>
                        <p class="explain_info">访客数：0点截至当前时间，页面被访问的去重人数，一个人在统计时间范围内访问多次只记为一次。</p>
                        <p class="explain_info">浏览量：0点截至当前时间，页面被访问的次数，一个人在统计时间内访问多次记为多次。</p>
                        <p class="explain_info">付款订单数：0点截至当前时间，成功付款的订单数，一个订单对应唯一一个订单号(拼团在成团时计入付款订单;货到付款在发货时计入付款订单)。</p>
                        <p class="explain_info">付款人数：0点截至当前时间，下单并且付款成功的客户数,一人多次付款记为一人。</p>
                    </div>
                    <!-- 规则说明 结束 -->
                </div>
                <span class="f12 c_gray"></span>
            </div>
            <div class="common_link"></div>
            
        </div>
        <!-- 区域标题 结束 -->

        <!-- 实时数据的显示 -->
        <div class="charts_show">
            <div class="charts_show-left">
                <p>付款金额</p>
                <p class="pay-amount J_pay-amount"></p>
                <div class="pay-title">昨日全天：<span class="J_yes-total"></span></div>
                <div id="charts_real_time" class="charts_real_time"></div>
            </div>
            <div class="charts_show-right">
                <div class="data-text">
                    <p class="data-text_text">访客数</p>
                    <p class="data-text_text visitor J_pay-data"></p>
                    <p class="yesterday">昨日全天：<span class="J_pay-data"></span></p>
                </div>
                <div class="data-text">
                    <p class="data-text_text">浏览量</p>
                    <p class="data-text_text visitor J_pay-data"></p>
                    <p class="yesterday">昨日全天：<span class="J_pay-data"></span></p>
                </div>
                <div class="data-text">
                    <p class="data-text_text">付款订单数</p>
                    <p class="data-text_text visitor J_pay-data"></p>
                    <p class="yesterday">昨日全天：<span class="J_pay-data"></span></p>
                </div>
                <div class="data-text">
                    <p class="data-text_text">付款人数</p>
                    <p class="data-text_text visitor J_pay-data"></p>
                    <p class="yesterday">昨日全天：<span class="J_pay-data"></span></p>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="business-view clearfix ">
            <div class="select-tab">运营视窗</div>
            <div class="clearfix pull-right" style="margin-right: 20px;">
                <div style="display: inline-block;">
                        时间筛选：
                        <select class="flow_select time_select" name="">
                            <option value="0">自然天</option>
                        </select>
                </div>
                <div class="pull-right flow_input_time">
                    <!--天选择-->
                    <input type="text" id="flow_timeone" class="zent-input laydate-icon now" placeholder="请选择日期" style="height: 26px;padding-left: 4px;"/>
                </div>
            </div>
        </div>
        <div>
            <div class="common_top">
                <span class="common_line"></span>
                <p class="common_title">
                    核心指标
                </p>
                <div class="common_link"></div> 
            </div>
            <div>
                <div class="items-select">
                    <div class="items-select-arrow items-select__prev J_items-select__prev"></div>
                    <div style="position: relative;overflow:hidden">
                        <ul class="items-select__content J_items-select__content">
                            <li class="items-select__item items-select__item--selected J_items-select__item" data-index="0">
                                <div class="statis-item">
                                    <p class="statis-item__title">付款金额</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                </div>
                            </li>
                            <li class="items-select__item items-select__item--selected J_items-select__item" data-index="1">
                                <div class="statis-item">
                                    <p class="statis-item__title">访问-付款转化率</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                </div>
                            </li>
                            <li class="items-select__item J_items-select__item" data-index="2">
                                <div class="statis-item">
                                    <p class="statis-item__title">客单价</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                </div>
                            </li>
                            <li class="items-select__item J_items-select__item" data-index="3">
                                <div class="statis-item">
                                    <p class="statis-item__title">付款订单数</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                    
                                </div>
                            </li>
                            <li class="items-select__item J_items-select__item" data-index="4">
                                <div class="statis-item">
                                    <p class="statis-item__title">付款人数</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                </div>
                            </li>
                            <li class="items-select__item J_items-select__item" data-index="5">
                                <div class="statis-item">
                                    <p class="statis-item__title">访客数</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                </div>
                            </li>
                            <li class="items-select__item J_items-select__item" data-index="6">
                                <div class="statis-item">
                                    <p class="statis-item__title">浏览量</p>
                                    <p class="statics-item-data J_core-data">0</p>
                                    <p class="statics-item-compare">
                                        较前一日
                                        <span class="J_core-compare">-</span>
                                    </p>
                                    <!-- <p class="statics-item-compare">
                                        较上一周
                                        <span>-</span>
                                    </p> -->
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="items-select-arrow items-select__next J_items-select__next"></div>
                </div>
                <div class="core-index-chart" id="core-index"></div>
            </div>
        </div>
        <div>
            <div class="common_top">
                <span class="common_line"></span>
                <p class="common_title">
                    流量看板
                </p>
                <div class="common_link">
                    <div class="link-right">
                        <i class="link-arrow"></i>
                        <a href="{{ URL('/merchants/statistics/shops/index') }}" target="_blank">店铺分析</a>
                    </div>
                </div>
            </div>
            <div class="flow-board">
                <div class="flow-board-title">流量质量指标</div>
                <div class="flow-board-content">
                    <div class="flow-board-box">
                        <div>
                            <div class="common_top">
                                <p style="color: #333;font-size: 13px;">人均浏览量</p>
                                <div class="common_right statis-item">
                                    <i class="glyphicon glyphicon-question-sign"></i>
                                    <!-- 规则说明 开始 -->
                                    <div class="explain_items" style="width: 200px;">
                                        <p class="explain_info">筛选时间内，浏览量/访客数</p>
                                    </div>
                                    <!-- 规则说明 结束 -->
                                </div>
                            </div>
                            <p class="statics-item-data J_pv-per"></p>
                            <p class="statics-item-compare">
                                较前一日
                                <span class="J_compare"></span>
                            </p>
                            <p class="statics-item-compare">
                                较上一周
                                <span class="J_compare"></span>
                            </p>
                        </div>
                        <div id="charts-flow" class="charts-flow"></div>
                    </div>
                </div>
            
            </div>
            <div class="flow-board">
                <div class="flow-board-title">流量转化</div>
                <div class="flow-board-content circle-chart-box">
                    <div class="circle-chart-item">
                        <div class="circle-chart">
                            <canvas id="circle-chart-c" width="180" height="180"></canvas>
                        </div>
                        <div class="data-box">
                            <div class="common_top">
                                <p style="color: #333;font-size: 13px;">商品-访问转化率</p>
                                <div class="common_right statis-item">
                                    <i class="glyphicon glyphicon-question-sign"></i>
                                    <!-- 规则说明 开始 -->
                                    <div class="explain_items" style="width: 250px;">
                                        <p class="explain_info">筛选时间内，商品访客数/店铺访客数</p>
                                    </div>
                                    <!-- 规则说明 结束 -->
                                </div>
                            </div>
                            <p class="statics-item-compare">
                                较前一日
                                <span class="J_compare"></span>
                            </p>
                            <p class="statics-item-compare">
                                较上一周
                                <span class="J_compare"></span>
                            </p>
                        </div>
                    </div>
                    <div class="circle-chart-item">
                        <div class="circle-chart">
                            <canvas id="circle-chart-c2" width="180" height="180"></canvas>
                        </div>
                        <div class="data-box">
                            <div class="common_top">
                                <p style="color: #333;font-size: 13px;">访问-付款转化率</p>
                                <div class="common_right statis-item">
                                    <i class="glyphicon glyphicon-question-sign"></i>
                                    <!-- 规则说明 开始 -->
                                    <div class="explain_items" style="width: 250px;">
                                        <p class="explain_info">筛选时间内，付款人数/访客数</p>
                                    </div>
                                    <!-- 规则说明 结束 -->
                                </div>
                            </div>
                            <p class="statics-item-compare">
                                较前一日
                                <span class="J_compare"></span>
                            </p>
                            <p class="statics-item-compare">
                                较上一周
                                <span class="J_compare"></span>
                            </p>
                        </div>
                    </div>
                </div>
            
            </div>
        </div>
        <div>
            <div class="common_top">
                <span class="common_line"></span>
                <p class="common_title">
                    商品看板
                </p>
                <div class="common_link">
                </div>
            </div>
            <div class="goods-board">
                <div class="goods-board-item goods-board-left">
                    <div class="goods-title">TOP5访问排行</div>
                    <div class="goods-content">
                        <div class="goods-thead">
                            <div class="thead-th">
                                <div class="goods-cell">商品</div>
                            </div>
                            <div class="thead-th">
                                <div class="goods-cell">访客数</div>
                            </div>
                            <div class="thead-th">
                                <div class="goods-cell">单品转化率</div>
                            </div>
                        </div>
                        <div class="goods-tbody J_top-view">
                            
                        </div>
                    </div>
                </div>
                <div class="goods-board-item">
                    <div class="goods-title">TOP5付款排行</div>
                    <div class="goods-content">
                        <div class="goods-thead">
                            <div class="thead-th">
                                <div class="goods-cell">商品</div>
                            </div>
                            <div class="thead-th">
                                <div class="goods-cell">付款件数</div>
                            </div>
                            <div class="thead-th">
                                <div class="goods-cell">售价</div>
                            </div>
                        </div>
                        <div class="goods-tbody J_top-pay">
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div>
            <div class="common_top">
                <span class="common_line"></span>
                <p class="common_title">
                    客户看板
                </p>
                <div class="common_link">
                    <div class="link-right">
                        <i class="link-arrow"></i>
                        <a href="{{ URL('/merchants/statistics/customer/index') }}" target="_blank">客户分析</a>
                    </div>
                </div>
            </div>
            <div class="goods-board">
                <div class="goods-board-item goods-board-left">
                    <div class="goods-title">微信粉丝</div>
                    <div class="goods-content">
                        <div class="customer-data">
                            <div class="customer-data-item">
                                <p style="color: #333;font-size: 13px;">累积粉丝数</p>
                                <p class="statics-item-data">49</p>
                                <p class="statics-item-compare">
                                    较前一日
                                    <span class="up-arrow">↑ 40%</span>
                                </p>
                                <p class="statics-item-compare">
                                    较上一周
                                    <span class="down-arrow">↓ -40%</span>
                                </p>
                            </div>
                            <div class="customer-data-item">
                                <p style="color: #333;font-size: 13px;">净增粉丝数</p>
                                <p class="statics-item-data">49</p>
                                <p class="statics-item-compare">
                                    较前一日
                                    <span class="up-arrow">↑ 40%</span>
                                </p>
                                <p class="statics-item-compare">
                                    较上一周
                                    <span class="down-arrow">↓ -40%</span>
                                </p>
                            </div>
                            <div class="customer-data-item">
                                <p style="color: #333;font-size: 13px;">访问粉丝数</p>
                                <p class="statics-item-data">49</p>
                                <p class="statics-item-compare">
                                    较前一日
                                    <span class="up-arrow">↑ 40%</span>
                                </p>
                                <p class="statics-item-compare">
                                    较上一周
                                    <span class="down-arrow">↓ -40%</span>
                                </p>
                            </div>
                        </div>
                        <div class="customer-chart-container" id="customer-chart-wx"></div>
                    </div>
                </div>
                <div class="goods-board-item">
                    <div class="goods-title">店铺会员</div>
                    <div class="goods-content">
                        <div class="customer-data">
                            <div class="customer-data-item">
                                <p style="color: #333;font-size: 13px;">累积会员数</p>
                                <p class="statics-item-data">49</p>
                                <p class="statics-item-compare">
                                    较前一日
                                    <span class="up-arrow">↑ 40%</span>
                                </p>
                                <p class="statics-item-compare">
                                    较上一周
                                    <span class="down-arrow">↓ -40%</span>
                                </p>
                            </div>
                            <div class="customer-data-item">
                                <p style="color: #333;font-size: 13px;">新增会员数</p>
                                <p class="statics-item-data">49</p>
                                <p class="statics-item-compare">
                                    较前一日
                                    <span class="up-arrow">↑ 40%</span>
                                </p>
                                <p class="statics-item-compare">
                                    较上一周
                                    <span class="down-arrow">↓ -40%</span>
                                </p>
                            </div>
                            <div class="customer-data-item">
                                <p style="color: #333;font-size: 13px;">成交会员数</p>
                                <p class="statics-item-data">49</p>
                                <p class="statics-item-compare">
                                    较前一日
                                    <span class="up-arrow">↑ 40%</span>
                                </p>
                                <p class="statics-item-compare">
                                    较上一周
                                    <span class="down-arrow">↓ -40%</span>
                                </p>
                            </div>
                        </div>
                        <div class="customer-chart-container" id="customer-chart-shop"></div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>
@endsection
@section('page_js')

<!-- 后台数据 -->
<script type="text/javascript">
    const pageUrl = `{{ config('app.dc_url')}}`;
    const wid = "{{$wid}}"
    const sourceUrl = `{{ config('app.source_img_url') }}`;
</script>

<!-- 下拉框美化插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 图表插件 -->
<script src="{{ config('app.source_url') }}static/js/echarts/echarts-all.js"></script>
<!-- 选择时间插件 -->
<!-- 时间控件js -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_mqnsz72x.js"></script>
@endsection