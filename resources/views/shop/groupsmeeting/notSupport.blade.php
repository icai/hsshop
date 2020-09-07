@extends('shop.common.template')
@section('head_css')
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/safeguard_1c5f2b2c598b88d2eceffd92bf496cfe.css" media="screen">
@endsection

@section('main')
	<div class="container not-support-container" style="min-height: 667px;">
	    <i class="not-support-cry"></i>
	    <div class="not-support-tip">
	        <p>
	            未成团订单不支持退款申请
	        </p>
	        <p class="font-size-12 c-gray-dark">
	            开团后的24小内未达到人数要求，订单将关闭并退款
	        </p>
	    </div>
	    <div class="action-container">
	        <a href="/shop/grouppurchase/groupon/{{$groups_id}}" class="btn btn-block btn-green">
	            查看团详情
	        </a>
	        <a href="javascript:history.back();" class="btn btn-block btn-white">
	            返回
	        </a>
	    </div>
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
@endsection