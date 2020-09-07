@extends('shop.common.template_free')
@section('head_css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/applySuccess.css">
@endsection

@section('main')
<div id="main" v-cloak>
    <div class="container">
		<div class="applySuccess">恭喜您领取成功</div>
		<div class="notice">请您等候通知，我们的通知方式有：关注公众号接受消息通知、短信通知、电话通知！</div>
		<div class="info">获取更多帮助，请关注官方公众号</div>
		<div class="code_img">
			<img src="{{ config('app.source_url') }}shop/images/hs_code.jpg" alt="">
			<p>长按识别二维码可以关注</p>
		</div>
	</div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
    var host ="{{ config('app.url') }}";
    var imgUrl = "{{ imgUrl() }}";
</script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/applySuccess.js" ></script>
<script>
   	$(function(){
	    var url = location.href.split('#').toString();
	    var urladd = "";
	    $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
	        if(data.errCode == 0){
	            wx.config({
	                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	                appId: data.data.appId, // 必填，公众号的唯一标识
	                timestamp: data.data.timestamp, // 必填，生成签名的时间戳
	                nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
	                signature: data.data.signature,// 必填，签名，见附录1
	                jsApiList: [
	                	'hideMenuItems',
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
	            title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
	            desc: '会搜股份新年钜献', // 分享描述
	            link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
	            imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
	            success: function () {
	                // 用户确认分享后执行的回调函数
	            },
	            cancel: function () {
	                // 用户取消分享后执行的回调函数
	            }
	        });

	        //分享给朋友
	        wx.onMenuShareAppMessage({
	            title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
	            desc: '会搜股份新年钜献', // 分享描述
	            link: url, // 分享链接
	            imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
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
	            title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
	            desc: '会搜股份新年钜献', // 分享描述
	            link: url, // 分享链接
	            imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
	            success: function () {
	               // 用户确认分享后执行的回调函数
	            },
	            cancel: function () {
	               // 用户取消分享后执行的回调函数
	            }
	        });

	        //分享到腾讯微博
	        wx.onMenuShareWeibo({
	            title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
	            desc: '会搜股份新年钜献', // 分享描述
	            link: url, // 分享链接
	            imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
	            success: function () {
	               // 用户确认分享后执行的回调函数
	            },
	            cancel: function () {
	                // 用户取消分享后执行的回调函数
	            }
	        });
	        wx.error(function(res){
	            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
	        });
	    });
    })
</script>
@endsection