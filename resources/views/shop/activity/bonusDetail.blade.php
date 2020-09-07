@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/bonusDetail.css">	
@endsection
@section('main')
	<div class="container" id="container">
		<div class='nav_img'>
			@if ($data['shop_micro_page_id'])
                <a href="/shop/microPage/index/{{$wid}}/{{$data['shop_micro_page_id']}}">
            @else
                <a href="javascript:;">
            @endif
                    @if ($data['image'])
                        <img src="{{imgUrl($data['image'])}}" alt="" />
                    @else
                        <img src="{{ config('app.source_url') }}shop/images/bonusDetail-banner.jpg" alt="" />
                    @endif
                </a>
		</div>
	  	<div class='packet_cont'>
            @forelse($data['coupons'] as $coupon)
			<div class='packet_coupon'>
	  			<div class='coupon'>
					<div class='coupon_top'>
	  					<div class='coupon_top_left'>￥<span>{{$coupon['amount']}}</span></div>
	  					<div class='coupon_top_right'>
							<div class='coupon_top_right_title'>{{$coupon['title']}}
		  						<p>
		  							@if ($coupon['limit_amount'])
		  							满{{$coupon['limit_amount']}}元可用
		  							@else
		  							无使用门槛
		  							@endif
		  						</p>
		  						<p>{{$coupon['range_type'] ? '部分劵' : '全品类'}}</p>
							</div>
		  				</div>
					</div>
					<div class='coupon_footer'>
                        <span>{{$coupon['start_at']}}至{{$coupon['end_at']}}</span>
					</div>
	 			</div>
			</div>
            @empty
            <div class='packet_coupon'>
	  			<div>
					<div class='tip_no'>
	  					<img src="{{ config('app.source_url') }}shop/images/noBonus.png" alt="" />
					</div>
					<div class='tip_no_text'>所有红包已领完</div>
					<div class='tip_no_text'>请关注商家下期活动</div>
	  			</div>
			</div>
            @endforelse
			<div class='packet_tip'>
	  			<div class='tip_name'>红包已经发送至您的账户：{{$data['phone']}}</div>
	  			<a href="/shop/member/coupons/{{$wid}}/valid"><div class='tip_account'>查看我的红包账户</div></a>
                <a href="/shop/index/{{$wid}}"><div class='tip_use'>立即使用</div></a>
			</div>
	  	</div>
	</div>
</div>
@include('shop.common.footer') 
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript">
    var wid = {!!$wid!!};
</script>
@endsection