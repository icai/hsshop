<!DOCTYPE html>
<html class="admin responsive-320">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name=”renderer” content="webkit">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $title or '' }}</title>
	<link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
	<!-- 核心base.css文件（每个页面引入） -->
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/getSettlementInfo.css"  media="screen">
	<style type="text/css">
		body{
          -webkit-overflow-scrolling : touch;
        }
		.hint{
			top:7%
		}
		.hint1{
			top:15%
		}
		.header_img img {
			width: 100%;
    		display: block;
		}
		.header_info {
		    padding: 10px;
		    color: #666666;
		    font-size: 16px;
		    line-height: 20px;
		}
		.action {
			line-height: 19px;
		    background: #fff;
		    padding: 20px;
		    font-size: 15px;
		    padding-right: 105px;
		    position: relative;
		    margin:10px 0;
		    padding-left: 10px;
		    font-weight: bold;
		}
		.action a{
			background: #B0282C;
		    padding: 11px 12px;
		    color: #fff;
		    position: absolute;
		    right: 10px;
		    top: 50%;
		    text-align: center;
		    line-height: 18px;
		    font-size: 17px;
		    border-radius: 20px;
		    margin-top: -18px;
		    font-weight: bold;
		}
		.qrcode {
			background:#fff;
		}
		.qrcode .qrcode_title{
			color: #3B3B3B;
		    font-size: 17px;
		    padding: 10px;
		    text-align: center;
		    line-height: 28px;
		    font-weight: 600;
		}
		.qrcode .qrcode_img {
			text-align:center;
		}
		.qrcode .qrcode_img img{
			width:80%;
		}
		.qrcode .qrcode_bottom{
			color:#666666;
			font-size:16px;
			text-align:center;
			padding-bottom:40px;
		}
		.wait_pay_product .product_desc div {
		    color: #333;
		    font-size: 17px;
		    margin: 5px 0 15px 0;
		    font-weight: bold;
		}
		.product_price {
			margin-top: -5px !important;
			color:#B0282C !important;
			font-size: 19px !important;
		}
		.wait_pay_product .product_desc div:nth-child(1) {
		    -webkit-line-clamp: 3 !important;
		}
		/*拼团人开始  */

		.gp-people-wrap {
		  padding: 15px 0 15px 0;
		  background-color: #fff;
		  margin-bottom:16px
		}

		.gp-people-head {
		  padding: 0 12px;
		  display: flex;
		  display: -webkit-flex;
		  display: -ms-flex;
		  justify-content: center;
		  -webkit-justify-content: center;
		  -ms-justify-content: center;
		  text-align: center;
		}
		.gp-people-head-item {
		  position: relative;
		  margin-right: 10px;
		  border-radius: 50%;
		  width: 60px;
		  height: 60px;
		  margin-left:-18px;
		  box-sizing: border-box;
		}
		.gp-people-head-item:first-child {
		    margin-left: 0px;
		}
		.gp-people-head-item.nobody {
		  display: flex;
		  display: -webkit-flex;
		  display: -ms-flex;
		  -webkit-align-items: center;
		  -ms-align-items: center;
		  align-items: center;
		  justify-content: center;
		  -webkit-justify-content: center;
		  -ms-justify-content: center;
		  border: 1px dashed #999;
		  font-size: 25px;
		  color: #999; 
		}

		.gp-people-head-item .colonel {
		  position: absolute;
		  top: -5px;
		  left: -5px;
		  background-color: #b1292d;
		  display: inline-block;
		  padding: 2.5px 5px;
		  color: #fff;
		  border-radius: 5px;
		  text-align: center;
		}

		.gp-people-head-icon {
		  width: 60px;
		  height: 60px;
		  border-radius: 50%;
		  min-width:50px;
		  border: 1px solid #b1292d;
		}
		.small-height{
		  height:40px;
		  width:40px;
		  min-width:30px;
		}
		.colonel_small{
		    font-size: 14px;
		    padding: 3px !important;
		}
		.total-group{
		    min-width: 68px;
		    font-size: 14px;
		    display: flex;
		    display: -webkit-box;
		    display: -webkit-flex;
		    display: -moz-box;
		    display: -ms-flexbox;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    -webkit-align-items:center;
		    -moz-align-items:center;
		    -o-align-items:center;
		    -webkit-justify-content:center;
		    -moz-justify-content:center;
		    -o-justify-content:center;
		    position:relative;
		    top:1px;
		}
		.gp-people-tip {
		  margin-top: 15px;
		  text-align: center;
		}

		.gp-people-btn {
		    padding: 0px 12px;
		    border-bottom: 1px solid #e5e5e5;
		    display: block;
		    background: #fff;
		    margin-bottom: 10px;
		}

		.gp-btn,.share-btn {
		  border: none;
		  font-weight:bolder;
		  border-radius: 25px;
		  color: #fff;
		  background-color: #b1292d;
		  font-size: 20px;
		  margin: 10px 0 15px 0;
		  height: 50px;
		  line-height: 45px;
		  width: 100%;
		  outline: none;
		}
		@media screen and (max-width: 359px){ 
		  .gp-people-head-item {
		    position: relative;
		    margin-right: 7px;
		    border-radius: 50%;
		    width: 60px;
		    height: 60px;
		    min-width:40px;
		    box-sizing: border-box;
		  }
		  .gp-people-head-icon{height:60px;width:60px;min-width:40px;}
		  .small-height{
		    height:40px;
		    width:40px;
		    min-width:21px;
		  }
		  .total-group{
		    min-width: 68px;
		    font-size: 9px;
		    display: flex;
		    display: -webkit-box;
		    display: -webkit-flex;
		    display: -moz-box;
		    display: -ms-flexbox;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    -webkit-align-items:center;
		    -moz-align-items:center;
		    -o-align-items:center;
		    -webkit-justify-content:center;
		    -moz-justify-content:center;
		    -o-justify-content:center;
		  }
		  .colonel_small{
		    font-size: 9px;
		    padding: 1px !important;
		  }
		} 
		@media screen and (min-width: 359px) and (max-width: 375px){ 
		  .gp-people-head-item {
		    position: relative;
		    margin-right: 7px;
		    border-radius: 50%;
		    width: 60px;
		    height: 60px;
		    min-width:40px;
		    box-sizing: border-box;
		  }
		  .gp-people-head-icon{height:60px;width:60px;min-width:40px;}
		  .small-height{
		    height:40px;
		    width:40px;
		    min-width:25px;
		  }
		  .total-group{
		    min-width: 68px;
		    font-size: 12px;
		    display: flex;
		    display: -webkit-box;
		    display: -webkit-flex;
		    display: -moz-box;
		    display: -ms-flexbox;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    -webkit-align-items:center;
		    -moz-align-items:center;
		    -o-align-items:center;
		    -webkit-justify-content:center;
		    -moz-justify-content:center;
		    -o-justify-content:center;
		  }
		  .colonel_small{
		    font-size: 10px;
		    padding: 1px !important;
		  }
		} 
		/*拼团人结束  */
		.footer {
            margin-top:20px;
		}
		
		.swiper-container {
			position:absolute;
			top:10px;
			color:white;
			width:90%
		}
		.swiper-container img {
			width:20px;
			border-radius:50%
		}
		.swiper-container .d_show>div {
			background:rgba(0,0,0,.7);
			border-radius:20px;
			padding:10px;
			font:12px/20px '';
			height:20px;
			margin-bottom:10px;
		}
		/* 弹窗 */
	    .pop_up{
	        position:fixed;
	        top:0;
	        left:0;
	        height:100%;
	        width:100%;
	        z-index: 10000000;
	    }
	    .pop_up .shade{
	        background-color:rgba(0,0,0,.8);
	        height:100%;
	        width:100%
	    }
	    
	    .pop_up .pop_content{
	        position:absolute;
	        top:0;bottom:0;left:0;right:0;
	        margin:auto;
	        z-index:100;
	        @if(session('wid') == '626' || session('wid') == '661')
	        height:480px;
	        @elseif(session('wid') == '634')
	        height:440px;
	        @endif
	        width:85%;
	        border-radius:5px;
	        background:white;
	        padding:20px 10px
	    }
	    .pop_up .attend{
	        font-weight:bold;
	        padding-top:20px;
	    }
	    
	    .pop_up .title{
	        text-align:center;
	        font:20px/30px "微软雅黑";
	        border-bottom:1px solid #e6e6e6;
	        padding-bottom:10px;
	        font-weight:bold
	    }
	    .pop_up .pop_text{
	        font:16px/25px "微软雅黑";
	        padding-top:10px;
	        font-weight:bold;
	        text-align:center
	    }
	    .pop_up .qrcode>div{
	        width:180px;
	        height:160px;
	        margin:5px auto;
	        text-align:center;
	        display:flex;
	        flex-direction:column;
	    }
	    .pop_up .qrcode>div img{
	        width:180px;
	        border:none;
	        outline:none
	    }
	    .pop_up .qrcode_tip{
	        color:#333;
	        font-size:17px;
	        display:block;
	        font-weight:bold;
	        padding:10px 0;
	        border-bottom:1px solid #e6e6e6
	    }
	    .pop_up .code_tip{
	        font:18px/40px "微软雅黑";
	        font-weight:normal;
	    }
	    .pop_up .code_step{
	        font:16px/22px "微软雅黑";
	        text-align:left;
	        width:230px;
	        position:absolute;
	        left:0;
	        right:0;
	        margin:0 auto
	    }
	    .pop_up .btn_wrap{
	        position:absolute;
	        bottom:20px;
	        left:0;right:0;
	        text-align:center
	    }
	    .pop_up .btn_wrap>.btn{
	        width:60%;
	        font:18px/40px "微软雅黑";
	        color:white;
	        font-weight:bold;
	        background:#B0282C;
	        border-radius:6px;
	    }
	    .pop_up .close_btn{
	        position:absolute;
	        right:10px;
	        top:10px;
	    }
	    .pop_up .close_btn img{width:22px}
		.pop_up .tip_bg{
	        padding: 0;
	        border-radius: 14px;
	        height:300px;
	    }
	    .pop_up .tip_bg .tip_action_title{
	        text-align: center;
	        padding-top: 20px;
	    }
	    .pop_up .tip_bg .tip_action_title a {
	        display: inline-block;
	        padding: 15px 50px;
	        background: #B1292D;
	        border-radius: 22px;
	        color: #fff;
	        margin: 0 auto;
	    }
	    .pop_up .tip_bg .xx_close{
	        width: 31px;
	        position: absolute;
	        top: 20px;
	        right: 30px;
	    }
	    .pop_up .tip_row{
	        position: absolute;
	        top: 0;
	        bottom: 0;
	        left: 0;
	        right: 0;
	        margin: auto;
	        z-index: 100;
	        height:50%;
	        border-radius: 5px;
	        padding: 20px;
	    }
	    .pop_up .tip_row .tip_bg1{
	        width: 88%;
	        position: absolute;
	        top: 0;
	        bottom: 0;
	        left: 0;
	        right: 0;
	        margin: auto;
	    }
	    .pop_up .tip_row_a{
	        position: absolute;
	        width: 43%;
	        height: 70px;
	        left: 28%;
	        bottom: 21%;
	    }
	    .pop_up .tip_bg .tip_title{
	        text-align: center;
	        font-size: 16px;
	        color: #333;
	        padding: 10px;
	    }
	    .pop_up .tip_bg .tip_title span{
	        color:#F33A40;
	    }
		.zhezhao .share_model img {
			width: 100% !important;
			position: relative;
			border-radius:0
		}
		.zhezhao {
			background: rgba(0,0,0,0.7) !important;
			position: fixed;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			z-index: 10000000;
		}
		.zhezhao .close_share {
			width: 40px;
			height: 40px;
			position: relative;
			top: 18.5%;
			left: 5%;
			margin: 0;
			position: absolute;
		}
		.small-height {
		    height: 40px;
		    width: 40px;
		    min-width: 30px;
		}
		.colonel_small {
		    font-size: 14px;
		    padding: 3px!important;
		}
	</style>
