@extends('shop.common.marketing')
@section('head_css')
	<script src='{{ config('app.source_url') }}mobile/js/rem.js'></script>
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/member_researchDetail.css">
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/Mdate.css"/>
@endsection
@section('main')
<div class="content">
	<div id="app">
		<div v-for="(item,key,idx) in content" :key="item.sort">
			<component :is="item.pageType" :content="item"></component>
        </div>
  	</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script>
	var data = {!! json_encode($data) !!},
		PageContent = data,
		host = '{{ imgUrl() }}',
		wid = "{{$wid}}",
		actId = data.id;
</script>
	<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/member_researchDetail.js"></script>
@endsection