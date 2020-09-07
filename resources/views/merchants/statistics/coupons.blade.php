@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_u3c5lzb9.css" />
@endsection
@section('slidebar')
@include('merchants.statistics.slidebar')
@endsection
@section('middle_header')

@endsection
@section('content')
<div class="content">
    程序猿和攻城狮正在积极努力搬砖中...
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_u3c5lzb9.js"></script>
@endsection