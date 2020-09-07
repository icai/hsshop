@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
	<!--当前页面css-->
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/corporateCulture.css">  
@endsection

@section('content')
	<div class="content">
		<!-- 宣传图片 -->
		<div class="banner-wrap">
			<img src="{{ config('app.source_url') }}mobile/images/banner_aboutus11.jpg" />
		</div>
		<!--author 韩瑜 date 2018.7.11-->
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
				<li class="menu-list-wrap menu-now">
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
				<li class="menu-list-wrap">
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
		<div class="content-wrap">
			<div class="content-wrap-title">
				<h3>企业文化</h3>
			</div>
			<!-- <p>爱、感恩、责任、坚持、创新</p> -->
			<p>杭州会搜科技股份有限公司以“爱”、“感恩”、“责任”、“创新”、“坚持”作为企业核心文化和价值观，始终贯彻“心怀感恩、尽心负责、开拓创新、坚持不懈”的核心企业理念，营造出一个温馨、积极向上的会搜大家庭。</p>
		</div>
		<div class="content-wrap content-bg">
			<div class="content-wrap-title">
				<h3>公司荣誉</h3>
			</div>
			<p>自公司成立以来，借力技术突破和创新发展，会搜科技及盈搜科技先后荣获二十多项著作权登记证书；</p>
			<p>2012年，荣获中华人民共和国信息产业部颁发的“软件企业认定证书”；同年，成功入选杭州市科技型初创企业“雏鹰计划”培育工程，深受省市领导重视与好评!</p>
			<p>2013年，公司取得浙江省通信管理局颁发的增值电信业务经营许可证书；
同年，成功入选杭州市高新技术企业，并荣获杭州江干区经济园‘创新示范企业’称号！连续两年获得‘优秀成长企业’荣誉称号、杭州市信息服务业发展专项资金扶持企业！
</p>
			<p>2016年，荣获江干区2016年度现代产业成长企业。</p>
		</div>
		<div class="content-wrap">
			<div class="content-wrap-title">
				<h3>团队风采</h3>
			</div>
			<p>杭州会搜科技股份有限公司注重企业文化的建设，为了丰富公司职工业余生活,营造健康向上的企业文化氛围,公司定期组织具有团队建设意义的各项活动，包括健身运动、户外体育、野外拓展、K歌、聚餐、摄影、公益、电影等，全力打造一支青春活力的卓越团队！</p>
			<div class="ab-swiper">				
				<div class="swiper-container">
				    <div class="swiper-wrapper">
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture1.jpg"/></div>
						<div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture2.jpg"/></div>
						<div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture3.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture5.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture6.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture7.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture8.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture9.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture10.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture11.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/culture12.jpg"/></div>
				    </div>
				</div>
				 <!-- 如果需要分页器 -->
				 <div class="swiper-pagination"></div>
			</div>
		</div>
		<div class="content-wrap">
			<div class="content-wrap-title">
				<h3>技术团队</h3>
			</div>
			<!-- <p>杭州会搜科技股份有限公司注重企业文化的建设，为了丰富公司职工业余生活,营造健康向上的企业文化氛围,公司定期组织具有团队建设意义的各项活动，包括健身运动、户外体育、野外拓展、K歌、聚餐、摄影、公益、电影等，全力打造一支青春活力的卓越团队！</p> -->
			<div class="ab-swiper1">				
				<div class="swiper-container">
				    <div class="swiper-wrapper">
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/technology_01.jpg"/></div>
						<div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/technology_02.jpg"/></div>
						<div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/technology_03.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/technology_04.jpg"/></div>
				        
				    </div>
				</div>
				 <!-- 如果需要分页器 -->
				 <div class="swiper-pagination1"></div>
			</div>
		</div>
		
	</div> 

@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection

@section('js')
	<script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script> 
	<script type="text/javascript">
	$(function(){
		var mySwiper = new Swiper ('.ab-swiper .swiper-container', {
		    direction: 'horizontal',
		    autoplay: 1000, //可选选项，自动滑动
			speed:1000,      //滑动速度
			loop : true,    //环路
	    
		    // 如果需要分页器
		    pagination: '.ab-swiper .swiper-pagination',
	  })
	  var mySwiper1 = new Swiper ('.ab-swiper1 .swiper-container', {
		    direction: 'horizontal',
		    autoplay: 1000, //可选选项，自动滑动
			speed:1000,      //滑动速度
			loop : true,    //环路
	    
		    // 如果需要分页器
		    pagination: '.ab-swiper1 .swiper-pagination1',
	  })
	    //横向导航
	    var now_left = $('.menu-now').offset().left;
	    var now_right = now_left + $('.menu-now').width();
	    var now_width = $(window).width();
	    var now_hide = now_right - now_width
	 	if(now_right > now_width){
	 		$(".menu-list").scrollLeft(now_hide); 
	 	}
	})
	</script>
@endsection