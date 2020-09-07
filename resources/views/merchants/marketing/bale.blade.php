@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_kg1fntrz.css" />
@endsection
@section('slidebar')
     @include('merchants.marketing.slidebar')
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
    <script src="{{ config('app.source_url') }}mctsource/js/order_kg1fntrz.js"></script>
@endsection