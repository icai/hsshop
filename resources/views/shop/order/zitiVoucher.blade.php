<!--自提凭证页面-->
<!--add by 韩瑜 2018-9-20-->
@extends('shop.common.template')
@section('head_css')
    <style>
        .lading-code-container {
            margin: 20px 12px;
            height: 545px;
            background: url(/shop/images/zitiVoucher.png) no-repeat center;
            background-size: 100% 100%;
            position: relative;
        }
        .lading-code-title {
            padding: 15px 0 0 12px;
            text-align: left;
        }
        .lading-code-title h3{
        	color: #333333;
        	font-size: 15px;
        	font-weight:bold;
        	margin-bottom: 8px;
        	width: 100%;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
        }
        .lading-code-title h3 span{
        	
        	display: inline-block;
        	background: #F58E32;
        	height: 17px;
        	line-height: 18px;	
        	width: 40px;
        	text-align: center;
        	margin-left: 10px;
        	border-radius: 4px;
        	font-size: 12px;
        	color: #fff;
        	font-weight:500;
        }
        .lading-code-title p{
        	font-size: 15px;
        	color: #333333;
        	padding: 5px 0;
        	width: 96%;
        }
        .lading-code-title img{
        	width: 15px;
        	height: 15px;
        	float: left;
        	margin:9px 5px 0 0;
        }
        .lading-code {
            position: absolute;
            width: 100%;
            text-align: center;
            font-size: 18px;
            color: #333;
            top: 315px;
        }
        .lading-code-msg {
            position: absolute;
            top: 350px;
            width: 100%;
        }
        .desc-item {
        	display: inline-block;
            font-size: 14px;
            background: #f5f5f5;
            width: 198px;
            height: 24px;
            color: #999999;
            margin-top: 12px;
        }
        .lading-code-img1 {
        	text-align: center;
        	width: 100%;
        	position: absolute;
        	top: 115px;
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
        	top: 112px;
        }
        .lading-code-img2 img {
        	width: 130px;
        	height: 130px;
        	margin-top: 15px;
        }
        .lading-code-order{
        	position: absolute;
        	top: 437px;
        	width: 100%;
        	font-size: 15px;
        }
        .lading-code-order p{
        	float: left;
        	padding-left: 10px;
        }
        .lading-code-order span{
        	float: right;
        	padding-right: 10px;
        }
        .order-btn{
        	position: absolute;
        	top: 483px;
        	font-size: 15px;
        	width: 100%;
        }
        .order-btn a{
        	display: inline-block;
        	border:1px solid #cccccc;
        	border-radius:4px;
        	text-align: center;
        	height: 44px;
        	width: 100px;
        	line-height: 44px;
        	color: #666666;
        	box-sizing: border-box;
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
                <div class="lading-code-container">
                    <div class="lading-code-title">
                    	<h3>{{ $orderData['ziti']['orderZiti']['title'] or '' }}<span>自提点</span></h3>
                    	<img src="{{ config('app.source_url') }}shop/images/zitiVoucher-address.png" alt="" />
                        <p style="line-height: 24px;">
                            {{ $orderData['ziti']['orderZiti']['province_title'] or '' }}{{ $orderData['ziti']['orderZiti']['city_title'] or '' }}{{ $orderData['ziti']['orderZiti']['area_title'] or '' }}{{ $orderData['ziti']['orderZiti']['address'] or '' }}
                        </p>
                    </div>
                    <div class="lading-code-img2">
                    	<!--二维码-->
                    	{!! $qrcodeData['qrcode'] !!}
                    </div>
                    <div class="lading-code">提货码：{{ $orderData['hexiao_code'] or '' }}</div>
                    <div class="lading-code-msg">
                        <div class="desc-item" style="line-height: 25px;">
							向商家出示邀请码
                        </div>
                    </div>
                    @if($orderData['orderDetail'])
                    @foreach($orderData['orderDetail'] as $val)
                    <div class="lading-code-order">
						<p>{{ $val['title'] }}</p><span>x{{ $val['num'] }}</span>
                    </div>
                    @endforeach
                    @endif
                    <div class="order-btn">
                        <a class="" href="/shop/order/detail/{{ $orderData['id'] }}">查看详情</a>
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
        var reqFrom = "{{ $reqFrom }}";
        var type = "{{ $type }}";
        var oid = {{ $orderData['id'] }}

    </script>
    @if($reqFrom == 'aliapp')
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script type="text/javascript">
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
@endsection


