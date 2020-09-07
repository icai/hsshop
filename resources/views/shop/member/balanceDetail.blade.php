@extends('shop.common.template')
@section('title', '余额明细')   
@section('head_css') 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/balanceDetail.css">
@endsection
@section('main')  
<div class="content white" id="app">
    <nav class="balance-detail-nav box_bottom_1px">
    	<a href="javascript:;" :class="{active:balanceType==0}" v-on:click="setNav(0)"><span>全部</span></a>
    	<a href="javascript:;" :class="{active:balanceType==1}" v-on:click="setNav(1)"><span>收入</span></a>
    	<a href="javascript:;" :class="{active:balanceType==2}" v-on:click="setNav(2)"><span>支出</span></a>
    </nav>
    <article class="content-wrap"> 
		<section v-for="(vo,index) in list" class="box_bottom_1px">
			<a href="javascript:;">
				<p class="list-wrap">
					<span class="pay-name" v-text="vo.pay_name"></span>
					<span :class="[vo.type_name=='-'?'text-primary' : 'text-success']" v-text="vo.type_name+vo.money" style="font-size:18px;font-weight:bold"></span>
				</p>
				<p class="list-wrap">
					<span v-text="vo.created_at" style="font-size:12px;color:#999999;"></span>
					<span v-text="vo.pay_way_name" style="font-size:14px;color:#666666;"></span>
				</p>
                <p class="baseInfo" v-if="vo.pay_desc"><span>备注：</span><span v-html="vo.pay_desc"></span></p>
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
	<script src="{{ config('app.source_url') }}shop/js/balanceDetail.js"></script>
@endsection