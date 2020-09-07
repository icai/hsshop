@extends('merchants.default._layouts') @section('head_css')
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/usersAlter.css">
@endsection @section('middle_header')
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
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
	<input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
	<!--主体左侧列表开始-->
	@include('merchants.wechat.slidebar')
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<!-- 导航模块 开始 -->
		<div class="nav_module clearfix">
			<!-- 左侧 开始 -->
			<div class="pull-left">
				<!-- （tab试导航可以单独领出来用） -->
				<!-- 导航 开始 -->
				<ul class="tab_nav">
					<li class="hover">
						<a href="{{ URL('/merchants/wechat/book') }}">预约管理</a>
					</li>
					
				</ul>
				<!-- 导航 结束 -->
			</div>
			<!-- 左侧 结算 -->
		</div>
		<!-- 导航模块 结束 -->
		<!--main-->
		<div class="bd_main">
			<h2 class="bd_img">
				<img src="{{ config('app.source_url') }}mctsource/images/book_Handle.png" alt="" />预约处理
			</h2>
			<div class="bd_box">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div>
					<i>预约确认</i>
					@if($list)
					<select class="selects" name="status" value="">
						{{--<option value="1" @if($list['status'] ==1) selected=selected @endif>等待客服回电</option>--}}
						{{--<option value="2" @if($list['status'] ==2) selected=selected @endif>已确认</option>--}}
						{{--<option value="3" @if($list['status'] ==3) selected=selected @endif>已拒绝</option>--}}
						@if($list['status'] ==1)
							<option value="1" selected=selected >等待客服回电</option>
							<option value="2" >已确认</option>
							<option value="3" >已拒绝</option>
						@elseif($list['status'] ==2)
							<option value="2" selected=selected >已确认</option>
							<option value="3" >已拒绝</option>
						@else
							<option value="3" >已拒绝</option>
						@endif
					</select>
					@else
						<div class="no-result widget-list-empty">还没有相关数据</div>
					@endif
				</div>
				<div id="bd_txt">
					<p>商家留言</p>
					<textarea class="txtarea" name="content" rows="" cols="">{{$list['content'] or ''}}</textarea>

				</div>
				<div id="anniu">
					<button type="submit" class="bd_btnq">取消</button>
					@if(isset($list['id']) && $list['id'])
					<button type="submit" class="bd_btnt" id="{{$list['id']}}">提交</button>
					@endif
				</div>
			</div>
		</div>
		
		
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection @section('page_js') @parent
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/usersAlter.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
	//获取处理页面跳转地址栏传递的参数
	var book_id = '{{$list['book_id']}}'
	var book_date = '{{$list['book_date']}}'
	var status = '{{$list['status']}}'
</script>
@endsection
