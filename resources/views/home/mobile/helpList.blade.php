<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"> 
    <meta http-equiv="X-UA-Compatible" content="chrome=1"><meta name="360-site-verification" content="43c040d547ea865dccc559d9ebe3fb9e" />
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <!--<meta name="keywords" content="会搜云微商城,会搜,会搜科技有限公司">
    <meta name="description" content="会搜股份【股票代码：837521】荣誉出品，会搜云专注做APP定制全套
解决方案，将原生App + H5网页版+ 微信小程序（Hot！）一并打通！
用心服务于 电商大商家/中大型企业客户…">-->
    <meta name="renderer" content="webkit">
    <meta name="baidu-site-verification" content="HIIZCPonde" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ config('app.source_url') }}home/image/icon_logo.png" type="image/png" />
	<title>{{ $title or '' }}</title>
	<script src="{{ config('app.source_url') }}mobile/js/rem.js"></script>  
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/reset.css">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/nav.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/helpList.css">
</head>
<body> 
	<!-- 顶部 -->
	<div class="header">
        <a href="javascript:history.go(-1)" class="go-back">
            <i class="icon-arrow"></i>
        </a>
        <div class="icon-logo"></div>
		<div class="icon-box">
			<i class="icon-nav"></i>
		</div>
		<nav class="nav none">
			<div class="nav-box">
				<div class="nav-header">
					<a href="/" class="index-link">
						<i class="index-logo"></i>
					</a>
					<div class="icon-box">
						<i class="icon-nav-close"></i>
					</div>
				</div>
				<ul class="nav-list ">
					<li class="nav-item">
						<a href="/">
							<span>首页</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="J_more-list">
							<span>产品服务</span>
							<i class="nav-item-icon"></i>
						</a>
						<ul class="nav-sub-list none">
							<li class="nav-item">
								<a href="/home/index/appCustomize">APP定制</a>
							</li>
							<li class="nav-item">
								<a href="/home/index/applet">微信小程序</a>
							</li>
							<li class="nav-item">
								<a href="/home/index/microshop">微商城系统</a>
							</li>
							<li class="nav-item">
								<a href="/home/index/distribution">分销系统</a>
							</li>
							<li class="nav-item">
								<a href="/home/index/microMarketing">微营销总裁班</a>
							</li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="J_more-list">
							<span>营销应用</span>
							<i class="nav-item-icon"></i>
						</a>
						<ul class="nav-sub-list nav-three none">
							<li class="nav-item">
								<a class="border-b J_more-list">
									<span>经营渠道</span>
									<i class="nav-item-icon"></i>
								</a>
								<ul class="nav-sub-list none">
									<li class="nav-item">
										<a href="/home/index/manageChannel/detail/1">
											<span>微信小程序</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/manageChannel/detail/2">
											<span>微信公众号</span>
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a class="border-b J_more-list">
									<span>促销折扣</span>
									<i class="nav-item-icon"></i>
								</a>
								<ul class="nav-sub-list none">
									<li class="nav-item">
										<a href="/home/index/salesDiscount/detail/1">
											<span>优惠券</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesDiscount/detail/2">
											<span>秒杀</span>
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a class="border-b J_more-list">
									<span>促销工具</span>
									<i class="nav-item-icon"></i>
								</a>
								<ul class="nav-sub-list none">
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/1">
											<span>享立减</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/2">
											<span>集赞</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/3">
											<span>多人拼团</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/4">
											<span>幸运大转盘</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/5">
											<span>微社区</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/6">
											<span>砸金蛋</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/salesTools/detail/7">
											<span>签到</span>
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a class="border-b J_more-list">
									<span>会员卡券</span>
									<i class="nav-item-icon"></i>
								</a>
								<ul class="nav-sub-list none">
									<li class="nav-item">
										<a href="/home/index/memberTicket/detail/1">
											<span>会员卡</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/memberTicket/detail/2">
											<span>积分</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/memberTicket/detail/3">
											<span>充值</span>
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a class="border-b J_more-list">
									<span>推广工具</span>
									<i class="nav-item-icon"></i>
								</a>
								<ul class="nav-sub-list none">
									<li class="nav-item">
										<a href="/home/index/extension/detail/1">
											<span>消息提醒</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/extension/detail/2">
											<span>消息模板</span>
										</a>
									</li>
									<li class="nav-item">
										<a href="/home/index/extension/detail/3">
											<span>投票</span>
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="J_more-list">
							<span>案例展示</span>
							<i class="nav-item-icon"></i>
						</a>
						<ul class="nav-sub-list none">
							<li class="nav-item">
								<a href="#">APP定制</a>
							</li>
							<li class="nav-item">
								<a href="#">微信小程序</a>
							</li>
							<li class="nav-item">
								<a href="#">微商城系统</a>
							</li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="J_more-list">
							<span>行业解决方案</span>
							<i class="nav-item-icon"></i>
						</a>
						<ul class="nav-sub-list none">
							<li class="nav-item">
								<a href="#">精品APP</a>
							</li>
							<li class="nav-item">
								<a href="#">明星小程序</a>
							</li>
							<li class="nav-item">
								<a href="#">优质微商城</a>
							</li>
						</ul>
					</li>
					<li class="nav-item">
						<a href="/home/index/news">
							<span>会搜云资讯</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="/home/index/helps">
							<span>帮助中心</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="/home/index/about" class="border-b">
							<span>关于我们</span>
						</a>
					</li>
				</ul>
			</div>
		</nav>
    </div>
    <div class="list-content">
	    <div class="help-list">
	    	@if($information['data'])
	        <ul>
	        	@foreach($information['data'] as $key=>$val)
	            <li>
	                <a href="/home/index/helpDetail/{{ $val['id'] }}">{{ request('key') }}.{{ $key+1 }} {{ $val['title'] }}</a>
	            </li>
	            @endforeach
	        </ul>
	        @else
	        	<p>暂无相关内容</p>
	        @endif
	    </div>
	    <nav class="list_page page-bottom">
			{{$page}}
		</nav>
    </div>
    
	
