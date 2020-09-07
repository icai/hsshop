@extends('shop.common.template')
@section('title', '提取记录')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cashLog.css">
@endsection
@section('main')
<div class="container">
    <!-- <div class="earning-body-item">
        <div class="timer-item">2018-06-01 <br>11:11:10</div>
        <div class="money-item">￥44.0</div>
        <div class="status-light">等待付款</div>
    </div> -->
</div>
    
@endsection
@section('page_js')
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/cashLog.js"></script>
@endsection