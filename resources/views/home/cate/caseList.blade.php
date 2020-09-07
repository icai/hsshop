@extends('home.mobile.default._layouts')

@section('title',$title) 
@section('css')
	<meta name="keywords" content="会搜云微商城,电商APP定制,小程序开发,微商城开发,杭州APP开发,商城APP定制开发">
	<meta name="description" content="会搜股份【股票代码：837521】荣誉出品，会搜云专注做APP定制全套
   	解决方案，将原生App + H5网页版+ 微信小程序（Hot！）一并打通！
		   用心服务于 电商大商家/中大型企业客户…">
	<meta name="360-site-verification" content="632c84a5f2cd5f61cb5d2da9e60c1db3" />
	<meta name="sogou_site_verification" content="aesuziJnOz"/>
	<meta name="baidu-site-verification" content="uyrxkhHHL2" />
	<!-- <meta name="shenma-site-verification" content="b0bd9779cb1724b8b9356af1e2faa1f0_1534130819">  -->
	<meta name="msvalidate.01" content="3ADCCB8541EF9859CA763B8421B3B43F" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css"> 
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/caseList.css"/>
@endsection
@section('content')
	@include('home.mobile.default.phone')
	<div class="content">
		<div class="banner">
		  <img src="{{ config('app.source_url') }}home/image/case_banner.jpg" alt="">
		</div>

		<div class="case-main">
			<!-- 小程序 -->
			<div class="case-main-program">
				<div class="case-main-program-top">
					<img src="{{ config('app.source_url') }}home/image/case_caseshow.jpg" alt="">
				</div>
				<div class="case-main-program-bottom clearfix">
						<ul class="case-main-program-bottom-ul">
							<li>
								<img src="{{ config('app.source_url') }}home/image/case_imag.png" alt="">
								<span>店铺名称</span>
							</li>
							<li>
								<img src="{{ config('app.source_url') }}home/image/case_imag.png" alt="">
								<span>店铺名称</span>
							</li>
						</ul>
				</div>
			</div>
			<!-- 微商城 -->
			<div class="case-main-micromall">
				<div class="case-main-micromall-top">
					<img src="{{ config('app.source_url') }}home/image/case_micromall.jpg" alt="">
				</div>
				<div class="case-main-micromall-bottom clearfix">
						<ul class="case-main-micromall-bottom-ul">
							<li>
								<img src="{{ config('app.source_url') }}home/image/case_imag.png" alt="">
								<span>店铺名称</span>
							</li>
							<li>
								<img src="{{ config('app.source_url') }}home/image/case_imag.png" alt="">
								<span>店铺名称</span>
							</li>
						</ul>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection


@section('js')
    <script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script> 
    <!-- 当前页面js -->
    <script  src="{{ config('app.source_url') }}home/js/caseList.js"  type="text/javascript"  charset="utf-8"></script> 
@endsection
