@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css') 
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/corporateCulture.css">  
@endsection
	<style>
		.swiper-container1 {
			padding-bottom: 30px;
		}
		.honor{
			padding-bottom: 50px
		}
		.honor-item{
			margin-bottom: 10px
		}
		.swiper-slide {
			height: auto !important;
		}
		.honor-ul-1 {
			overflow: hidden;
			margin-top: 15px;
		}
		.honor-ul-1 li {
			float: left;
			width: 50%;
			margin-bottom: 20px;
			padding: 0 5px;
		}
		.honor-ul-1 img {
			width: 100%;
		}
		.honor-ul-2 {
			overflow: hidden;
		}
		.honor-ul-2 li {
			float: left;
			width: 33.33%;
			margin-bottom: 20px;
			padding: 0 5px;
		}
		.swiper-container2 {
			font-size: 0;
			height: 114px;
		}
		.swiper-container2 .swiper-slide .img {
				display: inline-block;
				padding: 0 2px;
				width: 25%;
		}
		.swiper-container2 .swiper-slide {
			height: auto
		}
		.swiper-button-prev,
		.swiper-container-rtl .swiper-button-next {
				background-image: url('../image/honor/arrow-left.png') !important;
		}

		.swiper-button-next,
		.swiper-container-rtl .swiper-button-prev {
				background-image: url('../image/honor/arrow-right.png') !important;
				right: 6px !important;
		}
	</style>
@section('content')
	<div class="content">
		<!-- 宣传图片 -->
		<div class="banner-wrap">
			<img src="{{ config('app.source_url') }}mobile/images/banner_aboutus11.jpg" />
		</div>
		<!--author 韩瑜 date 2018.7.10-->
		<!-- 菜单 -->
		<div class="menu"> 
			<ul class="menu-list">
				<li class="menu-list-wrap">
					<a href="/home/index/about">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon01.png" alt="" />
							<div class="menu-list-word">
								<h3>会搜简介</h3>
								<p>了解会搜云</p>
							</div>
						</div>
					</a>
				</li>
				<li class="menu-list-wrap">
					<a href="/home/index/growth">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon02.png" alt="" />
							<div class="menu-list-word">
								<h3>发展历程</h3>
								<p>会搜的一路走来</p>
							</div>
						</div>
					</a>
				</li>		
				<li class="menu-list-wrap">
					<a href="/home/index/culture">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon03.png" alt="" />
							<div class="menu-list-word">
								<h3>企业文化</h3>
								<p>爱与感恩的理念</p>
							</div>
						</div>
					</a>
				</li>		
				<li class="menu-list-wrap">
					<a href="/home/index/recruit">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon04.png" alt="" />
							<div class="menu-list-word">
								<h3>招贤纳士</h3>
								<p>伯乐寻找千里马</p>
							</div>
						</div>
					</a>
				</li>
				<li class="menu-list-wrap menu-now">
					<a href="/home/index/honor">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon05.png" alt="" />
							<div class="menu-list-word">
								<h3>资质荣誉</h3>
								<p>荣誉奖项及资质</p>
							</div>
						</div>
					</a>
				</li>	
			</ul>
		</div>
		<!-- 菜单end -->
		<!--内容-->
		<div class="content-wrap">
			<div class="content-wrap-title">
				<h3>资质荣誉</h3>
			</div>
			<div class="honor">
				<div class="swiper-container swiper-container1">
						<div class="swiper-wrapper">
								<div class="swiper-slide">
										<img src="{{ config('app.source_url') }}mobile/images/aboutUs/swiper1.png" alt="">
								</div>
								<div class="swiper-slide">
										<img src="{{ config('app.source_url') }}mobile/images/aboutUs/swiper2.png" alt="">
								</div>
								<div class="swiper-slide">
										<img src="{{ config('app.source_url') }}mobile/images/aboutUs/swiper3.png" alt="">
								</div>
								<div class="swiper-slide">
										<img src="{{ config('app.source_url') }}mobile/images/aboutUs/swiper4.png" alt="">
								</div>
						</div>
						<!-- 如果需要分页器 -->
						<div class="swiper-pagination"></div> 
				</div>
				<ul class="honor-ul-1">
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/row1.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/row2.png" alt="">
					</li>
				</ul>
				<ul class="honor-ul-2">
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-1.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-2.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-3.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-4.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-5.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-6.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-7.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-8.png" alt="">
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/aboutUs/three-row-9.png" alt="">
					</li>
				</ul>
				<!-- 底部幻灯片 -->
				<div class="swiper-container swiper-container2">
						<div class="swiper-wrapper">
								<div class="swiper-slide">
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-1.jpg" alt="">
										</div>
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-2.jpg" alt="">
										</div>
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-3.jpg" alt="">
										</div>
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-4.jpg" alt="">
										</div>
								</div>
								<div class="swiper-slide">
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-5.jpg" alt="">
										</div>
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-6.jpg" alt="">
										</div>
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-7.jpg" alt="">
										</div>
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-8.jpg" alt="">
										</div>
								</div>
								<div class="swiper-slide">
										<div class="img">
												<img src="{{ config('app.source_url') }}mobile/images/aboutUs/bottom-row-9.jpg" alt="">
										</div>
								</div>
						</div>
						<!-- 如果需要分页器 -->
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
						<!-- <div class="swiper-pagination"></div>  -->
				</div>
			</div>
		</div>
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection

@section('js')
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script>
	$(function () {
		new Swiper('.swiper-container1', {
			autoplay: 3000,
			// autoplay: false,
			loop: true,
			autoHeight: true,
			pagination: '.swiper-pagination'
		})
		new Swiper('.swiper-container2', {
				paginationClickable: true,
				nextButton: '.swiper-button-next',
				prevButton: '.swiper-button-prev',
				parallax: true,
				autoHeight: true,
				speed: 600,
		})
	})
</script>
@endsection