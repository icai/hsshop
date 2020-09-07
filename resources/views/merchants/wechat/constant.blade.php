@extends('merchants.default._layouts') @section('head_css')
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_2bfBjnCv.css" /> @endsection @section('slidebar') @include('merchants.wechat.slidebar') @endsection @section('middle_header')
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
<div class="content" style="display: -webkit-box;">
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
					<li class="hover status">
						<a href="javascript:void(0);">我的所有信息</a>
					</li>
					<li class="status">
						<a href="javascript:void(0);">未读信息</a>
					</li>
					<li class="status">
						<a href="javascript:void(0);">已备注</a>
					</li>
					<li class="status">
						<a href="javascript:void(0);">已加星</a>
					</li>
					<li class="ml10">
						<div class="dropdown">
							<p id="dropdownMenu1" data-toggle="dropdown">
								导出消息
								<span class="caret"></span>
							</p>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
								<li role="presentation">
									<a role="menuitem" tabindex="-1" href="javascript:void(0)">一天</a>
								</li>
								<li role="presentation">
									<a role="menuitem" tabindex="-1" href="javascript:void(0)">两天</a>
								</li>
								<li role="presentation">
									<a role="menuitem" tabindex="-1" href="javascript:void(0)">三天</a>
								</li>
							</ul>
						</div>
					</li>
				</ul>
				<!-- 导航 结束 -->
			</div>
			<!-- 左侧 结算 -->
			<!-- 右边 开始 -->
			<div class="pull-right search_module">
				<!-- 搜索 开始 -->
				<label class="search_items">
                    <input class="search_input" type="text" name="" value="" placeholder="搜索"/>   
                </label>
				<!-- 搜索 结束 -->
			</div>
			<!-- 右边 结束 -->
		</div>
		<!-- 导航模块 结束 -->

		<!--中间数据开始-->
		<ul class="container_data">
			<li>
				<a class="co_38f" href="javascript:void(0)">0</a>待接待的客户</li>
			<li>
				<a class="co_38f" href="javascript:void(0)">0</a>我接待中的客户</li>
			<li>
				<a class="co_38f" href="javascript:void(0)">0</a>我的客户</li>
			<li>
				<a class="co_000" href="javascript:void(0)">0</a>我未回复的客户</li>
		</ul>
		<!--中间数据结束-->
		<div class="no_result">
			还没有相关数据
		</div>
		<table class="table">
			<thead>
				<tr>
					<th class="wd_20 align_left">会员</th>
					<th class="wd_50 align_left">信息（显示自动回复的）</th>
					<th class="wd_15 align_left">时间</th>
					<th class="wd_15 align_right">操作</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="wd_20 align_left">
						<div class="client">
							<figure>
								<img src="http://s9.sinaimg.cn/middle/6833c8fanaff5e0aaffc8&690">
							</figure>
							<h5 class="client_name">霍光辉</h5>
						</div>
					</td>
					<td class="wd_50 align_left">
						<p class="reply_text">这是我的回复</p>
						<!-- <a class="reply_img" href="http://s9.sinaimg.cn/middle/6833c8fanaff5e0aaffc8&690" target="_blank">
                            <img src="http://s9.sinaimg.cn/middle/6833c8fanaff5e0aaffc8&690" >
                        </a> -->
						<p class="click_reply co_0099" data-toggle="modal" data-target="#quick_modal">点击快速回复</p>
					</td>
					<td class="wd_15 align_left time">
						2017-01-19 10:42:29
					</td>
					<td class="wd_15 align_right">
						<div class="opts">
							<span class="add_star">加星</span> |
							<span class="remark">备注</span>
						</div>
					</td>
				</tr>
				<tr>
					<td class="wd_20 align_left">
						<div class="client">
							<figure>
								<img src="http://s9.sinaimg.cn/middle/6833c8fanaff5e0aaffc8&690">
							</figure>
							<h5 class="client_name">霍光辉</h5>
						</div>
					</td>
					<td class="wd_50 align_left">
						<p class="reply_text">这是我的回复</p>
						<!--  <a class="reply_img" href="http://s9.sinaimg.cn/middle/6833c8fanaff5e0aaffc8&690" target="_blank">
                            <img src="http://s9.sinaimg.cn/middle/6833c8fanaff5e0aaffc8&690" >
                        </a> -->
						<p class="click_reply" data-toggle="modal" data-target="#quick_modal">点击快速回复</p>
					</td>
					<td class="wd_15 align_left time">
						2017-01-19 10:42:29
					</td>
					<td class="wd_15 align_right">
						<div class="opts">
							<span class="add_star">加星</span> |
							<span class="remark">备注</span>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="footer_page">
			<span>共<i class="count">1</i>条，每页10条</span>
		</div>
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 快捷短语模态框开始（Modal） -->
<div class="modal" id="quick_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">
                    新建快速回复
                </h4>
			</div>
			<div class="modal-body">
				<textarea class="quick_modal_textarea" maxlength="500"></textarea>
				<div>
					<span class="font_ctl">最多输入<i class="js_quick_ctl">500</i>字</span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button type="button" class="btn btn-success">确定</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal -->
</div>
<!-- 快捷短语模态框结束 -->
@endsection @section('page_js')
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>

<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_2bfBjnCv.js"></script>
@endsection