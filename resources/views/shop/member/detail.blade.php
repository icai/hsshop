@extends('shop.common.template')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/qrCodeModal.css"/>
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/card_detail.css">
@endsection
@section('main')
	<div class="container " style="min-height: 620px;">
		<input type="hidden" id="wid" value="{{session('wid')}}">
		<input type="hidden" id="card-id" value="{{$card['id']}}">
		<div class="content">
			<div id="card-data" data-color="#55bd47" data-shop-name="" data-card-name="" data-logo="" data-rights="" data-term="" data-term-begin-time="" data-term-end-time=""></div>
			<div class="bgResize card-region js-show-code @if($card['cover'] == 0) {{$card['cover_value']}} @endif" style=" @if($card['cover'] != 0) background: url({{$card['cover_value']}}) 0 0 no-repeat; background-size: auto 100%; @else background-size: 100% 100%;  @endif;" >
				<div class="card-header">
					<h4 class="shop-name">
						<!-- <span class="shop-logo" style="background-image:url({{ $logo }})"></span> -->
						{{ $shop_name }}</h4>
					<div class="qr-code"></div>
				</div>
				<h3 class="member-type">{{$card['title']}}</h3>
				<div class="card-content">
					@if(empty($record))
					<p class="date">有效期:
						<span>
							@if($card['limit_type'] == 0)
								长期有效
							@elseif($card['limit_type'] == 1)
								{{$card['limit_days']}} 天
							@elseif($card['limit_type'] == 2)
                                {{  date('Y-m-d',strtotime($card['limit_start'])) }}～{{ date('Y-m-d',strtotime($card['limit_start'])) }}
							@endif
						</span>
					</p>
					@else
						<p class="expiry-date">领取时间:
							<span>
							{{ $record['created_at'] }}
							</span>
						</p>
					<!-- <br> -->
						<p class="expiry-date">有效期:
							<span>
							{{ $record['time'] }}
							</span>
						</p>
					@endif
					@if($tag == 1)
						<p class="card-state">未领取</p>
					@endif
				</div>
			</div>
			<div class="twoCode no" style="text-align: center;">
				<img style="width: 200px;height: 200px;" src="" />
				<p style="margin:5px;font-size:13px;">长按二维码识别同步到微信卡包</p>
			</div>
			<div class="errorCode no" style="margin: 30px 0;text-align: center">微信同步二维码失败</div>
			<div class="membership-region js-show-sub-info">
				<h3 class="membership-header">
					<!-- <span class="icon icon-member"></span>  -->
					会员权益
				</h3>
				<div class="membership">
					<ul class="arrow-right">
						@if(in_array(1,$card['member_power']))
							<li class="membership-item">
								<p class="item-name free-shipping">包邮</p>
							</li>
						@endif
						@if(in_array(2,$card['member_power']))
							<li class="membership-item">
								<p class="item-name discount">{{$card['discount']}}折</p>
							</li>
						@endif
						@if(in_array(3,$card['member_power']))
							<li class="membership-item">
								<p class="item-name coupon">优惠券</p>
							</li>
						@endif
						@if(in_array(4,$card['member_power']))
							<li class="membership-item">
								<p class="item-name score">送积分</p>
							</li>
						@endif
					</ul>
				</div>


				<p class="block-sub-desc js-block-sub-desc" style="display:none">
					@if(in_array(1,$card['member_power']))
						享受会员包邮<br>
					@endif
					@if(in_array(2,$card['member_power']))
						会员折扣{{$card['discount']}}折<br>
					@endif
					@if(in_array(3,$card['member_power']))
						随卡获赠优惠券：
						@foreach($couponData as $v)
								{{$v['title']}}  &nbsp;
							@endforeach
						<br>
					@endif
					@if(in_array(4,$card['member_power']))
						随卡获赠积分：{{$card['score']}}分<br>
					@endif
			</div>

			<div class="block block-list">
				<a href="javascript:;" class="block-item js-show-sub-info">
					<p class="arrow-right">
						<!-- <span class="icon icon-description"></span>  -->
						使用须知
					</p>
					<p class="block-sub-desc js-block-sub-desc" style="display:none">
						{{$card['description']}}
					</p>
				</a>
				<a href="javascript:;" class="block-item js-show-code">
					<p class="arrow-right">
						<!-- <span class="icon icon-cert"></span> -->
						出示会员凭证
					</p>
				</a>
				@if($reqFrom == 'aliapp')
				<a class="block-item custPhone J_makePhoneCall" data-phone="{{$card['service_phone']}}">
					<p class="arrow-right">
						<!-- <span class="icon icon-tel"></span>  -->
						客服电话
					</p>
				</a>
				@else
				<a class="block-item custPhone" href="tel:{{$card['service_phone']}}">
					<p class="arrow-right">
						<!-- <span class="icon icon-tel"></span>  -->
						客服电话
					</p>
				</a>
				@endif
				<a href="/shop/index/{{$card['wid']}}" class="block-item">
					<p class="arrow-right">
						<!-- <span class="icon icon-homepage"></span>  -->
						店铺主页
					</p>
				</a>

			</div>

			
			@if(!empty($record))
				<a href="javascript:;" class="single-block block delete-card js-delete-card" data-route="/shop/member/delete/{{session('wid')}}/{{$record['id']}}" data-wid="{{session('wid')}}" id="del">删除会员卡</a>
			@endif
			@if(!empty($record))
				@if($record['active_status'] == 0)
					<div class="bottom-fix box_top_1px">
						<div class="btn-1-1">
							<button onclick="window.location.href='/shop/member/cardActive/{{session('wid')}}?id={{$record['id']}}';return false" class="btn bg-primary js-obtain-card-btn1">激活该卡</button>
						</div>
					</div>
					@elseif($record['is_default'] == 0 && $card['state'] ==1)
					<div class="bottom-fix box_top_1px">
						<div class="btn-1-1">
							<button class="btn bg-primary js-obtain-card-btn" data-placement="/shop/member/setDefault/{{session('wid')}}/{{$record['id']}}">默认使用该卡</button>
						</div>
					</div>
				@endif
			@else
				<div class="bottom-fix box_top_1px">
					<div class="btn-1-1">
						<button class="btn bg-primary js-obtain-card-btn">领取会员卡</button>
					</div>
				</div>
			@endif

		</div>
	</div>
	{{--二维码弹框--}}
	@if(!empty($record))
		<div id="qcode" style="display: none;height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;">
			<div id="dJOOkY4yOt" class="" style="overflow: hidden; position: absolute; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); width: 90%; visibility: visible; opacity: 1;"><div class="member-popout">
					<div class="popout-header" style="background-color:#55bd47;">
						<div class="clearfix">
							<h3 class="title"><span class="logo">
								<img src="{{ $logo}}" style="width:100%;height:100%;" />
							</span>{{$card['title']}}</h3>
							<button class="close js-close">&times;</button>
						</div>
						<div class="clearfix card-region-popout" style="margin-top: -16px">
							<h2 class="card-name"></h2>
							<h4 class="card-discount"></h4>
						</div>
					</div>
					<div class="popout-main">
						<div class="qrcode">
							{!! QrCode::size(271)->generate($record['card_num']); !!}
						</div>
						<div class="barcode">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQwAAABGCAIAAAC2Wj16AAAA50lEQVR42u3TQQrEIAxA0dj739kuCkUIkdhFV++thoxY68wfsZhzRsQY4/38eCZ55f7bdZ4nnXneP8+rHfZvtN+hf/7qKdWZOzeW13dOm9dUt9d/yunve3rOf/5R3+5/XXMFsCUSEAmIBEQCIgGRgEhAJCASQCQgEhAJiAREAiIBkYBIAJGASEAkIBIQCYgERAIiAUQCIgGRgEhAJCASEAmIBEQCiAREAiIBkYBIQCQgEhAJIBIQCYgERAIiAZGASEAkgEhAJCASEAmIBEQCIgGRgEgAkYBIQCQgEhAJiAREAiIBRAInbscobIyrrF5QAAAAAElFTkSuQmCC">
							<h3 class="code">{{$record['card_num']}}</h3>
						</div>
						<div class="term-popout">有效期：
							{{$record['time']}}
							{{--@if($card['limit_type'] == 0)--}}
								{{--长期有效--}}
							{{--@elseif($card['limit_type'] == 1)--}}
								{{--{{$card['limit_days']}}--}}
							{{--@elseif($card['limit_type'] == 2)--}}
								{{--{{$card['limit_start']}}～{{$card['limit_end']}}--}}
							{{--@endif--}}
						</div>
					</div>
					<p class="tip">可截图保存至相册</p>
				</div>
			</div>
		</div>
	@endif
	</div>
