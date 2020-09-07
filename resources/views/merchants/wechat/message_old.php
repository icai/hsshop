@extends('merchants.default._layouts') @section('head_css')
<!--jq-ui-timepicker-->
<link type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}/static/js/jQuery-Timepicker/css/jquery-ui-timepicker-addon.css" />
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_82nodvi5.css"> @endsection @section('middle_header')
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
                    <li class="hover">
                        <a href="{{URL('/merchants/wechat/messages')}}">消息托管</a>
                    </li>
                    <li>
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
   					<p>信息托管</p>
   					<span>消息托管模式开启后，不管粉丝给你发什么信息，未触发其他自动回复规则时就会回复以下你设置的内容。
					<br />为了避免让粉丝感觉到骚扰，同样的内容一个小时内只会回复一次。
					</span>
   				</div>
   				<div class="btn1">
   					<button class="active"></button>
   				</div>
   			</div>
   			<div class="handle_content">
   				<hr />
   				<div class="set_info">设置开启条件</div>
   				<form class="form-horizontal" role="form">
   					<div class="from-group">
   						<lebel class="control-label">分时段开启：</lebel>
   						<div class="controls">
   							<input id="time1" class="input_smallest" name="open_time" type="text" value="22:00" />
   							至
   							<input id="time2" class="input_smallest" name="close_time" type="text" value="08:00" />
   						</div>
   					</div>
   					<div class="from-group">
   						<lebel class="control-label">周几生效：</lebel>
   						<div class="controls">
   							<label class="ckbox"><input type="checkbox" name="open_week" value="1" />一</label>
   							<label class="ckbox"><input type="checkbox" name="open_week" value="2" />二</label>
   							<label class="ckbox"><input type="checkbox" name="open_week" value="3" />三</label>
   							<label class="ckbox"><input type="checkbox" name="open_week" value="4" />四</label>
   							<label class="ckbox"><input type="checkbox" name="open_week" value="5" />五</label>
   							<label class="ckbox"><input type="checkbox" name="open_week" value="6" />六</label>
   							<label class="ckbox"><input type="checkbox" name="open_week" value="7" />日</label>
   						</div>
   					</div>
   					<div class="from-group">
   						<lebel class="control-label">空闲时开启：</lebel>
   						<div class="controls">
   							<input class="input_small" name="time_later" type="number" value="10" />分钟前有过对话 
   						</div>
   					</div>
   					<div class="from-group">
   						<lebel class="control-label">不重复回复：</lebel>
   						<div class="controls">
   							<input class="input_small" name="time_reply" type="number" value="120" />分钟前有过对话
   						</div>
   					</div>
   				</form>
   				<!--回复内容开始-->
   				<hr />
   				<div class="set_info">设置回复内容</div>
   				<div class="rule_body clearfix">
   					<div class="rule_left">回复内容：</div>
   					<div class="rule_right">
   						<!--规则右边开始-->
   						<div class="rule_reply">随机回复列表中的一条内容： </div>
   						<div class="right_info">
   							还没有任何回复！
   						</div>
   						<ol class="reply_list">
                             <li class="reply">
                                <span class="reply_type">文本</span><span class="reply_text">123</span>
                                <div class="reply_opts">
                                    <a class="replay_edit" href="javascript:void(0)">编辑</a>
                                    <span>-</span>
                                    <a class="replay_delete" href="javascript:void(0)">删除</a>
                                </div>
                            </li>
                           
                            <li class="reply">
                                <div class="img_text">
                                    <span class="green">图文</span>
                                    <a class="co_blue" href="##">测试</a>
                                </div>
                                <div class="reply_opts">
                                    <a class="replay_edit" href="javascript:void(0)">编辑</a>
                                    <span>-</span>
                                    <a class="replay_delete" href="javascript:void(0)">删除</a>
                                </div>
                            </li>
   						</ol>
   						<div class="rule_add_reply">
   							<a class="co_38f" href="javascript:void(0)">+添加一条回复</a>
   						</div>
   						<!--规则右边结束-->
   					</div>
                </div>
   				<!--回复内容结束-->
				<div class="btn_group"> 
					<button class="btn btn-primary">保存修改</button>
				</div>
       		</div>
        </div>
   		<!--操作部分结束-->
	</div>
