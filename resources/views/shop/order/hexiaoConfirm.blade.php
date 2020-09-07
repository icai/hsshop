<!--自提凭证页面-->
<!--add by 韩瑜 2018-9-20-->
@extends('shop.common.template')
@section('head_css')
    <style>
    	.content{
    		margin-top: 10px;
    		width: 100%;
    		background: #f5f5f5;
    	}
    	.content-top{
    		width: 100%;
    		background: #fff;
    	}
		.content-top .title{
			border-bottom: 1px solid #e5e5e5;
			height: 40px;
			width: 95%;
			margin-left: 10px;
		}
		.content-top .title .title-left{
			float: left;
			line-height: 40px;
			width: 22%;
			color: #666666;
			font-size: 15px;
		}
		.content-top .title .title-right{
			float: left;
			line-height: 40px;
			color: #333333;
			font-size: 14px;
			width: 75%;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;	
		}
		.content-bottom{
			margin-top: 10px;
			width: 100%;
			background: #fff;
			min-height: 380px;
		}
		.content-bottom .product-top{
			width: 100%;
			border-bottom: 1px solid #e5e5e5;
		}
		.content-bottom .product-top .title-wrap{
			border-bottom: 1px solid #e5e5e5;
			width: 95%;
			margin-left: 10px;
		}
		.content-bottom .product-top .img1{
			width: 17px;
			display: inline;
			margin: 12px 4px 0 0;
		}
		.content-bottom .product-top .title{
			height: 40px;
			line-height: 40px;
			color: #333333;
			font-size: 16px;
			display: inline;
		}
		.content-bottom .product-top .img2{
			display: inline;
			width: 6px;
			margin-left: 5px;
		}
		.content-bottom .product-det{
			padding: 10px 0;
			height: 90px;
			width: 95%;
			margin-left: 10px;
		}
		.content-bottom .product-det img{
			float: left;
			width: 90px;
			height: 90px;
			margin-right: 10px;
		}
		.content-bottom .product-det .product-det-r h2{
			color: #333333;
			font-weight:bold;
			font-size: 16px;
			display: -webkit-box;
			overflow:hidden;
			text-overflow: ellipsis;
			display:-webkit-box;
			-webkit-line-clamp:2;
			-webkit-box-orient:vertical;
		}
		.content-bottom .product-det .product-det-r h3{
			color: #999999;
			font-weight:500;
			font-size: 14px;
			padding: 7px 0 7px 0;
		}
		.content-bottom .product-det .product-det-r p{
			color: #FF3742;
			font-weight:500;
			font-size: 16px;
		}
		.content-bottom .product-det .product-det-r p span{
			color: #666666;
			font-weight:500;
			font-size: 14px;
			float: right;
			padding-right: 10px;
		}
		.content-bottom .product-bottom{
			height: 40px;
			width: 100%;
			border-bottom: 1px solid #e5e5e5;
		}
		.content-bottom .product-bottom .product-bottom-l{
			float: left;
			line-height: 40px;
			width: 21%;
			color: #666666;
			font-size: 15px;
			margin-left: 10px;
		}
		.content-bottom .product-bottom .product-bottom-r{
			float: left;
			line-height: 40px;
			color: #333333;
			font-size: 14px;
			width: 75%;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;	
		}
		.content-bottom .product-price{
			color: #333333;
			font-size: 16px;
			width: 100%;
		}
		.content-bottom .product-price p{
			float: right;
			margin: 10px 10px 0 0;
		}
		.content-bottom .product-price p span{
			color: #FF3742;
		}
		.content-bottom .hexiao-btn{
			width: 100%;
			text-align: center;
			margin-top: 100px;
		}
		.content-bottom .hexiao-btn a{
			width: 93%;
			display: inline-block;
			border-radius: 4px;
			background: #cdcdcd;
			color: #fff;
			height: 45px;
			line-height: 45px;
			font-weight:500;
			font-size: 19px;
		}
		.content-bottom .hexiao-tips{
			text-align: center;
			font-size: 12px;
			color: #999999;
			line-height: 38px;
		}
		.hexiao-model{
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.8);
			display: flex;
			display: -webkit-flex;
			justify-content: center;
			-webkit-box-pack: center;
			align-items: center;
			-webkit-align-items: center;
			z-index: 100;
			display: none;
		}
		.hexiao-model .hexiao-bg{
			width: 250px;
			height: 250px;
			margin: 30% auto;
			background:url(/shop/images/hexiao-model.png) no-repeat center;
            background-size: 100% 100%;
            position: relative;
		}
		.hexiao-model .hexiao-bg .tips1{
			width: 100%;
			position: absolute;
			top: 145px;
			text-align: center;
			color: #fff;
			font-size: 19px;
		}
		.hexiao-model .hexiao-bg .tips2{
			width: 100%;
			position: absolute;
			top: 213px;
			text-align: center;
			color: #666666;
			font-size: 19px;
		}
    </style>
