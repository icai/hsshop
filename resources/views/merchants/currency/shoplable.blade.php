@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_smsConf.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')

@endsection
@section('content')

	大法师打发士大夫似的

@endsection
@section('page_js')
<!--特殊按钮js文件-->
<script type="text/javascript">
	$data = {!! $data !!}
</script>
<script src="{{ config('app.source_url') }}mctsource/js/specialBtn.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_smsConf.js"></script>
@endsection