</div>
@include('shop.common.footer') 
<div style="height:40px;"></div>
@endsection
@section('page_js')
	<script>
		var reqFrom = "{{ $reqFrom }}";
	</script>
	@if($reqFrom == 'aliapp')
	<script type="text/javascript" src="https://appx/web-view.min.js"></script>
	<script>
		$('.J_makePhoneCall').click(function(){
			var phone = $(this).data('phone');
			my.postMessage({phone_number:phone});
		});
	</script>
	@endif
	<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script>
		var SOURCE_URL = `{{ config('app.source_url') }}`;
		var limit = "{{$card['is_share']}}";//是否分享 0 禁止 1允许
		var card_id = "{{$card['card_id'] or ''}}";//为空不请求会员卡同步入口
		var show = "{{ $show }}"
		if(card_id && show){
			 $.ajax({
			 	type:"GET",
			 	url:"/shop/member/cardQrcodeCreated/{{$card['id']}}",
			 	success:function(res){
			 		if(res.status == 1){
						$(".twoCode").removeClass("no");
						$(".twoCode img").attr("src",res.url);
					}	
			 	},
			 	error:function(){
			 		$(".errorCode").removeClass("no");
			 	}

			 });
		}
		//微信禁止分享
		function onBridgeReady(){
		 	WeixinJSBridge.call('hideOptionMenu');
		}
		if(limit == 0){
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
			        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
			    }
			}else{
			    onBridgeReady();
			}
		}
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
                desc: '移动电商，会搜云享', // 分享描述
                link: "{{ config('app.url') }}shop/member/detail/{{$wid}}/{{$card['id']}}",
                 // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标
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
                title: '移动电商，会搜云享-{{ $title or '' }}', // 分享标题
                desc: '移动电商，会搜云享', // 分享描述
                link: "{{ config('app.url') }}shop/member/detail/{{$wid}}/{{$card['id']}}",
                 // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标
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
                title: '移动电商，会搜云享-{{ $title or '' }}', // 分享标题
                desc: '移动电商，会搜云享', // 分享描述
                link: "{{ config('app.url') }}shop/member/detail/{{$wid}}/{{$card['id']}}",
                 // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: '{{ config('app.source_url') }}mobile/images/fx-logo.jpg', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    jifenAjax();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            // wx.trigger(function(){
              
            // })
            wx.error(function(res){               
                alert("errorMSG:"+res);
            });
        });
	</script>
	<script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        //根据手机屏幕宽度设置卡片的宽高比一定
		// var screenWidth = document.body.clientWidth;
		// console.log(screenWidth)
        // //固定宽高比
        // var aspectRatio = 710/360;
        // var cardWidth = screenWidth - 20;
        // var cardHeight = cardWidth /aspectRatio;
        // //console.log(screenWidth, cardWidth, cardHeight)
        // $(".bgResize").css({"height": cardHeight});
        // $(".card-region .card-header").css("margin-bottom", cardHeight/5.5)
        // $(".card-region .member-type").css("margin-bottom", cardHeight/6)
    </script>
	<script src="{{ config('app.source_url') }}shop/js/until.js" ></script>
	<script src="{{ config('app.source_url') }}shop/js/card_detail.js"></script>
@endsection


