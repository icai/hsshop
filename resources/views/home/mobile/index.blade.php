@extends('home.mobile.default._layouts')

@section('title',$title)
@section('css')
<meta name="description" content="会搜股份荣誉出品，会搜云专注做APP定制全套解决方案，将原生App+H5网页版+微信小程序一并打通。提供会搜云新零售系统、人工智能名片制作、电子名片在线制作、微信获客神器、精准获客系统、智能广告获客、微信商城分销系统、微信小程序ai智能名片、小程序商城如何运营、人工智能名片哪个公司好。">
<meta name="keywords" content="会搜云新零售系统,智能名片制作,电子名片在线制作,人工智能名片,微信获客神器,精准获客系统,微信商城分销系统,微信小程序ai智能名片,智能广告获客,小程序商城如何运营,人工智能名片哪个公司好">
<meta name="360-site-verification" content="632c84a5f2cd5f61cb5d2da9e60c1db3" />
<meta name="sogou_site_verification" content="aesuziJnOz" />
<meta name="baidu-site-verification" content="uyrxkhHHL2" />
<!-- <meta name="shenma-site-verification" content="b0bd9779cb1724b8b9356af1e2faa1f0_1534130819">  -->
<meta name="msvalidate.01" content="3ADCCB8541EF9859CA763B8421B3B43F" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/index.css">
@endsection
@section('content')
@include('home.mobile.default.phone')
<div class="content">
	<div class="content-banner">
		<div class="swiper-container" id="swiper_APP">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/banner05.png" />
					<div class="slider-content">
						<h2 class="slider-t1">会搜云新零售系统</h2>
						<p class="slider-t2">打造微信智能销售系统全面提升企业品牌形象</p>
						<a href="https://ai.huisou.cn" class="order-btn">了解详情</a>
					</div>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/mobile-home-banner-2.png" />
					<div class="slider-content">
						<h2 class="slider-t1">如何转型新零售</h2>
						<p class="slider-t2">线上线下场景相结合，多渠道获客活客</p>
						<a href="https://ai.huisou.cn" class="order-btn">了解详情</a>
					</div>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/mobile-home-banner-3.png" />
					<div class="slider-content">
						<h2 class="slider-t1">多种拓客方式，助推销售</h2>
						<p class="slider-t2">助理销售多渠道、多方式、多维度裂变获客，<br>给企业带来更多价值</p>
						<a href="https://ai.huisou.cn" class="order-btn">了解详情</a>
					</div>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/banner01.png" />
					<div class="slider-content">
						<h2 class="slider-t1">小程序定制</h2>
						<p class="slider-t2">小程序定制先行者，助力商户畅享十亿流量红利</p>
						<a href="/home/index/reserve?type=3" class="order-btn">了解详情</a>
					</div>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/banner02.png" />
					<div class="slider-content">
						<h2 class="slider-t1">APP定制</h2>
						<p class="slider-t2">资深APP定制团队，助力传统企业拥抱移动互联网</p>
						<a href="/home/index/reserve?type=2" class="order-btn">了解详情</a>
					</div>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/banner03.png" />
					<div class="slider-content">
						<h2 class="slider-t1">微商城开发</h2>
						<p class="slider-t2">微商城开发引领者，打造一站式移动电商解决方案</p>
						<a href="/home/index/reserve?type=5" class="order-btn">立即预约</a>
					</div>
				</div>
				<div class="swiper-slide">
					<img src="{{ config('app.source_url') }}mobile/images/banner04.png" />
					<div class="slider-content">
						<h2 class="slider-t1">移动互联网实战总裁班</h2>
						<p class="slider-t2">
							明星导师教学，全套落地实操方法，从零学习
							<br>
							移动互联网营销技巧
						</p>
						<a href="/home/index/reserve?type=4" class="order-btn">立即预约</a>
					</div>
				</div>
			</div>
			<!-- 如果需要分页器 -->
			<div id="swiper-pagination" class="swiper-pagination"></div>
		</div>
	</div>
	<div class="content-wraper g-gray clearflow">
		<h2 class="content-t1">会搜云产品</h2>
		<p class="content-t2">全渠道全场景的SaaS产品，助力企业转型布局新零售</p>
		<!-- <p class="content-t2 content-t2-t">助你开启移动互联网营销新时代</p> -->
		<div class="cando-box">
			<a href="https://ai.huisou.cn" class="cando-item">
				<img src="{{ config('app.source_url') }}mobile/images/service5.png" class="item-img">
				<div class="item-desc">会搜云<br>新零售系统</div>
			</a>
			<a href="/home/index/customization" class="cando-item">
				<img src="{{ config('app.source_url') }}mobile/images/service1.png" class="item-img">
				<div class="item-desc">APP定制</div>
			</a>
			<a href="/home/index/applet" class="cando-item">
				<img src="{{ config('app.source_url') }}mobile/images/service2.png" class="item-img">
				<div class="item-desc">小程序定制</div>
			</a>
			<a href="/home/index/microshop" class="cando-item">
				<img src="{{ config('app.source_url') }}mobile/images/service3.png" class="item-img">
				<div class="item-desc">微商城开发</div>
			</a>
			<a href="/home/index/microMarketing" class="cando-item">
				<img src="{{ config('app.source_url') }}mobile/images/service4.png" class="item-img">
				<div class="item-desc">微营销总裁班</div>
			</a>
		</div>
	</div>
	<div class="content-wraper clearflow">
		<h2 class="content-t1">会搜云经典案例展示</h2>
		<p class="content-t2">服务千万中小企业，助力传统行业转型，他们正在使用</p>
		<p class="content-t2 content-t2-t">会搜云改变自己的零售方式</p>
		<div class="example-box">
			<div class="example-item">
				<a href="/home/index/1/shop" class="example-link">
					<h2 class="example-t1">会搜云新零售系统</h2>
					<p class="example-t2">直播+电商模式</p>
					<p class="example-t3">助力商家构建私域流量及转型新零售</p>
				</a>
			</div>
			<div class="example-item">
				<a href="/home/index/2/shop" class="example-link">
					<h2 class="example-t1">APP开发</h2>
					<p class="example-t2">APP便捷了每个人的生活，APP开发让每个企业</p>
					<p class="example-t3">都开始了移动信息化进程</p>
				</a>
			</div>
			<div class="example-item">
				<a href="/home/index/3/shop" class="example-link">
					<h2 class="example-t1">小程序开发</h2>
					<p class="example-t2">小程序定制先行者，助力商户畅享十亿流量红利</p>
					<!-- <p class="example-t3">都开始了移动信息化进程</p> -->
				</a>
			</div>
			<div class="example-item">
				<a href="/home/index/4/shop" class="example-link">
					<h2 class="example-t1">微商城开发</h2>
					<p class="example-t2">微商城开发引领者，打造一站式的移动电商解决方案</p>
					<!-- <p class="example-t3">都开始了移动信息化进程</p> -->
				</a>
			</div>
		</div>
	</div>
	<div class="content-wraper g-gray clearflow">
		<h2 class="content-t1">丰富多样的营销应用</h2>
		<p class="content-t2">经营渠道、促销折扣、促销工具、会员卡券</p>
		<p class="content-t2 content-t2-t">助你玩转微信营销</p>
		<ul class="application-box">
			<li>
				<div class="application-item">
					<img src="{{ config('app.source_url') }}mobile/images/app1.png" class="app-img">
					<span class="app-desc">会搜云新零售系统</span>
					<i class="arrow-icon"></i>
				</div>
				<ul class="none">
					<li>
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">直播+电商</p>
							<p class="sub-t2">裂变引流，直播带货，帮助商家构建私域流量</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">新零售</p>
							<p class="sub-t2">构建智慧零售，线上线下一体式经营</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">企业管理</p>
							<p class="sub-t2">sales千里眼，boss千里眼，助力企业管理</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">智能拓客</p>
							<p class="sub-t2">销售多渠道、多方式、多维度裂变获客</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">社交名片</p>
							<p class="sub-t2">多维度社交名片，打造专属商业形象</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">分销系统</p>
							<p class="sub-t2">分享裂变，分销扩展销售渠道</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">VR</p>
							<p class="sub-t2">720°全景展示，打造全新视觉体验</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="https://ai.huisou.cn" class="app-sub-link">
							<p class="sub-t1">多样营销工具</p>
							<p class="sub-t2">拼团、秒杀、优惠券、表单、文件夹等多种营销工具灵活应用</p>
						</a>
					</li>
				</ul>
			</li>
			<li>
				<div class="application-item">
					<img src="{{ config('app.source_url') }}mobile/images/app1.png" class="app-img">
					<span class="app-desc">经营渠道</span>
					<i class="arrow-icon"></i>
				</div>
				<ul class="none">
					<li>
						<a href="/home/index/manageChannel/detail/1" class="app-sub-link">
							<p class="sub-t1">小程序</p>
							<p class="sub-t2">一键生成微信小程序</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/manageChannel/detail/2" class="app-sub-link">
							<p class="sub-t1">公众号</p>
							<p class="sub-t2">链接公众号，玩转微信生态圈</p>
						</a>
					</li>
				</ul>
			</li>
			<li>
				<div class="application-item">
					<img src="{{ config('app.source_url') }}mobile/images/app2.png" class="app-img">
					<span class="app-desc">促销折扣</span>
					<i class="arrow-icon"></i>
				</div>
				<ul class="none">
					<li>
						<a href="/home/index/salesDiscount/detail/1" class="app-sub-link">
							<p class="sub-t1">优惠券</p>
							<p class="sub-t2">向客户发放店铺优惠劵</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/salesDiscount/detail/2" class="app-sub-link">
							<p class="sub-t1">秒杀</p>
							<p class="sub-t2">快速抢购引导客户更多消费</p>
						</a>
					</li>
				</ul>
			</li>
			<li>
				<div class="application-item">
					<img src="{{ config('app.source_url') }}mobile/images/app3.png" class="app-img">
					<span class="app-desc">促销工具</span>
					<i class="arrow-icon"></i>
				</div>
				<ul class="none">
					<li>
						<a href="/home/index/salesTools/detail/1" class="app-sub-link">
							<p class="sub-t1">享立减</p>
							<p class="sub-t2">用户点击分享链接可减钱的新玩法</p>
						</a>
					</li>
					{{--<li class="app-sub-li">--}}
					{{--<a href="/home/index/salesTools/detail/2" class="app-sub-link">--}}
					{{--<p class="sub-t1">集赞</p>--}}
					{{--<p class="sub-t2">邀请好友点赞可享受优惠的玩法</p>--}}
					{{--</a>--}}
					{{--</li>--}}
					<li>
						<a href="/home/index/salesTools/detail/3" class="app-sub-link">
							<p class="sub-t1">多人拼团</p>
							<p class="sub-t2">引导客户邀请朋友一起拼团购买</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/salesTools/detail/4" class="app-sub-link">
							<p class="sub-t1">大转盘</p>
							<p class="sub-t2">常见的转盘式抽奖玩法</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/salesTools/detail/5" class="app-sub-link">
							<p class="sub-t1">微社区</p>
							<p class="sub-t2">打造人气移动社区,增加客户流量</p>
						</a>
					</li>
					<li>
						<a href="/home/index/salesTools/detail/6" class="app-sub-link">
							<p class="sub-t1">砸金蛋</p>
							<p class="sub-t2">好蛋砸出来，礼品不间断</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/salesTools/detail/7" class="app-sub-link">
							<p class="sub-t1">签到</p>
							<p class="sub-t2">每日签到领取积分或奖励</p>
						</a>
					</li>
				</ul>
			</li>
			<li>
				<div class="application-item">
					<img src="{{ config('app.source_url') }}mobile/images/app4.png" class="app-img">
					<span class="app-desc">会员卡券</span>
					<i class="arrow-icon"></i>
				</div>
				<ul class="none">
					<li>
						<a href="/home/index/memberTicket/detail/1" class="app-sub-link">
							<p class="sub-t1">会员卡</p>
							<p class="sub-t2">设置并给客户发放会员卡</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/memberTicket/detail/2" class="app-sub-link">
							<p class="sub-t1">积分</p>
							<p class="sub-t2">完善的积分奖励消耗制度</p>
						</a>
					</li>
					<li>
						<a href="/home/index/memberTicket/detail/3" class="app-sub-link">
							<p class="sub-t1">充值</p>
							<p class="sub-t2">开通会员充值功能</p>
						</a>
					</li>
				</ul>
			</li>
			<li>
				<div class="application-item">
					<img src="{{ config('app.source_url') }}mobile/images/app5.png" class="app-img">
					<span class="app-desc">推广工具</span>
					<i class="arrow-icon"></i>
				</div>
				<ul class="none">
					<li>
						<a href="/home/index/extension/detail/1" class="app-sub-link">
							<p class="sub-t1">消息提醒</p>
							<p class="sub-t2">向客户发布微信消息提醒</p>
						</a>
					</li>
					<li class="app-sub-li">
						<a href="/home/index/extension/detail/2" class="app-sub-link">
							<p class="sub-t1">消息模板</p>
							<p class="sub-t2">设置消息提醒模板</p>
						</a>
					</li>
					<li>
						<a href="/home/index/extension/detail/3" class="app-sub-link">
							<p class="sub-t1">投票</p>
							<p class="sub-t2">向客户发起投票活动</p>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	<div class="content-wraper clearflow service-wraper">
		<h2 class="content-t1">我们的服务流程</h2>
		<p class="content-t2">完善的定制流程，层层把关，确保交给客户</p>
		<p class="content-t2 content-t2-t">一份满意的答卷</p>
		<div class="service-slider">
			<div class="swiper-container">
				<div class="swiper-wrapper">
					<div class="swiper-slide">
						<div class="service-content">
							<div class="step-icon">1</div>
							<p class="sign-icon">
								<img src="{{ config('app.source_url') }}mobile/images/step1.png">
							</p>
							<p class="sign-step">商务代表，签订合同</p>
							<div class="sign-desc">
								<p>1、需求确认</p>
								<p>2、功能清单、功能报价</p>
								<p>3、项目时间进度计划</p>
							</div>
						</div>
					</div>
					<div class="swiper-slide">
						<div class="service-content">
							<div class="step-icon">2</div>
							<p class="sign-icon">
								<img src="{{ config('app.source_url') }}mobile/images/step2.png">
							</p>
							<p class="sign-step">需求沟通，产品原型</p>
							<div class="sign-desc">
								<p>1、设计功能逻辑图</p>
								<p>2、设计原型图</p>
								<p>3、客户确认原型图</p>
							</div>
						</div>
					</div>
					<div class="swiper-slide">
						<div class="service-content">
							<div class="step-icon">3</div>
							<p class="sign-icon">
								<img src="{{ config('app.source_url') }}mobile/images/step3.png">
							</p>
							<p class="sign-step">UI视觉设计</p>
							<div class="sign-desc">
								<p>1、UI设计视觉页面</p>
								<p>2、与客户确认UI设计图</p>
							</div>
						</div>
					</div>
					<div class="swiper-slide">
						<div class="service-content">
							<div class="step-icon">4</div>
							<p class="sign-icon">
								<img src="{{ config('app.source_url') }}mobile/images/step4.png">
							</p>
							<p class="sign-step">技术开发</p>
							<div class="sign-desc">
								<p>1、根据需求原型图及UI图完成前端、</p>
								<p style="text-indent: 1.5em;">后台开发</p>
								<p>2、前端与后台配合对接开发调试接口</p>
							</div>
						</div>
					</div>
					<div class="swiper-slide">
						<div class="service-content">
							<div class="step-icon">5</div>
							<p class="sign-icon">
								<img src="{{ config('app.source_url') }}mobile/images/step5.png">
							</p>
							<p class="sign-step">项目测试</p>
							<div class="sign-desc">
								<p>1、测试对项目进行整体测试</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;（功能逻辑、页面交互、数据校验）</p>
								<p>2、测试完成、提交验收</p>
							</div>
						</div>
					</div>
					<div class="swiper-slide">
						<div class="service-content">
							<div class="step-icon">6</div>
							<p class="sign-icon">
								<img src="{{ config('app.source_url') }}mobile/images/step6.png">
							</p>
							<p class="sign-step">验收上线，项目交付</p>
							<div class="sign-desc">
								<p>1、客户端对APP进行打包</p>
								<p style="text-indent: 1.5em;">上架各大应用市场</p>
								<p>2、交付给客户进行使用</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- 如果需要分页器 -->
			<div class="swiper-pagination service-swiper-pagination"></div>
		</div>
	</div>
	<div class="content-wraper clearflow about-wraper">
		<h2 class="content-t1">关于会搜云</h2>
		<p class="content-t2">会搜股份于2016年5月30日在新三板成功挂牌上市，</p>
		<p class="content-t2 content-t2-t">股票代码：837521</p>
		<div class="about-count">
			<div>
				<div class="count-msg"></div>
				<p class="about-t1">技术开发团队</p>
			</div>
			<div>
				<div class="count-msg count-msg2"></div>
				<p class="about-t1">定制、开发经验</p>
			</div>
			<div>
				<div class="count-msg count-msg3"></div>
				<p class="about-t1">企业客户认可</p>
			</div>
		</div>
		<div class="huisou-desc">
			<div class="huisou-item">
				<p class="huisou-t1">原生开发</p>
				<p class="huisou-t2">所有APP和小程序全部原生开发，不套模板</p>
			</div>
			<div class="huisou-item">
				<p class="huisou-t1">完美适配</p>
				<p class="huisou-t2">APP系统后台可PC端、移动端（Android、IOS）全适应</p>
			</div>
			<div class="huisou-item">
				<p class="huisou-t1">快速响应</p>
				<p class="huisou-t2">100人的技术团队及时处理后期各种问题</p>
			</div>
			<div class="huisou-item">
				<p class="huisou-t1">豪礼赠送</p>
				<p class="huisou-t2">确定开发业务后可赠送微营销课程</p>
			</div>
		</div>
	</div>
	<div class="content-wraper clearflow">
		<h2 class="content-t1">会搜云资讯动态</h2>
		<p class="content-t2">移动互联网时代，时刻把握行业最前沿的资讯！</p>
		@if($newestList)
		<div class="dynamic-container">
			@foreach($newestList as $val)
			<div class="dynamic-item">
				@if(isset($val['source']) && $val['source'])
				<img src="{{ config('app.source_url') }}{{ $val['source'][0]['l_path'] }}">
				@endif
				<div class="dynamic-desc">
					<a href="/home/index/newsDetail/{{ $val['id'] }}/news" class="desc-t1">{{ $val['title'] }}</a>
					<p class="desc-t2">{{ date('m-d',strtotime($val['created_at'])) }}</p>
				</div>
			</div>
			@endforeach
		</div>
		@endif
		<p class="news-more">
			<a href="/home/index/news" class="get-more">查看更多</a>
		</p>
	</div>
	<div class="register-wraper">
		<p class="register-tips">立即注册即可体验火爆的小程序</p>
		<a href="/auth/register" class="register-btn">立即注册</a>
	</div>
	<a class="order-footer" href="/home/index/reserve?type=3">我要预约</a>
</div>
@endsection
@section('footer')
@include('home.mobile.default.footer')
@endsection


@section('js')
<script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}mobile/js/index.js"></script>
@endsection