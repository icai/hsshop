@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/wheel_03d983uq.css">
@endsection
@section('main')
<div class="content">
	<div class="metbox">
		<div class="metbox_word">
			{{$data['content']}}
		</div>
	</div>
	<!--存在默认地址时出现-->
	@if ($address)
		<div class="is_address">
			<div class="address">
				<p>姓名：<span>{{$address['title']}}</span></p>
				<p>手机号码：<span>{{$address['phone']}}</span></p>
				<p>地址：<span>{{$address['detail_address']}}</span></p>
			</div>
			<div class="tips">提示:地址只能修改一次</div>
			@if (!$address['is_confirm'])
			<div class="twobtn_wrap"><!--确认之后/修改之后消失-->
				<div class="twobtn">
					<div class="edit_btn foot_btn">修改</div>
					<div class="sure_btn foot_btn">确认</div>
				</div>
			</div>
			@endif
		</div>
	@else
		<!--没有默认地址时出现-->
		<div class="no_address">
			<div>+添加地址</div>
		</div>
	@endif
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript">
	var wid = {{session('wid')}};
	var activity_id = {{$data['activity_id']}}
	var address_id = {{$address['id'] ?? 0}}
	var _token = $('meta[name="csrf-token"]').attr("content");
</script>
<script src="{{ config('app.source_url') }}shop/js/wheel_03d983uq.js"></script>
@endsection