@extends('shop.common.marketing')
	@section('title', '会员卡列表')   
	@section('head_css')
	 	<link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
	    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/mycart.css">
	@endsection
	@section('main')  
	    <div class="content">
	        <div class="info">
	            <a class="user-info box_bottom_1px" href="/shop/member/set/{{session('wid')}}">
		            <div class="avatar" style="background-image: url({{$member['headimgurl']}});"></div>
		            <h3 class="ellipsis">{{$member['truename']}}</h3>
		            <h5 class="ellipsis">{{$member['mobile']}}</h5>
		            <i class="arrow">设置</i>
		        </a>
	        </div>
			@if($reqFrom == 'wechat')
	        <div class="balance-wrap">
	        	<div class="balance-wrap-left">
	        		<p class="balance-text">余额(元)</p>
	        		<p class="balance-value">{{ $member['money']/100 }}</p>
	        	</div>
	        	<div class="balance-wrap-right">
					<a href="/shop/member/balanceDetail" class="balance-detail">明细</a>
	        		<a href="/shop/member/cardRecharge" class="btn-cz box_1px">充值</a>
	        	</div>
	        </div>
			@endif
	        @forelse($cartList as $value)
	        <div class="card-list">
	            <div class="card-area">
	              	<a href="/shop/member/detail/{{session('wid')}}/{{$value['memberCard']['id']}}?id={{$value['id']}}" class="card-item @if($value['state']==3) is-over @endif @if($value['memberCard']['cover'] == 0) {{$value['memberCard']['cover_value']}}  @endif" style=" @if($value['memberCard']['cover'] != 0) background: url({{$value['memberCard']['cover_value']}}) 0 0 no-repeat @endif; background-size: 100% auto;">

	                  	<h3 class="ellipsis">{{ $value['memberCard']['title'] }} @if($value['is_new'])<span class="new-sign">NEW</span>@endif</h3>
	                  	<div class="content-area">
	                     	<div class="rights">{{ $value['memberCard']['power_desc'] }} </div>
	                     	@if($value['state']==-1)
	                     	<div class="state">已删除</div>
	                     	@elseif($value['state']==2)
	                     	<div class="state">未激活</div>
	                     	@elseif($value['state']==3)
	                     	<div class="state">已过期</div>
							@elseif($value['state']==4)
								<div class="state">未开始</div>
							@elseif($value['state']==5)
								<div class="state">已禁用</div>
							@elseif($value['state']==1)
								<div class="state">使用中</div>
	                     	@endif
	                  	</div>
						@if($value['is_default'] == 1)
	                    	<div class="default-card"></div>
						@endif


						@if(isset($value['leftDays']))
							<div class="more-time">
								<p>剩余时间</p>
								<div class="time-bar">{{ $value['leftDays'] }} 天</div>
							</div>
						@else
							<div class="more-time">
								<p>无期限</p>
							</div>
						@endif

	                </a>
	      		</div>
	        </div>
	        @empty
	        <div class="empty-notice"></div>
	        <p class="notice">暂无会员卡</p>
	        @endforelse
		</div>
	@include('shop.common.footer')    
    @endsection
	@section('js')
 	
	@endsection