<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
	<title></title>
	<!-- 核心Bootstrap.css文件（每个页面引入） -->
	<link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
	<!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/bootstrap-rewrite.css">
	<!-- 核心base.css文件（每个页面引入） -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/static/css/base.css">
	<!-- 表单验证插件 -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/js/bootstrapvalidator/dist/css/bootstrapValidator.css">
	<!-- 当前页面css -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/forgetpsd.css">
</head>
<body>
	<!--主体开始-->
	<div class="container">
		<!--头部开始-->
		<div class="header clearfix">
			<div class="header_login">
				<a href="{{URL('/auth/login')}}">登录</a>
			</div>
			<a  class="header_logo" href="javascript:void(0)">
				<img src="{{ config('app.source_url') }}mctsource/images/gupiaodaima11.png" style="margin:11px 0 0 2px;"/>
			</a>
			<div class="header_title">
				找回密码
			</div>
		</div>
		<!--头部结束-->
		<!--内容开始-->
		<div class="main clearfix">
			<!--左边开始-->
			<div class="main_left">
				<form id="register" class="form-horizontal" role="form" method="post" action="/auth/forgetpassword/update">
					<div class="form-group">
						<label for="number" class="col-sm-3 control-label"><span class="co_d00">*</span>手机号码：</label>
						<div class="col-sm-6">
							<input id="number" class="form-control" type="text" name="mphone"  placeholder="账号是手机号" />
						</div>
					</div>
					<div class="form-group note_p">
						<label for="note" class="col-sm-3 control-label"><span class="co_d00">*</span>短信校验码：</label>
						<div class="col-sm-3">
							<input  id="note" class="form-control get_note" type="text" name="code" placeholder="填写4位短信验证码" />
						</div>
						<button class="btn btn-primary send" type="button" style="left: 0;">获取验证码</button>
					</div>
					<div class="form-group">
						<label for="psd" class="col-sm-3 control-label"><span class="co_d00">*</span>设定新密码：</label>
						<div class="col-sm-6">
							<input  class="form-control" type="password" name="password" id="psd" placeholder="8~20位字符" />
						</div>
					</div>
					<input class="btn btn-large btn-primary btn-signup change" type="submit" value="确认修改"/>
					{!! csrf_field() !!}
				</form>
			</div>
			<!--左边结束-->
			<!--右边开始-->
			<div class="main_right">
				<!--<div class="attention">
					
				</div>
				<p>关注官方微信，了解更多资讯</p>-->
			</div>
			<!--右边结束-->
		</div>
		<!--内容结束-->
		@include('auth.footer')
	</div>
	<!--主体结束-->
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
	<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
	<!-- 图表插件 -->
	<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
	<!-- 公共js -->
	<script type="text/javascript" src="{{config('app.source_url')}}mctsource/static/js/base.js"></script>
	<!-- 表单验证插件 -->
	<script src="{{config('app.source_url')}}/static/js/bootstrapvalidator/dist/js/bootstrapValidator.js"></script>
	<!-- 当前页面js -->
	<script src="{{config('app.source_url')}}mctsource/js/forgetpsd.js"></script>
</body>
</html>
