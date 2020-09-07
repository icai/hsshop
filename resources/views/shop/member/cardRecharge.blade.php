@extends('shop.common.template')
@section('title', '充值')   
@section('head_css') 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cardRecharge.css">
@endsection
@section('main')  
<div class="content">
	<!-- <div style="height:20px;"></div> -->
    <ul class="list-wrap">
    	<li class="line">
    		<div class="list-title">余额</div>
    		<div class="list-content"> ￥{{ $member['money']/100 }}</div>
    		<div class="list-record"><a href="/shop/member/rechargeRecord">充值记录</a></div>
    	</li>
    </ul>
    <dl class="sum-wrap">
    	<dt>请选择充值金额</dt>
    	<dd>
            @forelse($ruleList['data'] as $v)
    		<a class="balance-wrap" data-money="{{ $v['money']/100 }}" href="javascript:;">
    			<p class="balance-wrap-title">{{ $v['money']/100 }}元</p>
    			<p class="balance-wrap-explain">送{{ $v['add_score'] }}积分</p>
    		</a>
            @endforeach

    	</dd> 
    </dl>
</div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var wid = "{{session('wid')}}"; 
    </script> 
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/cardRecharge.js"></script> 
@endsection