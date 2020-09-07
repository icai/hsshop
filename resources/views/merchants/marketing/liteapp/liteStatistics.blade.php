@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_statis.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
					数据统计
				</li>
				{{--<li>--}}
					{{--<a href="javascript:void(0)">微信小程序</a>--}}
				{{--</li>--}}
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
        {{--<ul class="tab_nav">--}}
            {{--<li>--}}
                {{--<a href="/merchants/marketing/litePage">小程序微页面</a>--}}
            {{--</li>--}}
            {{--<li>--}}
                {{--<a href="/merchants/marketing/footerBar">底部导航</a>--}}
            {{--</li>--}}
            {{--<li class="">--}}
                {{--<a href="/merchants/marketing/xcx/topnav">首页分类导航</a>--}}
            {{--</li>--}}
			{{--<li class=""> <!-- update 梅杰 新增列表页-->--}}
				{{--<a href="/merchants/marketing/xcx/list">小程序列表</a>--}}
			{{--</li>--}}
			{{--<li class="hover">--}}
				{{--<a href="/merchants/marketing/liteStatistics">数据统计</a>--}}
			{{--</li>--}}
        {{--</ul>--}}
        <div class="app">
        	<div class="tooltip top" role="tooltip">
      <div class="tooltip-arrow"></div>
      <div class="tooltip-inner">
        Tooltip on the top
      </div>
    </div>
        	<div class="app-inner clearfix">
        		<div class="app-init-container">
        			<div class="app__content" id="js-react-container">
        				<div data-reactroot="">
        					<div class="weapp-overview">
        						<div class="zent-block__header clearfix ">
        							<div class="zent-block__header--left">
        								<h3>昨日概况</h3>
    								</div>
        						</div>
        						<div class="zent-loading-container zent-loading-container-static " style="height: initial;">
        							<table class="weapp-overview__table">
        								<tbody>
        									<tr>
        										<td rowspan="2" class="weapp-overview__td weapp-overview__td--main"><i class="icon-pay"></i>
        											<div class="statis-item ">
        												<h4><span class="statis-item__title">付款金额</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        													<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="昨日所有订单付款金额之和"></i></div>
    													</h4>
        												<p class="statis-item__data pay_amount">0.00</p><span class="statis-item__compare none-growth pay_amount_growth_col"><!-- react-text: 60 -->较前一日<!-- /react-text --><span class="arrow pay_amount_growth">-</span></span>
        											</div>
        										</td>
        										<td class="weapp-overview__td">
        											<div class="statis-item ">
        												<h4><span class="statis-item__title">浏览量</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        													<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="昨日页面被访问的次数,一个人昨日访问多次记为多次"></i></div>
    													</h4>
        												<p class="statis-item__data pv">0</p><span class="statis-item__compare none-growth pv_growth_col"><!-- react-text: 71 -->较前一日<!-- /react-text --><span class="arrow pv_growth">-</span></span>
        											</div>
        										</td>
        										<td class="weapp-overview__td">
        											<div class="statis-item ">
        												<h4><span class="statis-item__title">访客数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        													<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="昨日页面被访问的去重复人数,一个人多日访问多次只记为一人"></i></div>
    													</h4>
        												<p class="statis-item__data uv">0</p><span class="statis-item__compare none-growth uv_growth_col"><!-- react-text: 82 -->较前一日<!-- /react-text --><span class="arrow uv_growth">-</span></span>
        											</div>
        										</td>
        									</tr>
        									<tr>
        										<td class="weapp-overview__td">
        											<div class="statis-item ">
        												<h4><span class="statis-item__title">付款订单数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        													<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="昨日成功付款的订单数,一个订单对应唯一一个订单号"></i></div>
    													</h4>
        												<p class="statis-item__data pay_order_count">0</p><span class="statis-item__compare none-growth pay_order_count_growth_col"><!-- react-text: 94 -->较前一日<!-- /react-text --><span class="arrow pay_order_count_growth">-</span></span>
        											</div>
        										</td>
        										<td class="weapp-overview__td">
        											<div class="statis-item ">
        												<h4><span class="statis-item__title">付款客户数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        													<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="昨日成功付款的去重人数,一个人在昨日多次付款只记为一人"></i></div>
    													</h4>
        												<p class="statis-item__data pay_customer_count">0</p><span class="statis-item__compare none-growth pay_customer_count_growth_col"><!-- react-text: 105 -->较前一日<!-- /react-text --><span class="arrow pay_customer_count_growth">-</span></span>
        											</div>
        										</td>
        									</tr>
        								</tbody>
        							</table>
        						</div>
        					</div>
        					<div class="weapp-flow">
        						<div class="zent-block__header clearfix ">
        							<div class="zent-block__header--left">
        								<h3>流量统计</h3>
    								</div>
        							<div class="zent-block__header--right">
        								<div class="zent-popover-wrapper zent-select date-head__select " style="display: inline-block;">
        									<div class="zent-select-text">
        										<select class="zent-select-text flow_select" name="">
        											<option value="0">自然天</option>
        											<option value="1">自然周</option>
        											<option value="2">自然月</option>
        										</select>
        									</div>
        								</div>
        								<div class="date-range">
        									<div class="zent-datetime-picker ">
        										<div class="zent-popover-wrapper" style="display: block;">
        											<div class="picker-input picker-input--filled">
        												<div class="zent-input-wrapper flow_input_time">
        													<!--天选择-->
        													<input type="text" id="flow_timeone" class="zent-input laydate-icon now" placeholder="请选择日期">
    														<!--范围选择-->
    														<input type="text" id="flow_timetwo" class="zent-input laydate-icon now hidden" placeholder="请选择同一周周一至周日日期">
															<!--月份选择-->
															<input type="text" id="flow_timethr" class="zent-input laydate-icon now hidden" placeholder="请选择日期">
    													</div>
        												<span class="zenticon zenticon-calendar-o"></span><span class="zenticon zenticon-close-circle"></span>
    												</div>
        										</div>
        									</div>
        								</div>
        							</div>
        						</div>
        						<div class="items-select">
        							<ul class="items-select__content">
        								<li class="items-select__item items-select__item--selected">
        									<div class="statis-item ">
        										<h4>
        											<span class="statis-item__title">浏览量</span>
        											<div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        												<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,页面被访问的次数,一个人在统计时间内访问多次记为多次"></i>
													</div>
												</h4>
        										<p class="statis-item__data flow_pv">0</p><span class="statis-item__compare none-growth flow_pv_growth_col"><!-- react-text: 137 -->较前一天<!-- /react-text --><span class="arrow flow_pv_growth">-</span></span>
        									</div>
        								</li>
        								<li class="items-select__item items-select__item--selected">
        									<div class="statis-item ">
        										<h4><span class="statis-item__title">访客数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        											<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,页面被访问的去重人数,一个人在统计时间范围内访问多次只记为一人"></i></div>
    											</h4>
        										<p class="statis-item__data flow_uv">0</p><span class="statis-item__compare none-growth flow_uv_growth_col"><!-- react-text: 148 -->较前一天<!-- /react-text --><span class="arrow flow_uv_growth">-</span></span>
        									</div>
        								</li>
        								<li class="items-select__item">
        									<div class="statis-item ">
        										<h4><span class="statis-item__title">新访客数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        											<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,首次访问小程序的数据数,一人多次访问记为一人"></i></div>
    											</h4>
        										<p class="statis-item__data flow_newuv">0</p><span class="statis-item__compare none-growth flow_newuv_growth_col"><!-- react-text: 159 -->较前一天<!-- /react-text --><span class="arrow flow_newuv_growth">-</span></span>
        									</div>
        								</li>
        								<li class="items-select__item">
        									<div class="statis-item ">
        										<h4><span class="statis-item__title">平均访问深度</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        											<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,平均每个客户访问小程序的页面数"></i></div>
    											</h4>
        										<p class="statis-item__data flow_visit_depth">0</p><span class="statis-item__compare none-growth flow_visit_depth_growth_col"><!-- react-text: 170 -->较前一天<!-- /react-text --><span class="arrow flow_visit_depth_growth">-</span></span>
        									</div>
        								</li>
        								<li class="items-select__item">
        									<div class="statis-item ">
        										<h4><span class="statis-item__title">人均停留时长</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        											<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,平均每个客户停留在小程序页面的总时长"></i></div>
    											</h4>
        										<p class="statis-item__data flow_stay_time_uv">0s</p><span class="statis-item__compare none-growth flow_stay_time_uv_growth_col"><!-- react-text: 181 -->较前一天<!-- /react-text --><span class="arrow flow_stay_time_uv_growth">-</span></span>
        									</div>
        								</li>
        							</ul>
        						</div>
        						<div class="zent-loading-container zent-loading-container-static " style="height: initial;">
        							<div id="main" class="echarts"></div>
    							</div>
        						<div class="zent-block__header clearfix flow-resource">
        							<div class="zent-block__header--left">
        								<h3>访问来源</h3>
    								</div>
        						</div>
        						<div class="zent-loading-container zent-loading-container-static " style="height: initial;">
        							<div class="integration__fallback">
        								<span class="integration__fallback-placeholder hidden" style="height: 300px;">暂无数据</span>
        								<div id="main_sec" class="echarts"></div>
    								</div>
        						</div>
        					</div>
        					<div class="weapp-trade" style="display: none;">
        						<div class="zent-block__header clearfix ">
        							<div class="zent-block__header--left">
        								<h3>交易统计</h3>
    								</div>
        							<div class="zent-block__header--right">
        								<div class="zent-popover-wrapper zent-select date-head__select " style="display: inline-block;">
        									<div class="zent-select-text">
        										<select class="zent-select-text" name="">
        											<option value="">自然天</option>
        											<option value="">自然周</option>
        											<option value="">自然月</option>
        										</select>
        									</div>
        								</div>
        								<div class="date-range">
        									<div class="zent-datetime-picker ">
        										<div class="zent-popover-wrapper" style="display: block;">
        											<div class="picker-input picker-input--filled">
        												<div class="zent-input-wrapper">
        													<input type="text" id="trans_time" class="zent-input laydate-icon" value="">
    													</div>
        												<span class="zenticon zenticon-calendar-o"></span><span class="zenticon zenticon-close-circle"></span>
    												</div>
        										</div>
        									</div>
        								</div>
        							</div>
        						</div>
        						<div class="trans-table">
        							<div class="trans-table__row">
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">访客数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="页面被访问的去重人数,一个人在统计时间范围内访问多次只记为一人"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 221 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        							</div>
        							<div class="trans-table__row">
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">下单人数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,点击提交订单的客户数，一人多次下单记为一人"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 232 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">下单笔数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,点击提交订单的订单数"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 242 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">下单金额</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,点击提交订单的订单总金额"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 252 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        							</div>
        							<div class="trans-table__row">
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">付款人数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,付款成功的客户数,一人多次付款记为一人"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 263 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">付款笔数</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,付款成功的订单笔数"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 273 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">付款金额</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,付款成功的订单总金额"></i></div>
    										</h4>
        									<p class="statis-item__data">0</p><span class="statis-item__compare none-growth"><!-- react-text: 283 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        								<div class="statis-item trans-table__column">
        									<h4><span class="statis-item__title">客单价</span><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
        										<i class="zenticon zenticon-help-circle" data-toggle="tooltip" data-placement="top" title="统计时间内,客户平均付款金额。付款金额/付款人数"></i></div>
    										</h4>
        									<p class="statis-item__data">-</p><span class="statis-item__compare none-growth"><!-- react-text: 293 -->较前一天<!-- /react-text --><span class="arrow">-</span></span>
        								</div>
        							</div>
        							<div class="trans-table__rate"><img alt="交易统计转化率图" src="{{ config('app.source_url') }}mctsource/images/stastic.png" width="346" height="208">
        								<div class="trans-table__rate-1">
        									<!-- react-text: 298 -->下单转化率
        									<!-- /react-text --><br>
        									<!-- react-text: 300 -->-
        									<!-- /react-text -->
        								</div>
        								<div class="trans-table__rate-2">
        									<!-- react-text: 302 -->付款转化率
        									<!-- /react-text --><br>
        									<!-- react-text: 304 -->-
        									<!-- /react-text -->
        								</div>
        								<div class="trans-table__rate-3">
        									<!-- react-text: 306 -->全店转化率
        									<!-- /react-text --><br>
        									<!-- react-text: 308 -->-
        									<!-- /react-text -->
        								</div>
        							</div>
        						</div>
        						<div class="zent-loading-container zent-loading-container-static " style="height: initial;">
        							<div class="zent-loading-container zent-loading-container-static " style="height: initial;">
	        							<div id="main_thri" class="echarts"></div>
	    							</div>
        						</div>
        					</div>
        				</div>
        			</div>
        		</div>
        		<div class="notify-bar js-notify animated hinge hide">
        		</div>
        	</div>
        </div>
    </div>
@endsection

@section('page_js')
<!--时间插件-->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/laydate/laydate.js"></script>
<!-- 图表插件 -->
<script src="{{ config('app.source_url') }}static/js/echarts/echarts-all.js"></script>
<!--当前js-->
<script src="{{ config('app.source_url') }}mctsource/js/liteapp_statis.js" type="text/javascript" charset="utf-8"></script>
@endsection