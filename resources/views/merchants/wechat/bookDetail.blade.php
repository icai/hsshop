@extends('merchants.default._layouts') @section('head_css')
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/bookDetail.css">
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
				<img src="{{ config('app.source_url') }}mctsource/images/book_Detail.png" alt="" />预约详情
			</h2>

			<div class="bd_box">
		@if( $list )
					<div><i>预约号</i><span>{{ $list['id'] }}</span></div>
					{{--<div><i>真实姓名</i><span>{{ $list['name'] }}</span></div>--}}
				    {{--<div><i>电话</i><span>{{ $list['phones'] or ''}}</span></div>--}}
				@if( $list['form_content'])
				@foreach( $list['form_content'] as $k=>$v)
					<div><i>{{ $v['ykey'] }}</i><span>{{ $v['yval'] or ''}}</span></div>
				@endforeach
				@endif
					<div><i>预约状态</i>
						@if($list['status'] == '1')
							<span>等待客服处理</span>
						@elseif($list['status'] == '2')
							<span>已确认</span>
						@else
							<span>已拒绝</span>
						@endif
					</div>
					<div><i>备注</i><span>{{$list['remark'] or ''}}</span></div>
					<button class="bd_btn">返回</button>
		@else
			<div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据
			</div>
		@endif

			</div>
		</div>
		
		
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection @section('other')
<!-- 删除弹框 -->
<div class="popover left delete_pop" role="tooltip">
	<div class="arrow"></div>
	<div class="popover-content">
		<span>你确定要删除吗？</span>
		<button class="btn btn-primary sure_btn">确定</button>
		<button class="btn btn-default cancel_btn">取消</button>
	</div>
</div>
@endsection @section('page_js') @parent
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/bookDetail.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
</script>
<script type="text/javascript">
    var book_id = '{{$list['book_id']}}'
    {{--var book_date = '{{$list['book_date']}}'--}}
    {{--var status = '{{$list['status']}}'--}}
	$('.bd_btn').on('click',function(){
		window.location.href='/merchants/wechat/userList/'+book_id
	})
</script>
@endsection