@endsection
@section('main')
<div class="container " style="min-height: 482px;">

        

    <div class="content">
		<div class="content-top">
			<ul>
				<li>
					<div class="title">
						<div class="title-left">提货人</div>
						<div class="title-right">{{ $orderData['ziti']['ziti_contact'] or '' }}</div>
					</div>
				</li>
				<li>
					<div class="title">
						<div class="title-left">提货时间</div>
						<div class="title-right">请尽快到店自提</div>
					</div>
				</li>
				<li>
					<div class="title">
						<div class="title-left">提货地址</div>
						<div class="title-right">
							{{ $orderData['ziti']['orderZiti']['province_title'] or '' }}{{ $orderData['ziti']['orderZiti']['city_title'] or '' }}{{ $orderData['ziti']['orderZiti']['area_title'] or '' }}{{ $orderData['ziti']['orderZiti']['address'] or '' }}
						</div>
					</div>
				</li>
				<li>
					<div class="title" style="border: none;">
						<div class="title-left">支付方式</div>
						@if($orderData['pay_way'] == 1)
						<div class="title-right">微信支付</div>
						@elseif($orderData['pay_way'] == 2)
						<div class="title-right">支付宝支付</div>
						@elseif($orderData['pay_way'] == 3)
						<div class="title-right">储值余额支付</div>
						@endif
					</div>
				</li>
			</ul>
		</div>
		<div class="content-bottom">
			<div class="product-top">
				<a href="javascript:void(0);">
					<div class="title-wrap">
						<img class="img1" src="{{ config('app.source_url') }}shop/images/dianpu.png" alt="" />
						<div class="title">{{$__weixin['shop_name']}}</div>
					</div>
				</a>
				@if($orderData['orderDetail'])
				@foreach($orderData['orderDetail'] as $val)
				<div class="product-det">
					<img src="{{ imgUrl() }}{{ $val['img'] }}" alt="" />
					<div class="product-det-r">
						<h2>{{ $val['title'] }}</h2>
						<h3>{{ $val['spec'] }}</h3>
						<p>¥{{ $val['price'] }}<span>x{{ $val['num'] }}</span></p>
					</div>
				</div>
				@endforeach
				@endif
			</div>
			<div class="product-bottom">
				<div class="product-bottom-l">买家留言</div>
				<div class="product-bottom-r">{{ $orderData['buy_remark'] }}</div>
			</div>
			<div class="product-price">
				<p>已付款：<span>¥{{ $val['price'] }}</span></p>
			</div>
			<div class="hexiao-btn @if($orderData['status'] == 1) hide @endif">
				<a href="javascript:void(0)">已核销</a>
			</div>
			<div class="hexiao-btn primary @if($orderData['status'] == 2) hide @endif">
				<a style="background:#2abe38" href="javascript:void(0)">核销</a>
			</div>
		</div>
    </div>
    
</div>
<div class="hexiao-model">
	<div class="hexiao-bg">
		<div class="tips1">
			<p>恭喜你，核销成功！</p>
		</div>
		<a href="javascript:void(0)">
			<div class="tips2">
				<p>知道了</p>
			</div>
		</a>
	</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var reqFrom = "{{ $reqFrom }}";
        var oid = {{$orderData['id']}};
    </script>
    @if($reqFrom == 'aliapp')
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script type="text/javascript">
        $(function(){
        	$('.primary').click(function(){
        		$.get('/shop/order/hexiaoSure',{oid:oid},function(data){
        			$('.hexiao-model').css('display','block')
        			$('.primary').remove();
	        		$('.hexiao-btn').removeClass('hide');
        		})
        	})
        	$('.tips2').click(function(){
        		$('.hexiao-model').css('display','none')
        	})
        })
    </script>
@endsection


