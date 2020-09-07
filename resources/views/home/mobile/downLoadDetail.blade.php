<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"> 
    <meta http-equiv="X-UA-Compatible" content="chrome=1"><meta name="360-site-verification" content="43c040d547ea865dccc559d9ebe3fb9e" />
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="renderer" content="webkit">
    <meta name="baidu-site-verification" content="HIIZCPonde" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ config('app.source_url') }}home/image/icon_logo.png" type="image/png" />
	<title>{{ $title or '' }}</title>
	<script src="{{ config('app.source_url') }}mobile/js/rem.js"></script>
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/reset.css">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/downLoadDetail.css">
</head>
<body> 
	<div class="content">
        <p class="title1">
            如果在微信中无法下载
            <br>
            请点击手机右上角选择浏览器中打开
            <i class="tips-icon"></i>
        </p>
        <p class="title2">会搜云商家版</p>
        <p class="title3">轻松便捷的移动管理店铺</p>
        <div class="download-box">
            <a href="https://itunes.apple.com/cn/app/%E6%B1%87%E6%90%9C%E4%BA%91%E5%95%86%E5%AE%B6%E7%89%88/id1376377732?mt=8" class="down-btn btn1">
                <i class="ios-icon"></i>
                <span>iPhone版下载</span>
            </a>
        </div>
        <div class="download-box box2">
            <a href="https://a.app.qq.com/o/simple.jsp?pkgname=com.huisou.hsyun" class="down-btn btn1">
                <i class="android-icon"></i>
                <span>Android版下载</span>
            </a>
        </div>
    </div>
	
</body>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js"></script>
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
</html>
