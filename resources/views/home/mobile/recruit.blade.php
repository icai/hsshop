@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/corporateCulture.css">  
@endsection



@section('content')
	<div class="content">
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
				<li class="menu-list-wrap menu-now">
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
					<a href="/home/index/contactUs">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon05.png" alt="" />
							<div class="menu-list-word">
								<h3>联系我们</h3>
								<p>帮助您解答问题</p>
							</div>
						</div>
					</a>
				</li>	
			</ul>
		</div>
		<!-- 菜单end -->
		<div class="content-wrap">
			<div class="content-wrap-title">
				<h3>招贤纳士</h3>
			</div>
			<div class="job-top">
				我们拒绝高端、大气、上档次、只会说不会干<br /><br />
				我们欢迎低调、奢华、有内涵，为梦想而努力.不问出处，不问长相<br /><br />
				你可以没有硕果累累的文本&魅惑的三围，但你须有一颗积极进取的心<br /><br />
				这里并不安逸，内心脆弱者请绕道而行<br /><br />
				如果你依然决心尝试，来吧，我们会给你最最最热情的拥抱<br /><br />
				这里，充满活力与激情感受年轻的力量<br /><br />
				这里，拥有平等的晋升空间<br /><br />
				加入会搜，一起收获友爱、尊重和温暖的事业伙伴
			</div>
		</div>
		<div class="content-wrap content-bg">
			<div class="job-title"> 
				<h4>客服</h4>
				<span>工作性质：全职</span><span>工作地点：杭州</span>
			</div>
			<div class="job-service"> 
				<h5>职位描述：</h5>
				<p>1.处理业务人员提交的申请单并在苹果系统上进行注册；</p>
				<p>2.APP图标发与业务人员进行确认及反馈修改方案;</p>
				<p>3.将确认好的全套图标制作成素材包并上传生成至APP生成系统</p>
				<p>4.对已生成的APP进行数据填充，确保每个APP数据完整</p>
				<p>5.对已填充好的APP进行截图，用于应用市场发布</p>
				<p>6.在苹果电脑上发布APP上传至苹果市场待审核，审核通过后更新ios地址，用于二维码扫描下载</p>
				<p>7.处理业务人员反馈的bug及建议</p>
				<p>8.接听来电及沟通协调和其他配合工作等</p>
				<h5>任职资历：</h5>
				<p>1.高中及以上学历，年龄20—28岁之间；</p>
				<p>2.性格开朗，积极向上，普通话标准，具备较强的沟通能力及耐力；</p>
				<p>3.喜欢从事客服工作,&nbsp;从事过电话客服工作经验者优先；</p>
				<p>4.男女不限；</p>
			</div>
			<div class="job-title"> 
				<h4 class="job-border">IOS开发</h4>
				<span>工作性质：全职</span><span>工作地点：杭州</span>
			</div>
			<div class="job-service"> 
				<h5>职位描述：</h5>
				<p>1.基于IOS平台进行移动应用程序的系统分析与设计工作，承担核心功能代码编写，开发与维护系统公用核心模块；</p>
				<p>2.参与移动应用软件框架的研究，设计和实现、关键技术验证和选型等工作；</p>
				<p>3.移动应用软件性能优化，技术难题攻关，解决各类潜在技术风险，保证系统的安全、稳定、快速运行；</p>
				<p>4.带领并指导开发工程师、程序员进行代码开发/单元测试等工作，参与移动规范制订、技术文档编写。</p>
				<h5>任职资历：</h5>
				<p>1.1年及以上手机应用实际开发经验，1年以上IOS开发经验，1年以上C/C+/Java开发经验，具备敏捷编程思想，精通IOS及Mysql，有高并发，大数据量经验优先；</p>
				<p>2.精通Objective-C、Mac OS X、Xcode， 精通IOS SDK中的UI、网络、数据库、XML/JSON解析等开发技巧；</p>
				<p>3.有多个完整的IOS项目经验，至少参加过一个完整的商业级手机应用或游戏开发项目；</p>
				<p>4.熟悉各种主流手机特性，深刻理解手机客户端软件及服务端开发特点；</p>
				<p>5.精通常用软件架构模式，熟悉各种算法与数据结构，多线程，网络编程（Socket、http/web service）等；</p>
				<p>6.具有很强的学习能力和对新技术的追求精神，能够独立承担项目开发工作，具有比较强的责任心；严谨、主动，良好的团队意识和沟通能力 ；</p>
			</div>
		</div>
	</div> 

@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection

@section('js')
	<script type="text/javascript">
		$(function(){
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