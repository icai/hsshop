<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>我们结婚啦</title>
		<link rel="stylesheet" href="//cdn.bootcss.com/weui/1.1.1/style/weui.min.css">
		<link rel="stylesheet" href="//cdn.bootcss.com/jquery-weui/1.0.1/css/jquery-weui.min.css">
		<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}applet/invitation/static/css/index.css" />
		<link rel="stylesheet/less" type="text/css" href="{{ config('app.source_url') }}applet/invitation/public/css/showImg.less">
	</head>
	<body>
		<div class="app">
			<div id="back"><span class="backFun"></span></div>
			<div class="title">我和男神女神的婚礼</div>
			<div class="content">
				<!--生成图片区域-->
				<div class="createImg">
					<img id="browserImg" src="{{ imgUrl($imgSrc) }}"/>
					<a id="saveImg" href="{{ imgUrl($imgSrc) }}" download="下载的图片">长按图片保存到相册</a>
				</div>
				<!--文案区域-->
				<div class="wordsDiv">
					<p>朋友圈参考文案：</p>
					<div class="words">
						是的，没错～这是真的，你没看错！我知道比较突然，但是。。。你们懂得。我们一路走来，真的很不容易。希望得到大家的祝福！
						<span class="wordsChange"></span>
					</div>
				</div>
				<p class="copy" data-clipboard-target=".words">复制文案</p>
			</div>
		</div>
	</body>
	<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
	<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/jquery-weui.min.js"></script>
	<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/swiper.min.js"></script>
	<script src="{{ config('app.source_url') }}applet/invitation/static/js/less.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="{{ config('app.source_url') }}applet/invitation/static/js/clipboard.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="{{ config('app.source_url') }}applet/invitation/public/js/showImg.js" type="text/javascript" charset="utf-8"></script>
</html>
