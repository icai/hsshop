@extends('home.mobile.default._layouts')

@section('title',$title) 
@section('css')
	<meta name="keywords" content="会搜云微商城,电商APP定制,小程序开发,微商城开发,杭州APP开发,商城APP定制开发">
	<meta name="description" content="会搜股份【股票代码：837521】荣誉出品，会搜云专注做APP定制全套
   	解决方案，将原生App + H5网页版+ 微信小程序（Hot！）一并打通！
           用心服务于 电商大商家/中大型企业客户…">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css"> 
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/app_detail.css">
@endsection
@section('content')
	<div>
        <div class="banner">
            <img src="{{ config('app.source_url') }}{{ $data[$id-1]['xcx-bg'] }}" class="app-bg">
            <div class="app-intro">
                <p class="app-t1">{{ $data[$id-1]['name'] }}</p>
                <p class="app-t2">{{ $data[$id-1]['desc'] }}</p>
            </div>
        </div>
        <div class="app-content">
            <h2 class="app-tips">应用介绍</h2>
            <p class="app-desc">{{ $data[$id-1]['content'] }}</p>
            <div class="app-slider">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="{{ config('app.source_url') }}{{ $data[$id-1]['xcxImgs'] }}"/>
                        </div>
                    </div>
                </div> 
                <!-- 如果需要分页器 -->
                <div id="swiper-pagination" class="swiper-pagination"></div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection


@section('js')
	<script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script>  
	<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/JavaScript">
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            loop:true,
            // autoplay: 3000,
            slideShadows : true
        });
    </script> 
@endsection