</body>
	<script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
	<script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js"></script>
	<script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>  
	<script src="{{ config('app.source_url') }}mobile/js/common.js?v=1.0.01"></script> 
	@if(config('app.env') == 'prod')
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
	@endif
	@if(config('app.env') == 'dev')
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
	@endif
	<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript" src="//s.union.360.cn/166985.js" async defer></script>
	<script type="text/javascript">
		var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan style='display:none'; id='cnzz_stat_icon_1262563804'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/z_stat.php%3Fid%3D1262563804%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));
		// 微信分享
		$(function(){		
			var url = location.href.split('#').toString();
	        $.get("/shop/weixin/getWeixinSecretKey",{"url": url},function(data){
	            if(data.errCode == 0){
	                wx.config({
	                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	                    appId: data.data.appId, // 必填，公众号的唯一标识
	                    timestamp: data.data.timestamp, // 必填，生成签名的时间戳
	                    nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
	                    signature: data.data.signature,// 必填，签名，见附录1
	                    jsApiList: [
	                        'checkJsApi',
	                        'onMenuShareTimeline',
	                        'onMenuShareAppMessage',
	                        'onMenuShareQQ',
	                        'chooseWXPay'
	                    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	                });
	                
	            }
	        })		
		    wx.ready(function () {  

		    	//分享到朋友圈
		        wx.onMenuShareTimeline({  
		            title: '移动电商，会搜云享-{{ $title or '' }}', // 分享标题  
		            link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线  
		            imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标  
		            success: function () {   
		                // 用户确认分享后执行的回调函数  
		                //alert('分享成功');  
		            },  
		            cancel: function () {   
		                // 用户取消分享后执行的回调函数  
		            }  
		        });  

		        //分享给朋友 
		        wx.onMenuShareAppMessage({  
				    title: '{{ $title or '' }}', // 分享标题  
				    desc: '移动电商，会搜云享', // 分享描述  
				    link: url, // 分享链接  
				    imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标  
				    type: '', // 分享类型,music、video或link，不填默认为link  
				    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空  
				    success: function () {   
				        // 用户确认分享后执行的回调函数  
				    },  
				    cancel: function () {   
				        // 用户取消分享后执行的回调函数  
				    }  
				});

		        //分享到QQ
				wx.onMenuShareQQ({
				    title: '{{ $title or '' }}', // 分享标题
				    desc: '移动电商，会搜云享', // 分享描述
				    link: url, // 分享链接
				    imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标
				    success: function () { 
				       // 用户确认分享后执行的回调函数
				    },
				    cancel: function () { 
				       // 用户取消分享后执行的回调函数
				    }
				});

				//分享到腾讯微博
				wx.onMenuShareWeibo({
				    title: '{{ $title or '' }}', // 分享标题
				    desc: '移动电商，会搜云享', // 分享描述
				    link: url, // 分享链接
				    imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标
				    success: function () { 
				       // 用户确认分享后执行的回调函数
				    },
				    cancel: function () { 
				        // 用户取消分享后执行的回调函数
				    }
				});
		        wx.error(function(res){  
		            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。  
		            //alert("errorMSG:"+res);  
		        });  
		    });	
		    				
			//懒加载
			$(function() {
			    $("img.lazy").lazyload({
			    	threshold : 200,
			    	effect : "fadeIn"
				});
			});
			
			//	    百度统计代码
			var _hmt = _hmt || [];    
			(function() {    
				var hm = document.createElement("script");    
				hm.src = "https://hm.baidu.com/hm.js?1428d683bd5c972642b671971b847d6a";    
				var s = document.
				getElementsByTagName("script")[0];    
				s.parentNode.insertBefore(hm, s);    
			})();
		})
    </script>
    <script src="{{ config('app.source_url') }}mobile/js/helpList.js"></script> 
</html>
