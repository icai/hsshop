<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
	<title>{{$title}}</title>
	<link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
	<!-- 核心Bootstrap.css文件（每个页面引入） -->
	<link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
	<!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/bootstrap-rewrite.css">
	<!-- 核心base.css文件（每个页面引入） -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/static/css/base.css">
	<!-- 表单验证插件 -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/js/bootstrapvalidator/dist/css/bootstrapValidator.css">
	<!-- 当前页面css -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/auth_km1bqfcd.css">
	<script>!function(e){function t(a){if(i[a])return i[a].exports;var n=i[a]={exports:{},id:a,loaded:!1};return e[a].call(n.exports,n,n.exports,t),n.loaded=!0,n.exports}var i={};return t.m=e,t.c=i,t.p="",t(0)}([function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=window;t["default"]=i.flex=function(e,t){var a=e||100,n=t||1,r=i.document,o=navigator.userAgent,d=o.match(/Android[\S\s]+AppleWebkit\/(\d{3})/i),l=o.match(/U3\/((\d+|\.){5,})/i),c=l&&parseInt(l[1].split(".").join(""),10)>=80,p=navigator.appVersion.match(/(iphone|ipad|ipod)/gi),s=i.devicePixelRatio||1;p||d&&d[1]>534||c||(s=1);var u=1/s,m=r.querySelector('meta[name="viewport"]');m||(m=r.createElement("meta"),m.setAttribute("name","viewport"),r.head.appendChild(m)),m.setAttribute("content","width=device-width,user-scalable=no,initial-scale="+u+",maximum-scale="+u+",minimum-scale="+u),r.documentElement.style.fontSize=a/2*s*n+"px"},e.exports=t["default"]}]);  flex(100, 1);</script>
</head>
<body>
	<!--主体开始-->
	<div class="container">
		<!-- 默认为1时可注册 -->
		<input type="hidden" name="hide" value="{{ $close }}">
		@if($close != 1)
		<div class="register_info">
			<span class="info " style="background-color: green; color: #fff;width: 100%;display: inline-block;padding:3px 0;">注册功能关闭，邀请注册中</span>
		</div>
		@endif
		<!--头部开始-->
		<div class="header clearfix">
			<div class="header_login">
				<a href="{{URL('/auth/login')}}">已有账号，立即登录</a>
			</div>
			<a  class="header_logo" href="/">
				<img src="{{ config('app.source_url') }}mctsource/images/gupiaodaima11.png" style="margin:3px 0 0 0; width: 105px; height: 36px;"/>
			</a>
			<div class="header_title">
				免费注册
			</div>
		</div>
		<!--头部结束-->
		<!--内容开始-->
		<div class="main clearfix">
			<!--左边开始-->
			<div class="main_left">
				<form id="register" class="form-horizontal" role="form" method="post" action="{{ URL('/auth/register') }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label for="number"class="col-sm-3 control-label"><span class="co_d00">*</span>手机号码：</label>
						<div class="col-sm-6">
							<input id="number" class="form-control" type="text" name="mphone"  placeholder="今后使用手机登录" />
						</div>
					</div>
					<!-- <div class="form-group test">
						<label for="psdTwo" class="col-sm-3 control-label"><span class="co_d00">*</span>验证码：</label>
						<div class="col-sm-3">
							<input class="form-control get_note" type="text" name="captcha" id="psdTwo" placeholder="请输入验证码" />
						</div>
						<img id="captcha_img" class="captcha_img" src="{{ captcha_src('flat') }}" onClick="this.src='{{ captcha_src('flat') }}?random='+Math.random();" />
					</div> -->
					<div class="form-group note_p">
						<label for="note" class="col-sm-3 control-label"><span class="co_d00">*</span>短信校验码：</label>
						<div class="col-sm-4">
							<input id="note" class="form-control get_note" type="text" name="code" placeholder="请输入短信验证码" />
						</div>
						<button class="btn btn-default btn_disabled get_code" type="button" >获取验证码</button>
					</div>
					<div class="form-group">
						<label for="name" class="col-sm-3 control-label"><span class="co_d00">*</span>个人昵称：</label>
						<div class="col-sm-6">
							<input  class="form-control " type="text" name="name" id="name" placeholder="行不更名，坐不改姓" />
						</div>
					</div>
					<div class="form-group">
						<label for="psd" class="col-sm-3 control-label"><span class="co_d00">*</span>设置密码：</label>
						<div class="col-sm-6">
							<input  class="form-control" type="password" name="password" id="psd" placeholder="8~20位字符" />
						</div>
					</div>
					
					<div class="form-group">
						<label for="psdTwo" class="col-sm-3 control-label"><span class="co_d00">*</span>确认密码：</label>
						<div class="col-sm-6">
							<input  class="form-control" type="password" name="password_confirmation" id="psdTwo" placeholder="再输一次" />
						</div>
					</div>
					
					<input class="btn btn-large btn-primary btn-signup" type="submit" value="确认注册"/>
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
	<!-- 公共js -->
	<script type="text/javascript" src="{{config('app.source_url')}}mctsource/static/js/base.js"></script>
	<!-- 表单验证插件 -->
	<script src="{{config('app.source_url')}}/static/js/bootstrapvalidator/dist/js/bootstrapValidator.js"></script>
	<!-- 当前页面js -->
	<script src="{{config('app.source_url')}}mctsource/js/auth_km1bqfcd.js"></script>
</body>
</html>