</div>
<!--选择图文弹框开始-->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<ul class="list">
					<li class="js_small list_active">高级图文</li>
					<li class="js_item">微信图文</li>
					<li class="js_manage">
						<a class="co_38f" href="javascript:void(0);">微信图文素材管理</a>
					</li>
					<li class="co_000 js_link">
						<a class="co_38f" href="javascript:void(0)">高级图文素材管理</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th class="title">
								<span>标题</span>
								<a class="co_38f" href="javascript:void(0);">刷新</a>
							</th>
							<th class="set_time">创建时间</th>
							<th class="search">
								<input type="text" />
								<button class="btn btn-default">搜</button>
							</th>
						</tr>
					</thead>
					<tbody class="tabel_info">
						<tr>
							<td colspan="3">
								<div class="info">
									没有相关数据
								</div>
							</td>
						</tr>
					</tbody>
					<tbody class="small">
						<tr>
							<td>
								<div class="title_content">
									<div class="img_text">
										<span class="green">图文</span>
										<a class="co_blue" href="javascript:void(0);">123456</a>
									</div>
									<div class="read_all clearfix">
                                        <a class="jump" href="javascript:void(0);">
                                            <span>阅读全文</span>
                                            <span class="pull-right">></span>
                                        </a>
                                    </div>
                                    <div class="read_all clearfix">
                                        <span class="green">图文</span>
                                        <a class="co_blue" href="javascript:void(0);">123456</a>
                                    </div>
								</div>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
					<tbody class="item">
						<tr>
							<td>
								<div class="title_content">
									<div class="img_text">
										<span class="green">图文</span>
										<a class="co_blue" href="javascript:void(0);">123456</a>
									</div>
									<div class="read_all clearfix">
                                        <a class="jump" href="javascript:void(0);">
                                            <span>阅读全文</span>
                                            <span class="pull-right">></span>
                                        </a>
                                    </div>
                                    <div class="read_all clearfix">
                                        <span class="green">图文</span>
                                        <a class="co_blue" href="javascript:void(0);">123456</a>
                                    </div>
								</div>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer clearfix">
				<span class="pull-right">共 0 条，每页 3 条</span>
			</div>
		</div>
	</div>
</div>
<!--选择图文弹框结束-->
<!--选择图文弹框开始-->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<ul class="list">
					<li class="js_small list_active">高级图文</li>
					<li class="js_item">微信图文</li>
					<li class="js_manage">
						<a class="co_38f" href="javascript:void(0);">微信图文素材管理</a>
					</li>
					<li class="co_000 js_link">
						<a class="co_38f" href="javascript:void(0)">高级图文素材管理</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th class="title">
								<span>标题</span>
								<a class="co_38f" href="javascript:void(0);">刷新</a>
							</th>
							<th class="set_time">创建时间</th>
							<th class="search">
								<input type="text" />
								<button class="btn btn-default">搜</button>
							</th>
						</tr>
					</thead>
					<tbody class="tabel_info">
						<tr>
							<td colspan="3">
								<div class="info">
									没有相关数据
								</div>
							</td>
						</tr>
					</tbody>
					<tbody class="small">
						<tr>
							<td>
								<div class="title_content">
									<div class="img_text">
										<span class="green">图文</span>
										<a class="co_blue" href="javascript:void(0);">123456</a>
									</div>
									<div class="read_all clearfix">
										<span class="green">图文</span>
										<a class="co_blue" href="javascript:void(0);">123456</a>
									</div>
								</div>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
					<tbody class="item">
						<tr>
							<td>
								<div class="title_content">
									<div class="img_text">
										<span class="green">图文</span>
										<a class="co_blue" href="javascript:void(0);">123456</a>
									</div>
									<div class="read_all clearfix">
										<span>阅读全文</span>
										<span class="pull-right">></span>
									</div>
								</div>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer clearfix">
				<span class="pull-right">共 0 条，每页 3 条</span>
			</div>
		</div>
	</div>
