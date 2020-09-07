@extends('shop.common.marketing')
@section('head_css')
<style type="text/css">
    .content .image{text-align:center;padding:30px;}
    .content .image img{width:150px;}
    .content .info{text-align:center;color:#333;}
    .content .info a{color:#38f;}
</style>
@section('main')
<div class="content">
    <div class="image">
        <img src="{{ config('app.source_url') }}shop/images/bangdingchenggong@2x.png">
    </div>
    <p class="info">
        绑定成功！快去<a href="/shop/index/{{session('wid')}}">首页</a>看看吧~
    </p>
</div>
@endsection
@section('page_js')
@endsection