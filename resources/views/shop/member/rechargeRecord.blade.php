@extends('shop.common.template')
@section('title', '充值记录')   
@section('head_css') 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/rechargeRecord.css">
@endsection
@section('main')  
<div class="content white" id="app">
	<article class="content-wrap">
		<section v-for="(vo,index) in list" class="box_bottom_1px">
			<a href="javascript:;">
				<p class="list-wrap">
					<span v-text="vo.pay_name" style="font-size:16px;"></span>
					<span :class="[vo.type_name=='-'?'text-primary' : 'text-success']" v-text="vo.type_name+vo.money" style="font-size:18px;font-weight:bold;"></span>
				</p>
				<p class="list-wrap">
					<span v-text="vo.created_at" style="font-size:12px;color:#999999;"></span>
					<span v-text="vo.pay_way_name" style="font-size:14px;color:#666666;"></span>
				</p>
			</a>
		</section> 
	</article>
	<aside v-text="postText"></aside>
</div>
@include('shop.common.footer')    
@endsection
@section('page_js')
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script> 
	<script src="{{ config('app.source_url') }}shop/js/rechargeRecord.js"></script> 
@endsection