@extends('home.mobile.default._layouts')

@section('title',$title) 
@section('css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css"> 
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/appDownload.css">
@endsection
@section('content')
   <div class="banner">
	   <img src="{{ config('app.source_url') }}mobile/images/down-banner11.jpg">
   </div>
   <div class="tool-box">
	   <h2 class="year">2018</h2>
	   <p class="tool-title">好用的小程序微商城开店工具</p>
	   <div class="swiper-container">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/down-banner1.png" alt="会搜云"/>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/down-banner2.png" alt="会搜云"/>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/down-banner3.png" alt="会搜云"/>
				</div>
			</div>
			<div id="swiper-pagination" class="swiper-pagination"></div>
		</div>
   </div>
   <div class="service-box">
	   <p class="service-title">功能多样 一站式服务</p>
	   <div class="icon-container">
		   <div class="icon-item">
			   <img src="{{ config('app.source_url') }}mobile/images/service-icon1.png">
			   <p class="service-t1">商品管理</p>
			   <p class="service-t2">商品组合、定价方法</p>
		   </div>
		   <div class="icon-item">
			   <img src="{{ config('app.source_url') }}mobile/images/service-icon2.png">
			   <p class="service-t1">订单管理</p>
			   <p class="service-t2">推动经济效益和客户满意度</p>
		   </div>
		   <div class="icon-item">
			   <img src="{{ config('app.source_url') }}mobile/images/service-icon3.png">
			   <p class="service-t1">数据统计</p>
			   <p class="service-t2">精准快速的查找与分类</p>
		   </div>
		   <div class="icon-item">
			   <img src="{{ config('app.source_url') }}mobile/images/service-icon4.png">
			   <p class="service-t1">营销工具</p>
			   <p class="service-t2">多样化的促销活动</p>
		   </div>
	   </div>
		<div class="footer-ercode">
			<img src="{{ imgUrl() }}{{ $qrcodeUrl }}" class="er-code-img">
			<p class="down-tips">长按识别二维码进行下载</p>
		</div>
		<div class="phone-icon-box">
			<div class="phone-item">
				<i class="phone-icon icon1"></i>
				<span>iPhone</span>
			</div>
			<div class="phone-item">
				<i class="phone-icon icon2"></i>
				<span>Android</span>
			</div>
		</div>
   </div>
   <div class="down-link-box">
	   <img src="{{ config('app.source_url') }}mobile/images/down-logo.png" class="logo-footer">
	   <span class="footer-tips">会搜云商家版</span>
	   <a href="/home/index/downLoadDetail" class="down-link">立即下载</a>
   </div>
	
@endsection

@section('js')
	<script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script> 
	<script>
		$(function(){
			var mySwiper = new Swiper('.tool-box .swiper-container', {
				autoplay: 3000, //可选选项，自动滑动
			});
		})
		
	</script>
@endsection