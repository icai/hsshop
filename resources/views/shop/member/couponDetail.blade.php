@extends('shop.common.marketing')
    
@section('head_css')
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css" media="screen">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/coupons_list.css" media="screen">
@endsection
@section('main')
<div class="container" style="min-height: 100%;">
	<div class="content no-sidebar">
		<div class="content-body">

			<div class="promote-card">
                <input type="hidden" id="isValid" value="{{$isValid}}"/>
				<div class="clearfix">
					<h1 class="pull-left font-size-16 promote-card-name">{{$my_coupon['title']}}</h1>
                    @if($data['is_share'])
					    <p class="pull-right font-size-14 center promote-share transparent-color js-share">分享</p>
                    @endif
				</div>

				<p class="center promote-value">
					<span class="promote-value-sign">￥</span><i>{{$my_coupon['amount']}}</i> </p>
				<p class="center font-size-14 promote-limit">
                    @if($my_coupon['limit_amount']>0)
                        订单满 {{$my_coupon['limit_amount']}} 元 (含运费)
                    @else
                        不限制
					@endif
				</p>
				<p class="center font-size-12 transparent-color">
					有效日期： {{date('Y-m-d H:i', strtotime($my_coupon['start_at']))}} ~ {{date('Y-m-d H:i', strtotime($my_coupon['end_at']))}} </p>
				<div class="dot"></div>
			</div>
			<div class="get-promote-card">
				<div class="btns" style="display: flex; justify-content: space-between;">
					<a href="javascript:void(0);" class="btn btn-block receive_url">立即使用</a>
				</div>
				<div class="invalid hide">已过期</div>
				<!-- <p class="center">
					<a href="{{URL('/shop/member/coupons/'.$wid.'/valid')}}" class="font-size-12 c-blue promote-card-list-link">查看我的卡券</a>
				</p> -->
				<div class="hide-alert-box js-alert-box">
                    <div class="sync-alert-box js-sync-alert-box">
                        <a href="javascript:void(0)" class="js-cancel c-gray-dark close-alert">X</a>

                    </div>
                </div>
			</div>
			<div class="promote-goods">
				<div class="block">
					<span class="block-item border-none clearfix" data-wid="{{$wid}}" data-coupon-id="{{$id}}" data-range-type="{{$data['range_type']}}">
            			<span class="pull-left">适用商品</span>
						<span class="pull-right text-info">
                            @if($my_coupon['range_value'])
                                部分指定商品
                            @else
                                全部网店商品
                            @endif
						</span>
					</span>
				</div>
			</div>
			<div class="promote-desc js-promote-desc">
				<h2 class="promote-desc-title">使用说明</h2>
				<div class="block">
					<div class="block-item border-none clearfix">
                        @if($data['description'])
                            <span class="js-desc-detail">@php echo nl2br(e($data['description'])) @endphp</span>
                            <a class="c-blue more-desc pull-right js-more-desc" href="javascript:void(0)">更多</a>
                        @else
                            暂无使用说明
                        @endif
					</div>
				</div>
			</div>
		</div>
		<div id="shop-nav"></div>
	</div>
</div>
	<!--分享的蒙板-->
	<div id="js-share-guide" class="js-fullguide fullscreen-guide hide" style="font-size: 16px; line-height: 35px; color: #fff; text-align: center;">
		<span class="js-close-guide guide-close">×</span>
		<span class="guide-arrow"></span>
		<div class="guide-inner">请点击右上角<br>通过【发送给朋友】功能<br>或【分享到朋友圈】功能<br>把消息告诉小伙伴哟～</div>
	</div>
</body>
<!-- 页面加载开始 -->
<div class="pageLoading">
    <img src="{{ config('app.source_url') }}shop/images/loading.gif">
</div>
<!-- 页面加载结束 -->
@include('shop.common.footer') 
@endsection
@section('page_js')
<script type="text/javascript">
	var wid = "{{ $wid }}";
	var id = "{{ $id }}";
	var link_type = "{{ $data['link_type'] }}";//外链判断条件
	var link_id = "{{ $data['link_id'] }}";//外联指定为商品详情时为商品id   否则为0
	var invalid_text = "{{ $invalidText }}"; //失效原因 已使用或者已过期
</script>
<script src="{{ config('app.source_url') }}shop/js/coupons_use.js" type="text/javascript" charset="utf-8"></script>
@endsection