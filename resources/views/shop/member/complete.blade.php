@extends('shop.common.marketing')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/complete.css"/>
@endsection
@section('main')

<div class="container ">
	
	<div class="content">
		<div class="result-area success"></div>
		<p class="result-content">已成功领取会员卡</p>
		<a class="btn btn-block" href="/shop/index/{{session('wid')}}">进店逛逛</a>
	</div>
	
</div>



@include('shop.common.footer') 
<div style="height:40px;"></div>
@endsection
@section('page_js')
@endsection


