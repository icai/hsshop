@extends('merchants.default._layouts') @section('head_css')
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/book.css">
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
@include('merchants.wechat.slidebar')
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
	<input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
	<!--主体左侧列表开始-->
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
		<!--按钮-->
		<div class="right_content">
			<div class="box">
				<button class="btn btn-success newOrder">新增预约</button>
				<button class="btn btn-success refresh">刷新</button>
			</div>
		</div>
		<!--main-->
		@if($list['data'])
		<div id="t_content">
        	<ul class="t_content_header">
        		<li>
        			<span>预约名称</span>
        		</li>
        		<li>关键字</li>
        		<li>预约电话</li>
        		<li>限定方式</li>
        		<li>限定量</li>
        		<li>总数/待处理</li>
        		<li>开始时间</li>
        		<li>结束时间</li>
        		<li>操作</li>
        	</ul>
        	@forelse($list['data'] as $val) 
        	<ul class="t_content_con">
        		<li>
        			<span>{{ $val['title'] }}</span>
        		</li>
        		<li>{{ $val['keywords'] }}</li>
        		<li>{{ $val['phone'] }}</li>
        		<li>{{ $val['limit_type'] }}</li>
        		<li>{{ $val['limit_num'] }}</li>
        		<li>{{ $val['bookTotal'] }}/{{ $val['pendingTotal'] }}</li>
        		<li>{{ $val['start_time'] }}</li>
        		<li>{{ $val['end_time'] }}</li>
        		<li>
        			<p><a href="/merchants/wechat/userList/{{ $val['id']  }}">预约管理</a></p>
        			<p><a href="/merchants/wechat/bookSave?id={{ $val['id'] }}">编辑</a></p>
        			<p class="t_shan" data-id="{{ $val['id'] }}">删除</p>
					<p>
                        <a href="javascript:;" class="book-link">推广链接</a>
                        <input type="hidden" value="{{config('app.url')}}shop/book/detail/{{ session('wid') }}/{{ $val['id'] }}" />
                    </p>
        		</li>
        	</ul>
        	@empty
        	@endforelse        	
        	<!-- 分页 -->
	        <div class="text-right">
	            {!! $pageHtml !!}
	        </div>
        </div>
		@else
		<div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center; margin-top: 10px">还没有相关数据
        </div>
        @endif
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection 
@section('page_js') @parent
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url') }}mctsource/js/book.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
</script>
@endsection