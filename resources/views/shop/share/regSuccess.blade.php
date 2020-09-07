<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<title>注册成功</title>
	<style>
		html,body,.content{
			margin:0;padding:0;
			height:100%;width: 100%;
		}
		.content{
			background: #fb0055;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}
		.text{
			color:white;
			width: 100%;
			text-align: center;
		}
		.success{
			width: 100%;
			text-align: center;
		}
		.success img{
			width:100px;
			height:100px
		}
	</style>
</head>
<body>
	<div class="content">
		<div class="success">
			<img src="{{config('app.url')}}static/images/share_complete.png"/>
		</div>
		<div class="text">
			<p>恭喜您注册成功</p>
			<p>立即去领取免费小程序</p>
		</div>
		
	</div>
</body>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="https://abc.huisou.cn/shop/static/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
		var thisurl = "{{$appurl}}";
			(
				function(){
					var vTap = [];
					var telT;
					
					$(window).on('load',function(){
						console.log($(this).height(),$(this).width()*2.03+'px')
						$('article').css({'height':$(this).width()*2.03+'px'})
						$('.shade').css({'width':$(this).width()+'px','height':$('article').height()+180+'px'})
					})
					//手机验证
					{{--$('#tel').on('blur',function(){--}}
						{{--if($(this).val()!='' && $(this).val().match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/g)){--}}
							{{--$.ajax({--}}
								{{--url:thisurl+'auth/share/share/isRegister',--}}
								{{--type:'get',--}}
								{{--data:{--}}
									{{--tel:$('#tel').val()--}}
								{{--},--}}
								{{--success:function(res){--}}
									{{--console.log(res);--}}
									{{--if(res.info == "该账号可以注册"){--}}
										{{--vTap.push(true)--}}
									{{--}else{--}}
										{{--$('.info').text('该账号已注册').show();--}}
									{{--}--}}
									{{--telT=setTimeout(function(){--}}
										{{--$('.info').hide();--}}
										{{--clearTimeout(telT);--}}
									{{--},1000)--}}
								{{--}--}}
							{{--});--}}
						{{--}else if($(this).val()==''){--}}
						{{--}else{--}}
							{{--$('.info').text('手机号码输入错误').show();--}}
						{{--}--}}
						{{--telT=setTimeout(function(){--}}
							{{--$('.info').hide();--}}
							{{--clearTimeout(telT);--}}
						{{--},1000)--}}
					{{--});--}}
					
					//密码验证
					$('#pw').on('blur',function(){
						let pwValue = $(this).val();
						if(pwValue.match(/^([a-zA-Z0-9]){6,12}$/g)){
							vTap.push(true)
						}else if($(this).val()==''){
						}else{
							$('.info').text('请输入6-12位密码').show();
						}
						telT=setTimeout(function(){
							$('.info').hide();
							clearTimeout(telT);
						},1000)
					});

					//获取验证码
					var flag = false;
					$('#getvalidate').on('click',function(){
						var sendFlag = false;
						if($('#tel').val() && $('#tel').val().match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9]|19[0|1|2|3|5|6|7|8|9]|16[0|1|2|3|5|6|7|8|9])\d{8}$/g)){
							if(flag){
								$('.info').text('短信已发送').show();
								telT=setTimeout(function(){
									$('.info').hide();
									clearTimeout(telT);
								},1000)
								return false
							}
                            $.ajax({
                                url:thisurl+'auth/share/isRegister',
                                type:'get',
                                async:false,
                                data:{
                                    tel:$('#tel').val()
                                },
                                success:function(res){
                                    console.log(res);
                                    if(res.status == '1'){
                                        sendFlag = true;
                                    }else{
                                        $('.info').text('该账号已注册').show();
                                    }
                                    telT=setTimeout(function(){
                                        $('.info').hide();
                                        clearTimeout(telT);
                                    },1000)
                                }
                            });
							if (!sendFlag){
							    return false;
							}
							$.ajax({
								url:thisurl+'/auth/sendcode',
								type:'get',
								data:{
									mphone:$('#tel').val()
								},
								success:function(res){
									console.log(res);
									if(res.status == 1){
										//status
										vTap.push(true)
									}
								}
							})
							$(this).val('60s后重试');
							countDown($(this));
						}else{
							$('.info').text('手机号码格式不正确').show();
						}
						telT=setTimeout(function(){
							$('.info').hide();
							clearTimeout(telT);
						},1000)
					})
					
					//协议
					$('label.grey').on('click',function(){
						$('.pop').show()
					})
					$('.cancel').on('click',function(){
						$('.pop').hide()
					})
					
					//立即注册
					$('#submit').on('click',function(){

                       if(!$('#tel').val().match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9]|19[0|1|2|3|5|6|7|8|9]|16[0|1|2|3|5|6|7|8|9])\d{8}$/g)){
                           $('.info').text('手机号码格式不正确').show();
					   }
					   if (!$("#agreement").is(':checked')){
                           $('.info').text('您还没有同意协议').show();
                           return false;
					   }
						if($('#tel').val() && $('#validate').val() && $('#nickname').val() && $('#pw').val()){
                            $.ajax({
                                url:thisurl+'auth/share/register',
                                data:{
                                    'mphone':$('#tel').val(),
                                    'sms_code':$('#validate').val(),
                                    'nickname':$('#nickname').val(),
                                    'password':$('#pw').val(),
								},
                                type:'post',
                                cache:false,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType:'json',
                                success:function (res) {
                                    if (res.status == 1){
                                        window.location.href=thisurl+'auth/share/regSuccess'
                                    }else {
                                        $('.info').text(res.info).show();
                                    }
                                },
                                error : function() {
                                    $('.info').text('服务器打盹了……').show();
                                }
                            })
						}else{
							$('.info').text('请完善注册信息信息').show();
						}	
						telT=setTimeout(function(){
							$('.info').hide();
							clearTimeout(telT);
						},1000)	
					})

					//倒计时
					var t;
					
					function countDown(that) {
				        var time = 60;
				        var _this = that;
				        if(!flag){
				        	flag=true;
				        	t = setInterval(function () {
					            --time;
					            var html = time + "s后重试";
					            $("#getvalidate").val(html);
					            if (time == 0) {
					                clearInterval(t);
					                $("#getvalidate").val("获取验证码")
					                flag=false;
					            }
					        }, 1000)
				        }
				        
				    }
				}
			)()
// 
var url = location.href.split('#').toString();
var shareurl = "https://abc.huisou.cn/auth/share/register";
            $.get(thisurl+'auth/share/getShareData',{"url": url},function(data){
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
                    title: "快来领取超值福利，使用小程序商城", // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: shareurl, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
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
                    title: '快来领取超值福利，使用小程序商城', // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: shareurl, // 分享链接
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
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
                    title: '快来领取超值福利，使用小程序商城', // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: shareurl, // 分享链接
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
                    success: function () {
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                       // 用户取消分享后执行的回调函数
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: '快来领取超值福利，使用小程序商城', // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: shareurl, // 分享链接
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
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
        
		</script>
</html>
