@extends('merchants.default._layouts') @section('head_css')
<!-- 当前模块css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_KlJMOLjn.css" /> @endsection @section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 面包屑导航 开始 -->
		<ul class="crumb_nav">
            <li>
                <a href="javascript:void(0);">公众号</a>
            </li>
            <li>
                <a href="javascript:void(0);">{{ $title }}</a>
            </li>
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
@endsection @section('content')
<div class="content" style="display: -webkit-box;">
	<!--<div class="container">-->
	<!--主体左侧列表开始-->
	@include('merchants.wechat.slidebar')
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<!--头部开始-->
		<div class="right_header">
			<ul>
				<li>
					<a class="co_38f" herf="javascript:void(0)">0</a>未读消息
				</li>
				<li>
					<a class="co_000" herf="javascript:void(0)">0</a>昨日新增粉丝
				</li>
				<li>
					<a class="co_000" herf="javascript:void(0)">0</a>昨日跑路粉丝
				</li>
				<li>
					<a class="co_38f" herf="javascript:void(0)">10000</a>昨日总粉丝
				</li>
			</ul>
		</div>
		<!--头部结束-->
		<!-- 图文标题 开始 -->
		<div class="common_top">
			<span class="common_line"></span>
			<p class="common_title">
				微信粉丝增减趋势
				<a href="javascript:void(0);" target="_blank">详细》</a>
			</p>
			<div class="common_right">
				<a href="javascript:void(0)">学习如何高效吸粉!</a>
				<i class="glyphicon glyphicon-question-sign"></i>
				<!-- 规则说明 开始 -->
				<div class="explain_items">
					<p class="explain_info">新增粉丝：新关注的粉丝去重人数；</p>
					<p class="explain_info">跑路粉丝：取消关注的粉丝去重人数；</p>
					<p class="explain_info">净增粉丝：新关注与取消关注的用户数之差；</p>
				</div>
				<!-- 规则说明 结束 -->
			</div>
		</div>
		<!-- 图文标题 结束 -->
		<div class="data">
			<p class="co_000">暂无数据，粉丝增加不给力啊，快去宣传你的店铺吧～</p>
		</div>
		<!-- 图文标题 开始 -->
		<div class="common_top">
			<span class="common_line"></span>
			<p class="common_title">
				微信互动趋势
				<a href="javascript:void(0);" target="_blank">详细》</a>
			</p>
			<div class="common_right">
				<i class="glyphicon glyphicon-question-sign"></i>
				<!-- 规则说明 开始 -->
				<div class="explain_items">
					<p class="explain_info">接收信息数：粉丝主动发送信息、点击菜单的次数总和；</p>
					<p class="explain_info">发送信息数：回应菜单查阅、自动回复、人工回复、消息群发的次数总和；</p>
					<p class="explain_info">互动人数：粉丝主动发送消息、点击菜单的去重人数。</p>
				</div>
				<!-- 规则说明 结束 -->
			</div>
		</div>
		<!-- 图文标题 结束 -->
		<div class="data">
			<p class="co_000">您刚入驻，正在同步数据，建议您明天过来看～</p>
		</div>
		<!-- 图文标题 开始 -->
		<div class="common_top">
			<span class="common_line"></span>
			<p class="common_title">
				微信浏览趋势
				<a href="javascript:void(0);" target="_blank">详细》</a>
			</p>
			<div class="common_right">
				<i class="glyphicon glyphicon-question-sign"></i>
				<!-- 规则说明 开始 -->
				<div class="explain_items">
					<p class="explain_info">浏览PV：所有店铺和商品页面的浏览次数；</p>
					<p class="explain_info">浏览UV：所有店铺和商品页面的访问人数；</p>
					<p class="explain_info">到店PV：带去店铺和商品页面的次数。</p>
					<p class="explain_info">到店UV：带去店铺和商品页面的人数；</p>
				</div>
				<!-- 规则说明 结束 -->
			</div>
		</div>
		<!-- 图文标题 结束 -->
		<div id="echarts"></div>
	</div>
	<!--主体右侧内容结束-->
	<!--</div>-->
</div>
@endsection @section('page_js')
<!-- 图表插件 -->
<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_KlJMOLjn.js"></script>
@endsection