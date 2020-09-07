@extends('shop.common.marketing')
@section('head_css')
	<script src="{{ config('app.source_url') }}mobile/js/rem.js"></script>
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/researchDetail.css"/>
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/Mdate.css"/>
@endsection
@section('main')
<div class="content">
	<div id="app" :style="{'background':background_color}" v-cloak>
		<div v-for="(item,key,idx) in content" :key="item.sort">
			<component :is="item.pageType" :content="item" @ievent="getTaZe"></component>
		</div>
		<p class="line-submit">
			<button @click="submitForm" class="submitBtn" style="border: none" :style="{'background':btnBackColor}">${btnText}</button>
		</p>
  	</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script>
	var data = {!! json_encode($data) !!},
		PageContent = data,
		_host = "{{ config('app.url') }}",
		host = "{{ config('app.source_url') }}",
		source = '{{ imgUrl() }}',
		wid = "{{$wid}}",
		actId = data.id;
</script>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/distpicker.data.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/distpicker.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/iScroll.js"></script>
<script src="{{ config('app.source_url') }}static/js/Mdate.js"></script>
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/js/researchDetail.js"></script>
@endsection

