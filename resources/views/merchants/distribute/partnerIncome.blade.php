@extends('merchants.default._layouts')
@section('head_css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpktyf2.css" />
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')
	
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav"> 
            <li class="hover">  
                <a id="income">合伙人佣金详情</a>
            </li>  
            <li>  
                <a id="contacts">合伙人人脉</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content" style="padding-bottom: 40px;">
	<!--显示部分-->
	<div class="screen">
		<div class="screen_wraper">
		    <form id="form_1" class="form-inline">
		  		
		  		<div class="form-group col-sm-4">
		    		<label>微信昵称:</label>
		    		<span></span>
		  		</div>
		  		<div class="form-group col-sm-4">
		    		<label>手机号:</label>
		    		<span></span>
		  		</div>
				<div class="form-group col-sm-4">
					<label>注册时间:</label>
					<span></span>
				</div>
			</form>
		</div>
		<div class="screen_wraper">
			<form id="form_2" class="form-inline">
		  		<div class="form-group col-sm-4">
		    		<label>累计佣金:￥</label>
		    		<span></span>
		  		</div>
		  		<div class="form-group col-sm-4">
		    		<label>可提现佣金:￥</label>
		    		<span></span>
		  		</div>
		  		<div class="form-group col-sm-4">
		    		<label>待提现佣金:￥</label>
		    		<span></span>
		  		</div>
			</form>
		</div>
		<div class="screen_wraper">
			<form id="form_3" class="form-inline">
		  		<div class="form-group col-sm-4">
		    		<label>下级用户交易总额:￥</label>
		    		<span></span>
		  		</div>
			</form>
		</div>
	</div>
	<!--列表名称-->
	<div class="shortHint">
		<div class="order-btn active js-order-btn" data-type="1">佣金流水</div>
		<div class="order-btn js-order-btn" data-type="2">下级所有订单</div>
		<div class="common_link"></div>
		<div class="timer-wraper clearfix">
			<div class="timer-select-title">时间筛选：</div>
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
	<!--佣金流水-->
	<div class="fx-wraper">
		<div class="member_list">
			<ul class="list_item list_header">
				<!--<li>微信ID</li>-->
				<li>微信昵称</li>
				<li>金额</li>
				<li>来源订单</li>
				<li>分销信息</li>
				<li>分销生成时间</li>
			</ul>
			<div class="list_div js-list_div">
				
			</div>
		</div>
		<div class="main_bottom flex-end">
			<span id="pageInfo">
				<span></span>
				<a class="js-firstPage" href="##">首页</a>
				<a class="js-prevPage" href="##">上一页</a>
				<a class="js-nextPage" href="##">下一页</a>
				<a class="js-lastPage" href="##">尾页</a>
			</span>
		</div>
	</div>

	<!-- 下级所有订单 -->
	<div class="all-wraper">
		<div class="member_list">
			<ul class="list_item list_header">
				<li>微信昵称</li>
				<li>订单号</li>
				<li>订单金额</li>
				<li>订单状态</li>
				<li>下单时间</li>
			</ul>
			<div class="list_div js-list_div1">
				
			</div>
		</div>
		<div class="main_bottom flex-end">
			<span id="pageInfo1">
				<span></span>
				<a class="js-firstPage1" href="##">首页</a>
				<a class="js-prevPage1" href="##">上一页</a>
				<a class="js-nextPage1" href="##">下一页</a>
				<a class="js-lastPage1" href="##">尾页</a>
			</span>
		</div>
	</div>
	
</div>
@endsection
@section('page_js')
	<!-- 时间控件js -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpktyf2.js"></script>
    <script type="text/javascript">
    	var id = 666;
    </script>
@endsection