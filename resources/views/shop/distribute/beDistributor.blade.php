@extends('shop.common.template')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/distribute_bebutor.css">
@endsection
@section('main') 
<div class="container " id="container">
	<div class="distribute_box">		
		<div class="distribute_title zx_texcenter">成为分销客</div>
		<div class="distribute_subtitle zx_texcenter">一步开启赚钱之旅!</div>
		<div class="zx_texcenter distribute_con">
			<p class="zx_mabom10">获得百万佣金</p>
			<p>享受分销乐趣</p>
		</div>
		<div class="zx_texcenter distribute_button">
			<div class="button_click button_sure">确认</div>
			<div class="button_click button_close no_longer" data-tag = "1">取消</div>
		</div>
		<div class="zx_texcenter distribute_xieyi">
			点击"确认"按即表示同意
			<a href="/shop/distribute/distributeAgreement" class="xieyi_a"><分销客协议></a>
		</div>
		<a class="distribute_tip no_longer" href="javascript:void(0);" data-tag = "2">不再提示</a>
	</div>
</div>
<!-- 当前页面js -->
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript">
	var wid = "{{session('wid')}}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/distribute_bebutor.js"></script>
@endsection