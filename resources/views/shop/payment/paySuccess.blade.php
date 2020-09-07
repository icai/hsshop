@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cashier_37c11f150f3d2a2b01fbc8c4d9e92bf4.css">
    <style>
        .lading-code-container {
            margin: 0 12px;
            height: 460px;
            background: url(/shop/images/pay-success-1.png) no-repeat center;
            background-size: 100% 100%;
            position: relative;
        }
        .lading-code-title {
            font-size: 15px;
            color: #333;
            padding: 15px 0 0 30px;
            text-align: left;
        }
        .lading-code {
            position: absolute;
            width: 100%;
            text-align: center;
            font-size: 18px;
            color: #333;
            top: 252px;
        }
        .lading-code-msg {
            position: absolute;
            top: 296px;
            padding: 0 12.5px 0 12.5px;
            width: 100%;
            box-sizing: border-box;
        }
        .lading-code-time {
            position: absolute;
            top: 346px;
            padding: 0 12.5px 0 12.5px;
            width: 100%;
            box-sizing: border-box;
        }
        .lading-code-product{
            position: absolute;
            top: 394px;
            padding: 0 12.5px 0 12.5px;
            width: 100%;
            box-sizing: border-box;
        }
        .lading-code-price{
            position: absolute;
            top: 423px;
            padding: 0 12.5px 0 12.5px;
            width: 100%;
            box-sizing: border-box;
        }
        .desc-item {
           font-size: 14px;
           text-align: left;
           display: flex;
        }
        .item-left {
            display: inline-block;
            color:#999;
        }
        .item-right {
            display: inline-block;
            color: #333;
            max-width: 250px;
        }
        .item-time {
            margin-top: 15px;
        }
        .sep-line {
            border-top: 1px dashed #ccc;
            margin: 14px 0 12px 0;
        }
        .item-price {
            margin-top: 14px;
        }
        .lading-code-img1 {
        	text-align: center;
        	width: 100%;
        	position: absolute;
        	top: 55px;
        }
        .lading-code-img1 img {
        	width: 250px;
        	height: 66px;
        	margin-top: 15px;
        }
        .lading-code-img2 {
        	text-align: center;
        	width: 100%;
        	position: absolute;
        	top: 49px;
        }
        .lading-code-img2 img {
        	width: 130px;
        	height: 130px;
        	margin-top: 15px;
        }
        .showtips{
			width: 100%;
			height: auto;
			background: #ff32329c;
			color: #fff;
		}
		.showtips h4{
			font-size: 22px;
			padding: 10px;
		}
		.showtips p{
			padding-top:10px ;
			padding: 0 10px 10px 10px;
		}
    </style>