</div>
<!--选择图文弹框结束-->
<!--微页面模态框开始-->
<div class="modal" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<ul class="list">
					<li class="js_small list_active">微页面</li>
					<li class="js_item">微页面分类</li>
					<li class="js_manage">
						<a class="co_38f" href="javascript:void(0);">分类管理</a>
					</li>
					<li class="co_000 js_link">
						<a class="co_38f" href="javascript:void(0)">新建微页面</a>-
						<a class="co_38f" href="javascript:void(0)">草稿管理</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th class="title">
								<span>标题</span>
								<a class="co_38f" href="javascript:void(0);">刷新</a>
							</th>
							<th class="set_time">创建时间</th>
							<th class="search">
								<input type="text" />
								<button class="btn btn-default">搜</button>
							</th>
						</tr>
					</thead>
					<tbody class="small">
						<tr>
							<td><a class="co_38f" href="javascript:void(0);">手机数码 - 01</a></td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
						<tr>
							<td><a class="co_38f" href="javascript:void(0);">手机数码 - 01</a></td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
					<tbody class="item">
						<tr>
							<td><a class="co_38f" href="javascript:void(0);">最新商品</a></td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
						<tr>
							<td><a class="co_38f" href="javascript:void(0);">最热商品</a></td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer clearfix">
				<span class="pull-right">共 0 条，每页 3 条</span>
			</div>
		</div>
	</div>
</div>
<!--微信模态框结束-->
<!--商品模态框开始-->
<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<ul class="list">
					<li class="js_small list_active">已上架商品</li>
					<li class="co_000 js_link">
						<a class="co_38f" target="_blank" href="{{URL('/merchants/product/create')}}">新建商品</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th class="title">
								<span>标题</span>
								<a class="co_38f" href="javascript:void(0);">刷新</a>
							</th>
							<th class="set_time">创建时间</th>
							<th class="search">
								<input type="text" />
								<button class="btn btn-default">搜</button>
							</th>
						</tr>
					</thead>
					<tbody class="small">
						<tr>
							<td>
								<img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fs2_zeLW79XAF9YDcESRmEgWwl1e.jpg"/>
								<a class="co_38f" href="javascript:void(0);">实物商品（购买时需填写收货地址，测试商品，不发货，不退款）</a>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
						<tr>
							<td>
								<img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fs2_zeLW79XAF9YDcESRmEgWwl1e.jpg"/>
								<a class="co_38f" href="javascript:void(0);">实物商品（购买时需填写收货地址，测试商品，不发货，不退款）</a>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
					<tbody class="item">
						<tr>
							<td><a class="co_38f" href="javascript:void(0);">女装</a></td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
						<tr>
							<td><a class="co_38f" href="javascript:void(0);">男装</a></td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer clearfix">
				<span class="pull-right">共 0 条，每页 3 条</span>
			</div>
		</div>
	</div>
</div>
<!--商品模态框结束-->		
@endsection @section('page_js') @parent
<script type="text/javascript" src="http://code.jquery.com/ui/1.9.1/jquery-ui.min.js"></script>
<script src="{{config('app.source_url')}}static/js/jQuery-Timepicker/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="{{config('app.source_url')}}static/js/jQuery-Timepicker/js/jquery-ui-timepicker-zh-CN.js" type="text/javascript"></script>
<!--jq扩充包用于qq表情-->
<script src="{{config('app.source_url')}}static/js/jquery-browser.js"></script>
<!--qq表情包插件-->
<script src="{{config('app.source_url')}}static/js/jquery.qqFace.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
	var domain_url = "{{config('app.source_url')}}";
</script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_82nodvi5.js"></script>
@endsection