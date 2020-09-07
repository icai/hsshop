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
		<!--author 韩瑜 date 2018.7.10-->
		<!-- 菜单 -->
		<div class="menu"> 
			<ul class="menu-list">
				<li class="menu-list-wrap menu-now">
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
		<!--内容-->
		<div class="content-wrap">
			<!-- 第一部分 -->
			<div class="content-wrap-title">
				<h3>会搜简介</h3>
			</div>
			<p>杭州会搜科技股份有限公司 (简称：会搜科技股份），坐落于杭州西子湖畔东方电子商务园内，是一家拥有未来思维和创新精神的创新型技术研发企业。</p>
			<p>自2010年11月成立以来，已经成为一个拥有全面的人才管理结构、规范的市场运营机制、完善的企业管理制度的专业移动互联网软件技术服务公司，拥有近两百人的技术及业务团队，已累计用户数十万。</p>
            <p>2016年05月，公司完成股改，成功在新三板挂牌（股票代码：837521）。2017年07月，获得来自苏州高新创业投资集团融联管理有限公司2000万元A轮股权融资，公司估值已达10个亿。同时，据已有数据分析，公司营业额和市场份额在同类公司中处于领先地位，展现出了自身的竞争优势。</p>
			<p>同时，据已有数据分析，公司营业额和市场份额在同类公司中处于领先地位，展现出了自身的竞争优势。</p>
			<div class="content-img">
				<div>
					<div class="count-msg"></div>
					<p class="about-t1">技术开发团队</p>
				</div>
				<div>
					<div class="count-msg count-msg2"></div>
					<p class="about-t1">年软件开发经验</p>
				</div>
				<div>
					<div class="count-msg count-msg3"></div>
					<p class="about-t1">企业客户认可</p>
				</div>
			</div>
		</div>
		<!-- <div class="content-wrap content-bg"> -->
			<!-- 第二部分 -->
			<!-- <div class="content-wrap-title">
				<h3>核心优势</h3>
			</div>
			<h4>1、专业团队</h4>	
			<p>产品策划：会搜设有专业的产品策划部门，通过项目经理理解客户的商业需求后，策划人员进行调研、策划方案，确保为客户提供最优秀、独特、充分且经济的开发方案。能够在众多的需求中准确识别关键需求，深入挖掘对产品有价值的隐性需求，保持产品在市场上的竞争差异性。</p>
			<p>视觉设计：会搜拥有多名优秀的美工设计师，在视觉设计方面，能够非常明确的传达这个APP的主旨，达到色彩、图案、形态、布局等的选择必须与APP的功能、情感相呼应，用户是多样的，需求是多种的，流行是会随着时间变化的，所以熟悉精通多种风格是会搜对设计师的基本要求。</p>
			<p>功能开发: 我们相信，个性化、多样化的APP功能才是帮助客户实现商业目的的有力渠道，而会搜具有丰富编程开发经验的开发人员，保障了您独特的业务需求均能满足。</p>
			<p>品质监控: 会搜并非单纯的APP开发公司，后期服务也是我们的优势之一。会搜建立了完整的产品品质监控流程，从调研、策划、开发、运营、维护。每一步都会对客户进行1对1辅导。</p>
			<h4>2、专业流程</h4>
			<p> 为了开发更好的优质产品，保证服务质量，会搜构建了清晰、可视、可持续改进的研发流程。研发流程涉及市场分析、产品定位、用户分析、系统设计、程序开发、性能优化等，在每个环节我们都秉承着打造高品质移动电商解决方案的开发理念。专业的研发流程为你提供定制化解决方案实现你期望的成果。</p>
			<h4>3、知名企业成功案例：</h4>
			<p> 会搜拥有众多成功案例，与多家知名企业合作打造高品质的互联网产品，比如：菜鸟物流APP、绿建家APP、中国锁具APP等。我们按照不同行业、不同类型客户的需求特点，总结、提炼各类优秀项目案例，形成了会搜科技特有的移动电商解决方案。专业化流程、优质的服务、丰富的经验以及对创意品质的追求是客户选择我们的理由。</p>		
		</div> -->
		<div class="content-wrap">
			<!-- 第三部分 -->
			<div class="content-wrap-title">
				<h3>经营理念</h3>
			</div>
			<p>会搜股份将继续坚持以“客户第一、服务为本”的经营理念，以技术创新和用户体验为支撑的发展源泉，打造核心竞争优势；通过不断吸纳优秀人才、提升企业管理效能，真正为用户创造更多的价值。</p>
            <p>会搜股份自主研发核心团队，紧握时代脉搏，快速研发出适应时代潮流需求的移动互联网软件产品，创造广泛的社会效益，并产生直接经济效应。这个具有同一目标，团结、勤奋、富有爱心、责任感以及强烈的使命感的团队，在长期实践中积累了丰富经验，为公司的发展提供了有力的支撑和保障。</p>
		</div>
		<div class="content-wrap content-bg">
			<div class="content-wrap-title">
				<h3>公司环境</h3>
			</div>
			<div class="ab-swiper">				
				<div class="swiper-container">
				    <div class="swiper-wrapper">
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}mobile/images/about_bottom03.jpg"/></div>
				        <div class="swiper-slide"><img src="{{ config('app.source_url') }}mobile/images/about_bottom04.jpg"/></div>
				    </div>
				</div>
				 <!-- 如果需要分页器 -->
				 <div class="swiper-pagination"></div>
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
	})
	</script>
@endsection