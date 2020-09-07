@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css') 
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
				<li class="menu-list-wrap menu-now">
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
			<div class="content-wrap-title">
				<h3>发展目标</h3>
			</div>
			<p>
				会搜科技股份将继续坚持以“客户第一、服务为本”的经营理念，以技术创新和用户体验为支撑的发展源泉。通过提升企业管理效能，不断吸纳技术水平高、事业心强的开发人员，用强大的技术实力打造了保证公司健康发展的坚实基础。
			</p>
			<p>
				公司的发展目标是依托现有的平台及资源，整合产业价值链，平稳布局于移动互联网，并依靠严谨深厚的技术积累，开展更多的业务领域，成为一家立足软件开发，跨领域运作、多元化发展、在行业处于领先地位的软件开发公司。通过为各行业用户提供优质高效的专业服务，不断扩大品牌影响力，最终实现服务人民、造福社会的宏伟目标！
			</p>
			
		</div>
		<div class="content-bg content-wrap">
			<div class="content-wrap-title content-bottom ">
				<h3>会搜历程</h3>
			</div>
			<div class="content-year">
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth01.png" alt="" />
					<span>2010年</span>
					<p>2010年11月，杭州会搜科技股份有限公司成立。</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth02.png" alt="" />
					<span>2012年</span>
					<p>
						杭州会搜科技股份推出会搜网综合性平台，自主研发的来福网全面上线，迎来了用户市场的广泛好评。</br>会搜荣获中华人民共和国信息产业部颁发的“软件企业认定证书”；同年，成功入选杭州市科技型初创企业“雏鹰计划”培育工程，深受省市领导重视与好评!
					</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth01.png" alt="" />
					<span>2013年</span>
					<p>
						3月，杭州会搜科技股份推出APP开发服务，风靡江浙；<br />7月，基于微信而研发的阿凡提微商系统成功上线，帮助大量客户推广公司的产品并提升服务质量，让客户真正地受益匪浅。获得‘优秀成长企业’荣誉称号，成为杭州市信息服务业发展专项资金扶持企业。
					</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth02.png" alt="" />
					<span>2014年</span>
					<p>继2013之后，又一次获得‘优秀成长企业’荣誉称号、成为杭州市信息服务业发展专项资金扶持企业。</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth01.png" alt="" />
					<span>2015年</span>
					<p>会搜喜获“杭州市最具创新活力微小企业”荣誉称号，为促进全市中小企业转型升级、创新发展作出了贡献。</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth02.png" alt="" />
					<span>2016年</span>
					<p>5月会搜登录新三板挂牌（股票代码：837521）</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth01.png" alt="" />
					<span>2017年</span>
					<p>会搜云微商城系统上线，提供从PC端到移动端再到微信端的多端合一的线上线下解决方案。</p>
					<p>2017年07月，获得来自苏州高新创业投资集团融联管理有限公司2000万元A轮股权融资。</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth02.png" alt="" />
					<span>2018年</span>
					<p>荣获浙江省“高新技术企业”证书。</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth01.png" alt="" />
					<span>2019年</span>
					<p>会搜云新零售系统发布，打造微信智能销售系统，全面提升企业品牌输出。</p>
				</div>
				<div class="content-year-item">
					<img src="{{ config('app.source_url') }}mobile/images/growth02.png" alt="" />
					<span>······</span>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection

@section('js')

@endsection