<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>我们结婚啦</title>
		<link rel="stylesheet" href="//cdn.bootcss.com/weui/1.1.1/style/weui.min.css">
		<link rel="stylesheet" href="//cdn.bootcss.com/jquery-weui/1.0.1/css/jquery-weui.min.css">
		<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}applet/invitation/static/css/index.css" />
		<link rel="stylesheet/less" type="text/css" href="{{ config('app.source_url') }}applet/invitation/public/css/index.less">
	</head>

	<body>
		<div class="app">
			<div class="title">我和男神女神的婚礼</div>
			<div class="content">
				<!--顶部图片-->
				<img id="topTitleImg" src="{{ config('app.source_url') }}applet/invitation/public/images/topImg@3x.png" />
				<!--主要内容显示-->
				<div class="weui-cells weui-cells_form">
					<!--选择样式-->
					<div class="weui-cell firstLabel">
						<div class="weui-cell__hd"><label class="weui-label">选择样式：</label></div>
					</div>
					<div class="style_img">
						<img src="{{ config('app.source_url') }}applet/invitation/public/images/jhqj@3x.png" data-type="1" class="active">
						<img src="{{ config('app.source_url') }}applet/invitation/public/images/jhz@3x.png" data-type="2">
						<img src="{{ config('app.source_url') }}applet/invitation/public/images/qj@3x.png" data-type="3">
					</div>
				</div>
				<div class="weui-cells weui-cells_form">
					<!--新郎姓名-->
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">新郎姓名：</label></div>
						<div class="weui-cell__bd">
							<input id="man" class="weui-input" type="text" placeholder="例子：陈程">
						</div>
					</div>
					<!--新娘姓名-->
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">新娘姓名：</label></div>
						<div class="weui-cell__bd">
							<input id="woman" class="weui-input" type="text" placeholder="例子：范冰冰">
						</div>
					</div>
					<!--结婚登记日期-->
					<div class="weui-cell type_1 type_2">
						<div class="weui-cell__hd"><label for="" class="weui-label">结婚登记日期：</label></div>
						<div class="weui-cell__bd">
							<input id="my-input" class="picker" value="" placeholder="请选择结婚登记日期">
						</div>
					</div>
				</div>
				<div class="weui-cells weui-cells_form type_1 type_3">
					<!--爱情宣言-->
					<div class="weui-cell">
						<div class="weui-cell__hd"><label for="" class="weui-label">爱情宣言：</label></div>
					</div>
					<div class="radios">
						<p class="flex-star" data-index="1"><img src="public/images/coupon_use_select@2x.png" width="20" /><span>这一生我只牵你的手，因为有你就足够！</span></p>
						<p class="flex-star" data-index="2"><img src="public/images/coupon_use_normal@2x.png" width="20" /><span>陪你疯狂千世，陪我万事轮回！</span></p>
						<p class="flex-star" data-index="3"><img src="public/images/coupon_use_normal@2x.png" width="20" /><span>你的世界很大，而我的世界只有你!</span></p>
						<p class="flex-star" data-index="4"><img src="public/images/coupon_use_normal@2x.png" width="20" /><span><input id="manifestoSelf" placeholder="自定义：请写下你的爱情宣言!"/></span></p>
					</div>
				</div>
				<div class="weui-cells weui-cells_form type_1 type_3">
					<!--婚礼日期-->
					<div class="weui-cell">
						<div class="weui-cell__hd"><label for="" class="weui-label">婚礼日期：</label></div>
						<div class="weui-cell__bd">
							<input id="datetime-picker" class="picker" placeholder="请选择婚礼日期">
						</div>
					</div>
					<!--酒店-->
					<div class="weui-cell">
						<div class="weui-cell__hd"><label for="" class="weui-label">酒店：</label></div>
						<div class="weui-cell__bd">
							<input type="text" id='picker' class="picker" placeholder="请选择酒店"/>
						</div>
					</div>
				</div>
				<div class="submit">
					<button>生成请柬</button>
				</div>
			</div>
		</div>
	</body>
	<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
	<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/jquery-weui.min.js"></script>
	<script src="{{ config('app.source_url') }}applet/invitation/static/js/less.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="{{ config('app.source_url') }}applet/invitation/public/js/index.js" type="text/javascript" charset="utf-8"></script>
</html>