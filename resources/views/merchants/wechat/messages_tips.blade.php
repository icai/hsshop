@extends('merchants.default._layouts') @section('head_css')
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_01hu3rzo.css"> @endsection @section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 面包屑导航 开始 -->
		<ul class="crumb_nav">
			<li>
				<a href="#&status=1">营销中心</a>
			</li>
			<li>
				<a href="#&status=2">微信</a>
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
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
	<!--主体左侧列表开始-->
	@include('merchants.wechat.slidebar')
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<div class="header  clearfix">
			<!-- 左侧 开始 -->
            <div class="pull-left">
            <!-- （tab试导航可以单独领出来用） -->
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li>
                        <a href="{{URL('/merchants/wechat/replyset')}}">关键词自动回复</a>
                    </li>
                    <li>
                        <a href="{{URL('/merchants/wechat/subscribereply')}}">关注后自动回复</a>
                    </li>
                    <li>
                        <a href="{{URL('/merchants/wechat/messages')}}">消息托管</a>
                    </li>
                    <li class="hover">
                        <a href="{{URL('/merchants/wechat/messagestips')}}">小尾巴</a>
                    </li>
                    <li>
                        <a href="{{URL('/merchants/wechat/weeklyreply')}}">每周回复</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <!-- 搜素框~~或者自己要写的东西 -->
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>自动回复使用教程 </a>
            </div>
            <!-- 右边 结束 -->
		</div>
		<!--操作部分开始-->
		<div class="handle">
			<div class="handle_title">
				<div class="tails">
					<p>小尾巴</p>
					<span>启用后，自动回复给粉丝的文本消息末尾都会自动加上“小尾巴”里的内容</span>
				</div>
				<div class="btn1">
					<button class="active"></button>
				</div>
			</div>
			<div class="ctl">
				<div class="handle_content clearfix">
					<div class="handle_content_left pull-left">
						内容：
					</div>
					<div class="handle_content_right pull-left">
						<div id="editor" style="height:200px;">

						</div>
					</div>
				</div>

				<div class="btn_group">
					<button class="btn btn-primary">保存</button>
				</div>
			</div>
		</div>
		<!--操作部分结束-->
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection @section('page_js') @parent
<!--ueditor插件-->
<script type="text/javascript" src="{{config('app.source_url')}}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" src="{{config('app.source_url')}}static/js/UE/UEditor/ueditor.all.js"></script>

<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
</script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_01hu3rzo.js"></script>
@endsection