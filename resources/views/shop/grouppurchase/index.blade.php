@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/group_0uyrk812.css">
@endsection

@section('main')
	<div class="container">
		<div class="tuan-list content">
			<p class="title">拼团商品</p>
			<div class="js-waterfall">
	            <div class="js-list b-list" id="product_list">
					@forelse($rule[0]['data'] as $val)
                	<a class="block name-card-vertical" href="/shop/grouppurchase/detail/{{$val['id']}}/{{session('wid')}}">
                		<div class="img-outner">
							<img class="thumb" src="{{imgUrl()}}{{$val['product']['img']}}">
						</div>
						<div class="detail">
							<h3 class="goods-name">{{$val['title']}}</h3>
							<div class="groupon-info">
								<span class="join-num">{{$val['groups_num']}}人团</span>
								<span>¥</span><span class="price">{{$val['min']}}@if($val['max'] && $val['min'] != $val['max'])～{{$val['max']}}@endif</span>
								<span>／件 &nbsp;原价<s>{{$val['product']['price']}}</s></span>
								<span class="join-text pull-right">去开团</span>
							</div>
						</div>
					</a>
						@endforeach
	            </div>
	        </div>
		</div>
	</div> 
@endsection
@section('page_js')
	<script type="text/javascript">
		var _host ="/";
		var imgUrl ="{{ imgUrl() }}";

	</script>
    <!-- 当前页面js -->
	<script src="{{ config('app.source_url') }}shop/js/group_0uyrk812.js"></script>
	<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
    	//微信分享
  		$(function(){	  	
  			var $jifen_tc = $('.jifen_tc');	
  			function jifentcShow(data){
	    		$jifen_tc.find('p').find('span').html(data);
				$jifen_tc.show();
	    	}	
	    	function jifenAjax(){
	    		$.ajax({
					type:"get",
					data:{},
					url:"/shop/point/addShareRecord/"+wid,
					dataType:"json",
					headers:{
						'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
					},
					success:function(data){
						if(data.errCode == 3 || data.errCode == 1 || data.errCode == 2){
							return false;
						}else{
							jifentcShow(data.data);
							setTimeout(function(){
								$jifen_tc.hide();
							},3000)
						}
					},
					error:function(data){
						tool.tip(data.errMsg);
					}
				});
	    	}
            var url = location.href.split('#').toString();
            $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
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
			@if($reqFrom == 'wechat')
            wx.ready(function () {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: '{{ $shareData["share_title"] or "" }}', // 分享标题
                    desc: '{{ $shareData["share_desc"] or "" }}', // 分享内容介绍
                    link: "{{ config('app.url') }}shop/grouppurchase/index/{{session('wid')}}?_pid_={{session('mid')}}",
                     // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: '{{ $shareData["share_img"] or "" }}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        jifenAjax();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: '{{ $shareData["share_title"] or "" }}', // 分享标题
                    desc: '{{ $shareData["share_desc"] or "" }}', // 分享内容介绍
                    link: "{{ config('app.url') }}shop/grouppurchase/index/{{session('wid')}}?_pid_={{session('mid')}}",
                     // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: '{{ $shareData["share_img"] or "" }}', // 分享图标分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        jifenAjax();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享到QQ
                wx.onMenuShareQQ({
                    title: '{{ $shareData["share_title"] or "" }}', // 分享标题
                    desc: '{{ $shareData["share_desc"] or "" }}', // 分享内容介绍
                    link: "{{ config('app.url') }}shop/grouppurchase/index/{{session('wid')}}?_pid_={{session('mid')}}",
                     // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: '{{ $shareData["share_img"] or "" }}', // 分享图标分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        jifenAjax();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                wx.error(function(res){
                    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                });
            }); 
            @endif
		})
    </script>
@endsection