<!DOCTYPE html>
<html class="" lang="zh-cmn-Hans">
	<head>
		<meta charset="utf-8">
		<meta name="keywords" content="会搜,移动电商服务平台" />
		<meta name="description" content="" />
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="format-detection" content="telephone=no">
		<meta http-equiv="cleartype" content="on">
		<meta name="referrer" content="always">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
		<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
		<title>@yield('title')</title>
		@yield('head_css')
	</head>
	<body class=" ">
		<div class="container " style="min-height: 600px;">
			<div class="header">
				<div class="js-mp-info share-mp-info ">
					<a class="page-mp-info" href="##">
						<img class="mp-image" width="24" height="24" src="{{ config('app.source_url') }}shop/member/hsadmin/image/sy_2010091620583620405.jpg" />
						<i class="mp-nickname"> {{ $members['truename']  or $members['nickname'] }} </i>
					</a>
					<div class="links">
						<span class="js-search mp-search search-icon"></span>
						<a class="mp-homepage" href="##">我的记录</a>
					</div>
				</div>
			</div>
			@yield('main')
		</div>
	</body>
    <script type="text/javascript">
        var APP_HOST = "{{ config('app.url') }}";
        var APP_IMG_URL = "{{ imgUrl() }}";
        var APP_SOURCE_URL = "{{ config('app.source_url') }}";
        var CHAT_URL = "{{config('app.chat_url')}}";
    </script>
	@if(config('app.env') == 'prod')
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
	@endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
   <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script type="text/javascript">
    </script>
	@yield('js')
</html>