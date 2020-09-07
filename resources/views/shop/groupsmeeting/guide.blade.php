@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/guide_gt61j61y.css">
@endsection

@section('main')
	<div class="container">
		<div class="guide content">
	        <div class="tip">
	            <h3 class="font-size-14 bold">什么是多人拼团？</h3>
	            <p class="font-size-14">多人拼团（以下简称：拼团）是指由多人一起拼单购买的团购活动，通过拼团买家可以享受比一般网购更低的折扣。</p>
	        </div>

	        <div class="tip">
	            <h3 class="font-size-14 bold">怎样算拼团成功？</h3>
	            <p class="font-size-14">
	                每一个团的有效期为24小时，在有效期内找到满足人数的好友参加拼团，即可算拼团成功。
	            </p>
	        </div>

	        <div class="tip">
	            <h3 class="font-size-14 bold">拼团失败，怎样退款？</h3>
	            <p class="font-size-14">若24小时内没有凑齐人数，即算作拼团失败。系统会自动将所支付的货款原路退回，具体到账时间以各银行为准。</p>
	        </div>

	        <div class="tip pic-container">
	            <h3 class="font-size-14 bold">拼团流程</h3>
	            <div class="font-size-14 center">
                    <img src="{{ config('app.source_url') }}shop/images/steps2@2x.png" />
                </div>
	        </div>
	    </div>
	</div> 
@endsection
@section('page_js')
@endsection