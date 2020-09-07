<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />-->
	<title>{{ $title }}</title>
	<link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
	<script type="text/javascript">
        function isIE(){
            var navigatorName = "Microsoft Internet Explorer"; 
            var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
            var isEdge = userAgent.indexOf("Edge") > -1; //判断是否IE的Edge浏览器
            var isIE = false;
            if( isEdge || (!!window.ActiveXObject || "ActiveXObject" in window)){
                document.write("<div style='width: 450px; margin: 50px auto;font-size:20px;'>请下载谷歌浏览器<a style= 'color: #38f;margin-left:10px;' href='http://www.google.cn/intl/zh-CN/chrome/browser/desktop/index.html'>谷歌浏览器下载</a></div>")
                window.stop ? window.stop() : document.execCommand("Stop");
            }
        }
        isIE();
    </script>
	<!-- 核心Bootstrap.css文件（每个页面引入） -->
	<link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
	<!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/bootstrap-rewrite.css">
	<!-- 核心base.css文件（每个页面引入） -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/static/css/base.css">
	<!-- 表单验证插件 -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/js/bootstrapvalidator/dist/css/bootstrapValidator.css">
	<!-- 当前页面css -->
	<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/auth_os0x2ynv.css">
	
	<script>!function(e){function t(a){if(i[a])return i[a].exports;var n=i[a]={exports:{},id:a,loaded:!1};return e[a].call(n.exports,n,n.exports,t),n.loaded=!0,n.exports}var i={};return t.m=e,t.c=i,t.p="",t(0)}([function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=window;t["default"]=i.flex=function(e,t){var a=e||100,n=t||1,r=i.document,o=navigator.userAgent,d=o.match(/Android[\S\s]+AppleWebkit\/(\d{3})/i),l=o.match(/U3\/((\d+|\.){5,})/i),c=l&&parseInt(l[1].split(".").join(""),10)>=80,p=navigator.appVersion.match(/(iphone|ipad|ipod)/gi),s=i.devicePixelRatio||1;p||d&&d[1]>534||c||(s=1);var u=1/s,m=r.querySelector('meta[name="viewport"]');m||(m=r.createElement("meta"),m.setAttribute("name","viewport"),r.head.appendChild(m)),m.setAttribute("content","width=device-width,user-scalable=no,initial-scale="+u+",maximum-scale="+u+",minimum-scale="+u),r.documentElement.style.fontSize=a/2*s*n+"px"},e.exports=t["default"]}]);  flex(100, 1);</script>	
</head>
<body>
	<!--主体开始-->
	<div class="container">
		<!--头部开始-->
		<div class="header clearfix">
			<!--<div class="header_login">
				<a href="{{ URL('/auth/register') }}">免费注册，轻松开店</a>
			</div>-->
			<a  class="header_logo" href="/"><img src="{{config('app.source_url')}}mctsource/images/gupiaodaima11.png"/></a>
			<div class="header_title">
				登录
			</div>
		</div>
		<!--头部结束-->
		<!--内容开始-->
		<div class="main clearfix">
			<!--左边开始-->
			<div class="main_left">
				<form id="login" class="form-horizontal" role="form" method="post" action="{{ URL('/auth/login') }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label for="number" class="col-sm-3 control-label">手机号码：</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" name="mphone" maxlength="11"  placeholder="账号是手机号" />
						</div>
					</div>
					<div class="form-group">
						<label for="psd" class="col-sm-3 control-label">登录密码：</label>
						<div class="col-sm-6">
							<input  class="form-control" type="password" name="password" id="psd" placeholder="请输入密码" />
						</div>
					</div>
					<div class="form-group test">
						<label for="psdTwo" class="col-sm-3 control-label">验证码：</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" name="captcha" id="psdTwo" placeholder="请输入验证码" />
							<img id="captcha_img" class="captcha_img" src="{{ captcha_src('flat') }}" onClick="this.src='{{ captcha_src('flat') }}?random='+Math.random();" />
						</div>
					</div>
					<div class="form-group font12">
						<label class="col-sm-offset-4">
							<input type="checkbox" class="pad0" name="remember" style="margin-left: 10px">三天内自动登录
						</label>
						<a class="fl" href="/auth/forgetpsd" >忘记密码？</a>
					</div>
					<input class="btn btn-large btn-primary btn-signup" value="登录"/ type="button">
				</form>
			</div>
			<!--左边结束-->
			<!--右边开始-->
			<!-- <div class="main_right">
				<div class="attention">
					<img src="{{config('app.source_url')}}home/image/huisouyun_120.png"/>
				</div>
				<p class="font14">下载手机客户端</p>
				<p>随时随地管理您的店铺</p>
				<div class="download">
					<button class="btn btn-success" data-container="body"><span class="icon"></span>期待上线!</button>
					
					<div class="more clearfix">
						<h4>使用微信扫码，直接下载安装</h4>
						<div class="more_left">
							<h5>iOS版</h5>
							<a href="javascript:void(0)">
								
								App Store 
							</a>
						</div>
						<div class="more_right">
							<h5>Android版</h5>
							<a href="javascript:void(0)">
								
								直接下载
							</a>
						</div>
					</div>
					
				</div>
			</div> -->
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
	<!-- <script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script> -->
	<!-- 表单验证插件 -->
	<script src="{{config('app.source_url')}}static/js/bootstrapvalidator/dist/js/bootstrapValidator.js"></script>
	<!-- 核心 base.js JavaScript 文件 -->
	<script src="{{config('app.source_url')}}mctsource/static/js/base.js"></script>
	<!-- 当前页面js -->
	<script src="{{config('app.source_url')}}mctsource/js/auth_os0x2ynv.js"></script>
	<script type="text/javascript">
		function reImg(){  
		    var img = document.getElementById("captcha_img");  
		    img.src = "{{ captcha_src('flat') }}?random="+Math.random();  
		}  
	</script>
</body>
</html>
