@extends('merchants.default._layouts') @section('head_css')
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_1ynk47v5.css"> 
@endsection @section('middle_header')
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
@include('merchants.wechat.slidebar')
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
	<!--主体左侧列表开始-->
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<div class="login">
			<form class="form-horizontal" role="form">
				<div class="control-group">
					<label class="control-label">公众微信号:</label>
					<div class="controls clearfix">
						@if(!empty($wechat_id))
						<div class="controls_action">
							<span>{{ $wechat_id }}</span>
						</div>
						@else
						<div class="controls_action">
							<span style="color: #999;">你的公众号暂未设置微信号，可设置并重新授权</span>
							<a class="co_38f" href="https://mp.weixin.qq.com/" target="_blank">去设置></a>
						</div>
						@endif
						<a class="co_38f ctl_opts" href="##" data-toggle="modal" data-target="#myModal">绑定到其它微信号</a>
						<!-- 模态框（Modal） -->
						<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="myModalLabel">提示</h4>
									</div>
									<div class="modal-body">
										<p>解除绑定微信号，会造成当前店铺的重要信息丢失（包括图文素材、自动回复、自定义菜单等），请谨慎操作；</p>
										<label>
                                            <input type="checkbox" class="remove" />已知晓解除绑定的风险，确认解绑
                                        </label>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn co_bbb">解除绑定</button>
										<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
									</div>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal -->
						</div>

					</div>
				</div>
				<div class="control-group clearfix">
					<label class="control-label">公众号昵称:</label>
					<div class="controls">
						<div class="controls_action">
							<span>{{ $nick_name }}</span>
						</div>
					</div>
				</div>
				<div class="control-group clearfix">
					<label class="control-label">微信账号类型:</label>
					<div class="controls">
						<div class="controls_action">
							@if($service_type_info == 2)
							<span>认证服务号</span>
							@else
							<span>未认证服务号</span>
							@endif
						</div>
						<div class="line"></div>
						<div class="controls_action">
							账号已升级？点此：
							<a class="co_38f set" href="javascript:;" target="_blank" onclick="goset()">更新授权</a>
							<!-- 模态框（Modal） -->
							<div class="modal" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="myModalLabel">提示</h4>
										</div>
										<div class="modal-body">
											<p>请你注意:</p>
											<ol>
												<li>必须使用当前绑定的公众号进行授权，否则将可能导致某些重要数据丢失或其它异常情况</li>
												<li>为保证您在会搜云平台功能的正常使用，授权时请保持默认选择，把权限统一授权给会搜云。</li>
											</ol>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-primary pull-left btn-deltete">解除绑定</button>
										</div>
									</div>
									<!-- /.modal-content -->
								</div>
								<!-- /.modal -->
							</div>
						</div>
						<div class="help_icon">
							<i class="glyphicon glyphicon-question-sign"></i>
							<div class="help_info">
								<p>如果您的公众号已成功升级（从未认证升为认证号，或从订阅号升为服务号），请点击更新授权</p>
								<strong>如何升级：</strong>
								<p>如需对公众号进行微信认证，请登录“微信公众平台-> 公众号设置”，在“认证情况”栏目，点击申请微信认证</p>
							</div>
						</div>
					</div>
				</div>
				<div class="control-group">
					<div class="controls hint">
						<p>您的店铺已获得该公众号的所有接口权限，可以正常对接微信</p>
						<p>
							如果使用中发现接口有异常，请点此
							<a class="co_38f" href="##" data-toggle="modal" data-target="#myModal1">重新授权</a>试试
							<a class="co_blue" href="https://www.huisou.cn/home/index/helpDetail/771">查看教程</a>
						</p>
					</div>
				</div>
				<!-- <div class="control-group btn_group">
					20180718 梅杰 隐藏 <input class="btn btn-primary" type="submit" value="保存">
				</div> -->
			</form>
		</div>
		<!-- <div class="code">
			<img src="https://open.weixin.qq.com/qr/code/?username=My--space" />
		</div> -->
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection @section('page_js') @parent
<!-- 搜索插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<script type="text/javascript">
    //主体左侧列表高度控制
    $('.left_nav').height($('.content').height());
    var domain_url = "{{config('app.source_url')}}";
    var imgUrl = "{{ imgUrl() }}";
</script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_1ynk47v5.js"></script>
<script type="text/javascript">
	function goset(){
		$.ajax({
			type: "GET",
			async: false,
			url: '/merchants/wechat/authRedirect',
			success: function(data) {
				$('.set').attr('href',data.url + 'updateauthorized');
			}
		});
		return true;
	}
</script>
@endsection