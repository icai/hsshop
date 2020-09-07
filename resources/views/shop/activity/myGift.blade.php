@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/wheel_vbca56zi.css">
@endsection
@section('main')
<div class="content">
	<div class="mod-con">
		<div class="mod-div">
			<p>确定要删除这个赠品吗？</p>
			<div class="mod-bom">
				<a class="mod-quxiao">取消</a>
				<a class="mod-sure">确定</a>
			</div>
		</div>
	</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script type="text/javascript">
		var wid = {{session('wid')}};
		var _host = "{{ config('app.source_url') }}";
		var imgUrl = "{{ imgUrl() }}";
	</script>
	<script src="{{ config('app.source_url') }}shop/js/wheel_vbca56zi.js"></script>
@endsection