@endsection
@section('main')
<div class="container " style="min-height: 482px;">
	<div class="showtips" style="display: none;">
        <h4>温馨提示：</h4>
        <p>该订单已核销</p>
   </div>
    <div class="content">
        <div class="paid-status success">
            <div class="header center">
                <h2>
                    <p class="success-icon"></p>
                    <p class="paid-success-tips">订单支付成功</p></h2>
                <!-- 价格 -->
                <p class="price-warp">
                    <span class="">￥{{ $order['pay_price'] or $order['money']/100 }}</span>
                </p>

                @if(isset($order['is_takeaway']) && $order['is_takeaway'] == 1)
	                <p style="text-align:center;padding:10px 0">
                        准备等待美味的饭菜吧~
                    </p>
                @elseif($isShowPoint == 1)
                    <p style="text-align:center;padding:10px 0">
                        确认收货后会有送积分哦！
                    </p>
                @endif 

                @if(isset($order['is_hexiao']) && $order['is_hexiao'] == 1)
	                <div class="lading-code-container" style="margin-top: 10px;">
	                    <div class="lading-code-title">自提订单提货凭证</div>
	                    <div class="lading-code-img2">
	                    	<!--二维码-->
	                    	@if(isset($order['qrcode']) && !empty($order['qrcode']))
                            {!! $order['qrcode'] !!}
                            @endif
	                    </div>
	                    <div class="lading-code">提货码：{{ $order['hexiao_code'] or '' }}</div>
	                    <div class="lading-code-msg">
	                        <div class="desc-item" style="line-height: 25px;">
	                            <div class="item-left">提货地址：</div>
	                            <div class="item-right">{{ $order['ziti']['orderZiti']['province_title'] or '' }}{{ $order['ziti']['orderZiti']['city_title'] or '' }}{{ $order['ziti']['orderZiti']['area_title'] or '' }}{{ $order['ziti']['orderZiti']['address'] or '' }}</div>
	                        </div>
	                    </div>
	                    <div class="lading-code-time">
	                        <div class="desc-item" style="line-height: 25px;">
	                            <div class="item-left">到店时间：</div>
	                            <div class="item-right">请尽快到店自提</div>
	                        </div>
	                    </div>
	                    <div class="lading-code-product">
	                        <div class="desc-item" style="line-height: 25px;">
	                            <div class="item-left">商品信息：</div>
	                            <div class="item-right">{{ $order['orderDetail'][0]['title'] or '' }}</div>
	                        </div>
	                    </div>
	                    <div class="lading-code-price">
	                        <div class="desc-item" style="line-height: 25px;">
	                            <div class="item-left">实付金额：</div>
	                            <div class="item-right">{{ $order['pay_price'] or 0 }}元</div>
	                        </div>
 	                    </div>
	                </div>
                @endif
            </div>
            <div class="nbody nBody_li">
                <div>
                    <ul>
                        <li>
                            <label>支付方式</label>
                            <div>
                                @if($order['pay_way'] == 1)
                                    微信支付
                                @elseif($order['pay_way'] == 2)
                                支付宝支付
                               @elseif($order['pay_way'] == 3)
                                储值余额支付
                                @elseif($order['pay_way'] == 4)
                                货到付款
                                @elseif($order['pay_way'] == 5)
                                代付
                                @elseif($order['pay_way'] == 6)
                                领取赠品

                                @elseif($order['pay_way'] == 7)
                                    优惠兑换
                                @elseif($order['pay_way'] == 8)
                                    银行卡支付
                                @elseif($order['pay_way'] == 9)
                                    会员卡支付
                                    @endif

                            </div></li>
                        {{--<li class="">--}}
                            {{--<label>关注店铺公众号</label>--}}
                            {{--<div>--}}
                                {{--<a href="javascript:;" class="btn js-favour-shop">关注</a></div>--}}
                        {{--</li>--}}
                    </ul>
                </div>
            </div>
            <div class="bottom">
                <div class="action-container">
                    <div class="item" style="width:100%;">
                     {{--@if(!empty($card))--}}
                        {{--<a href="/shop/member/detail/{{session('wid')}}/{{$card['card_id']}}?id={{$card['id']}}" class="btn btn-green btn-block">获取一张会员卡</a></div>--}}
                    {{--@endif--}}
                    <!-- todo 记得调整样式 -->
                    {{--<div class="item" style="width: 100%; ">--}}
                        {{--<a href="" class="--}}
                        {{--btn btn-white btn-block         ">我要晒订单</a>--}}
                    {{--</div>--}}
                    <div class="item" style="width: 100%; ">
                        @if($type == 1)
                        <a class="btn btn-block btn-white" href="/shop/member/mycards/{{$order['wid']}}">查看订单详情</a>
                        @else
                        <a class="btn btn-block btn-white" href="/shop/order/detail/{{$order['id']}}">查看订单详情</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var oid = {{ $order['id'] }};
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    @if(isset($order['is_hexiao']) && $order['is_hexiao'] == 1)
    <script type="text/javascript">
    	//add by 韩瑜 2018-9-25
    	//是否扫二维码轮询
		$(function(){
			get_comments()
			function get_comments(msg){
				if(msg == undefined){
					msg = '';
				}
				$.ajax({
					type:"get",
					url:"/shop/order/scanLongConnet",
					data:{oid:oid},
					dataType:'json',
					success:function(data){
						if (data.status == 200){
                                location.href = '/shop/order/hexiaoRedirect?oid='+oid+'&come=user'
						}else if(data.status == -3){
							$('.showtips').css('display','block')
						}
						setTimeout(get_comments,1000)
					}
				});
			}	
		})
    </script>
    @endif
@endsection