</head>
<body>
	<div class="waitPayOrder" id="app" v-cloak>
		<!-- 顶部图片 -->
		<div class="header_img">
			@if(session('wid') == 661 && session('wid') != 626)
			<!-- 微商城 -->
			<img src="{{ config('app.source_url') }}shop/images/banner444.jpg?tttt">
			@elseif(session('wid') == 626)
			<img src="{{ config('app.source_url') }}shop/images/banner621.jpg?t=123">
			@else
			<!-- 小程序 -->
			<img src="{{ config('app.source_url') }}shop/images/banner555.jpg?tttt">
			@endif
		</div>
		<a href="javascript:void(0);" class="gp-people-btn" style="border: none;"  v-if="data1.goupos.status == 1" @click="setShowShare">
			<button class="share-btn" >助力好友凑团</button>
		</a>
		<a href="{{config('app.url')}}shop/meeting/detail/{{$data['goupos']['rule_id']}}/{{$data['wid']}}" class="gp-people-btn" style="border: none;" v-if="data1.goupos.status != 1">
	        <button class="gp-btn">我也要免费领</button>
	    </a>
		<div class="gp-people-wrap">
		    <div class="gp-people-head">
		        <div class="gp-people-head-item small-height" v-for="(list,index) in data1.member" v-if="index <= 9">
		            <span class="colonel colonel_small" v-if="index == 0">团长</span>
		            <img :src="list.headimgurl" class="gp-people-head-icon small-height">
		        </div>
		    </div>
		    <div class="gp-people-tip " v-if="data1.goupos.status == 1">还剩@{{data1.goupos.lackNum}}人</div>
		    <div class="gp-people-tip " v-if="data1.goupos.status == 2 && data1.member.length <= 3">团已满</div>
		    <div class="gp-people-tip " v-if="data1.goupos.status == 2 && data1.member.length > 3">团已满(最多只显示10人)</div>
		</div>
		<!-- 底部说明 -->
		<div class="qrcode">	
			<div class="qrcode_title">想要了解@if(session('wid') != 661)小程序@else微商城@endif更多使用方法以及相关的教程可以关注我们微信</div>
			<div class="qrcode_img">
				<img src="{{ config('app.source_url') }}shop/images/hsqrcode626.jpg">
			</div>
			<div class="qrcode_bottom">长按识别扫一扫关注我们微信</div>
		</div>
		<div class="swiper-container">
			<div id="conts"> 
				<div class="dm">
					<div class="d_screen">
						<div class="d_mask"></div>
						<div class="d_show">
					</div>
				</div>
			</div> 
		</div>
		<!--放弃支付弹框-->
		<div class="giveUp_price" v-if="isShowGiveUp">
			<div class="mask" @click="GiveUpPay"></div>
			<div class="payComment">
				<div>确定要放弃付款吗？</div>
				<div>
					你尚未完成支付 ,<br/>喜欢的商品可能会被抢购哦！
				</div>
				<div class="flex_around">
					<span @click="GiveUpPay">暂时放弃</span>
					<span @click="continuePay">继续支付</span>
				</div>
			</div>
		</div>
		<!--页面提示-->
		<div class="prompting" v-text="hint" v-if="hint_show"></div>
		<!-- 弹窗 -->
        <div class="pop_up" v-if="show_tip">
            <div class="shade" style="background-color: rgba(0,0,0,0.6);">
            </div>
            <div class="tip_bg">
                <div class="tip_row">
                    <img class="tip_bg1" src="{{ config('app.source_url') }}shop/images/tip_bg1.png">
                    <img @click="hideModel" class="xx_close" src="{{ config('app.source_url') }}shop/images/xx2.png">
                    <a class="tip_row_a" href="/shop/meeting/groups/showMyGroups/{{session('wid')}}"></a>   
                </div>
            </div>
        </div>
		<!-- 分享弹窗 -->
		<div class="zhezhao" v-on:click="setShowShare" v-if="isShowShare">
            <div class="share_model">
            @if(session('wid')== '661')
            <img src="{{ config('app.source_url') }}shop/images/pintuanshare077.png" />
            @elseif(session('wid') == '626')
            <img src="{{ config('app.source_url') }}shop/images/pintuanshare626.png?t=123" />
            @else
            <img src="{{ config('app.source_url') }}shop/images/pintuanshare066.png" />
            @endif
            </div>
            <div class="close_share"></div>
        </div>
		@include('shop.common.meetingBottom')
	</div>
	@include('shop.common.footerMeeting')
	<script type="text/javascript">
		var data1 = {!! json_encode($data)  !!};
		var pid = "{{$data['id']}}";
		var remak_no="{{$remak_no}}";
	</script>
	<!-- 主体内容 结束 -->
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
		var _host = "{{ config('app.source_url') }}";
		var imgUrl = "{{ imgUrl() }}";
		var host ="{{ config('app.url') }}";
		var _token = $('meta[name="csrf-token"]').attr("content");
	</script>
	<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<script type="text/javascript">
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
	            wxShare();
	        })
	        
	        function wxShare(){
                @if(session('wid') == 661)
					var share_title ='39800微商城我已免费领到，你也赶紧领一个';
					var share_desc ='一起来拼团，拼团满50人，全部免单！';
                @elseif(session('wid') == 626)
                	var share_title ='移动互联网实战总裁班课程我已免费领到，你也赶紧领！';
                    var share_desc =' 超值拼团限时回馈，5人成团，人人0元领取《移动互联网实战总裁班》1天！';
                @else
					var share_title =' 19800小程序我已免费领到，你也赶紧领一个';
					var share_desc ='一起来拼团，拼团满50人全部免单！';
                @endif
                var share_img = _host + 'shop/images/share_groups_meeting.jpg';
                var share_url= host +'shop/meeting/groupon/'+ data1.goupos.id +'/{{session('wid')}}?_pid_={{session('mid')}}&group_type=2'
				wx.ready(function () {
                    //分享到朋友圈
                    wx.onMenuShareTimeline({
                        title: share_title, // 分享标题
                        desc: share_desc, // 分享描述
                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                        imgUrl: share_img, // 分享图标
                        success: function () {
                            // 用户确认分享后执行的回调函数

                        },
                        cancel: function () {
                            // 用户取消分享后执行的回调函数
                        }
                    });

                    //分享给朋友
                    wx.onMenuShareAppMessage({
                        title: share_title, // 分享标题
                        desc: share_desc, // 分享描述
                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                        imgUrl: share_img, // 分享图标
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
                        title: share_title, // 分享标题
                        desc: share_desc, // 分享描述
                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                        imgUrl: share_img, // 分享图标
                        success: function () {
                           // 用户确认分享后执行的回调函数
                        },
                        cancel: function () {
                           // 用户取消分享后执行的回调函数
                        }
                    });

                    //分享到腾讯微博
                    wx.onMenuShareWeibo({
                        title: share_title, // 分享标题
                        desc: share_desc, // 分享描述
                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                        imgUrl: share_img, // 分享图标
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
	        }
	</script>
	<script src="{{ config('app.source_url') }}shop/js/meetingGetSettlement.js" ></script>
</body>
</html>