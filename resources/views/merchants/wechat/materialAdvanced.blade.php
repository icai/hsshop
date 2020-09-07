@extends('merchants.default._layouts') @section('head_css')
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_lrqmvpo1.css"> @endsection @section('middle_header')
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
					<li>
						<a href="{{ URL('/merchants/wechat/materialWechat') }}">微信图文</a>
					</li>
					<li class="hover">
						<a href="{{ URL('/merchants/wechat/materialAdvanced') }}">高级图文</a>
					</li>
				</ul>
				<!-- 导航 结束 -->
			</div>
			<!-- 左侧 结算 -->

		</div>
		<!-- 导航模块 结束 -->
		<!--新建图文开始-->
		<div class="right_content">
			<div class="box">
				<button class="btn btn-success">新建图文</button>
				<!--新建图文模态框开始-->
				<div class="new_cap">
					<div class="details">
						该类型不可用于群发。请使用“微信图文”创建群发图文素材。
						<!-- <a class="co_blue" href="javascript:void(0);">查看详情</a> -->
					</div>
					<ul class="clearfix">
						<li>
							<a href="{{ URL('/merchants/wechat/materialAdvancedSingle') }}">
								<p class="co_000">单条图文</p>
								<div class="bg_e5"></div>
							</a>
						</li>
						<li>
							<a href="{{ URL('/merchants/wechat/materialAdvancedMulti') }}">
								<p class="co_000">多条图文</p>
								<div class="bg_e5"></div>
								<div class="bg_e5"></div>
							</a>
						</li>
					</ul>
				</div>
				<!--新建图文模态框结束-->
			</div>
			@if ( empty($list) )
			<div class="no_result">
				还没有相关数据
			</div>
			@else
			<!--数据列表开始-->
   			<div class="data_list">
       			<table class="table">
					<thead>
						<tr>
							<th class="title">标题</th>
							<th class="set_time">
								<a class="co_38f" href="javascript:void(0);">创建时间<span></span></a>
							</th>
							<!-- <th class="prev_send"><a class="co_38f" href="javascript:void(0);">上次发送</a></th> -->
							<th class="options">操作</th>
						</tr>
					</thead>
					<tbody>
						@foreach ( $list as $v )
						<tr>
							<td class="title">
								<div class="item">
									@if ( $v['type'] == 1 )
									<div class="img_text">
										<span class="green">图文</span>
										<a class="co_blue" href="{{ $v['href'] }}" target="_blank" >{{ $v['title'] }}</a>
									</div>
									<div class="read_all clearfix">
                                        <a class="jump" href="{{ $v['href'] }}" target="_blank">
                                            <span>阅读全文</span>
                                            <span class="pull-right">></span>
                                        </a>
									</div>
									@else
										<div class="img_text">
											<span class="green">图文</span>
											<a class="co_blue" href="{{ $v['href'] }}" target="_blank" >{{ $v['title'] }}</a>
										</div>
										@foreach ( $v['_child'] as $val )
										<div class="read_all clearfix">
											<span class="green">图文</span>
											<a class="co_blue" href="{{ $v['href'] }}" target="_blank">{{ $val['title'] }}</a>
										</div>
										@endforeach
									@endif
								</div>
							</td>
							<td class="set_time">{{ $v['created_at'] }}</td>
							<!-- <td class="prev_send">——:——</td> -->
							<td class="options">
								@if ( $v['type'] == 1 )
								<div class="opts">
									<a class="co_38f" href="{{ url('/merchants/wechat/materialAdvancedSingle') }}/{{ $v['id'] }}">编辑</a>-<a class="co_38f delete pop" href="javascript:void(0);"  data-toggle="delete_pop"  data-url="{{ url('/merchants/wechat/materialAdvancedMultiDel') }}/{{ $v['id'] }}">删除</a>
								</div>
								@else
								<div class="opts">
									<a class="co_38f" href="{{ url('/merchants/wechat/materialAdvancedMulti') }}/{{ $v['id'] }}">编辑</a>-<a class="co_38f delete pop" href="javascript:void(0);" data-toggle="delete_pop"  data-url="{{ url('/merchants/wechat/materialAdvancedMultiDel') }}/{{ $v['id'] }}">删除</a>
								</div>
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<div>{{ $pageHtml }}</div>
				<!-- <div class="page clearfix">
					<span class="pull-right">共 2 条，每页 20 条</span>
				</div> -->
   			</div>
			<!--数据列表结束-->
			@endif
		</div>
		<!--新建图文结束-->
	</div>
	<!--主体右侧内容结束-->
	<!-- 中间 结束 -->
	@endsection @section('other')
	<!-- 删除弹框 -->
	<div class="popover delete_pop left" role="tooltip">
		<div class="arrow"></div>
		<div class="popover-content">
			<span>你确定要删除吗？</span>
			<button class="btn btn-primary sure_btn">确定</button>
			<button class="btn btn-default cancel_btn">取消</button>
		</div>
	</div>
	@endsection @section('page_js') @parent
	<!-- 搜索插件 -->
	<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
	<!-- 当前页面js -->
	<script src="{{config('app.source_url')}}mctsource/js/wechat_base.js"></script>
	<script src="{{config('app.source_url')}}mctsource/js/wechat_lrqmvpo1.js"></script>
	<script type="text/javascript">
		//主体左侧列表高度控制
		$('.left_nav').height($('.content').height());
	</script>

	@endsection