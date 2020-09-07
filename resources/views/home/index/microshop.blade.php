@extends('home.base.head')
@section('head.css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/microshop.css"/>
@endsection
@section('content')
	@include('home.base.slider')
    <div class="main_content">
        <!--分页一-->
        <div class='shop_bg'>
			<div class="beijing">
				<a class="order-btn" href="/home/index/reserve?type=5">立即预约</a>
			</div>
		</div>
		<!--会搜微商城四大亮点-->
		<div class="shop_bright">
			<h4 class='shop_title'>会搜微商城四大亮点</h4>
			<ul class='clearfix'>
				<li class='clearfix'>
					<div class='shop_bright_div_a'>
						<p>五分钟搭建微商城平台</p>
						<p class='shop_p'>快速授权会搜云系统，轻松搭建微商城平台</p>
					</div>
					<div class='shop_bright_div_b'>
						<img src="{{ config('app.source_url') }}home/image/shop_01@2x.png" alt="">
						<img src="{{ config('app.source_url') }}home/image/shop_001@2x.png" alt="">
					</div>
				</li>
				<li class='clearfix shop_li'>
					<div class='shop_bright_div_a'>
						<p>强大的自定义系统</p>
						<p class='shop_p'>多种模块自由搭配，满足不同商家个性化需求</p>
					</div>
					<div class='shop_bright_div_b'>
						<img src="{{ config('app.source_url') }}home/image/shop_02@2x.png" alt="">
						<img src="{{ config('app.source_url') }}home/image/shop_002@2x.png" alt="">
					</div>
				</li>
				<li class='clearfix'>
					<div class='shop_bright_div_a'>
						<p>主流营销工具系统</p>
						<p class='shop_p'>多种营销工具灵活组合应用，积累老客户，引流新客户，</p>
						<p class='shop_p'>实现店铺高活跃、高客单、高复购</p>
					</div>
					<div class='shop_bright_div_b'>
						<img src="{{ config('app.source_url') }}home/image/shop_03@2x.png" alt="">
						<img src="{{ config('app.source_url') }}home/image/shop_003@2x.png" alt="">
					</div>
				</li>
				<li class='clearfix shop_li'>
					<div class='shop_bright_div_a'>
						<p>裂变分销体系</p>
						<p class='shop_p'>符合规定的3级分销，打通线上线下，分销变现；顾客</p>
						<p class='shop_p'>购买，推客就能获得佣金</p>
					</div>
					<div class='shop_bright_div_b'>
						<img src="{{ config('app.source_url') }}home/image/shop_04@2x.png" alt="">
						<img src="{{ config('app.source_url') }}home/image/shop_004@2x.png" alt="">
					</div>
				</li>
			</ul>
		</div>
		<!--微商城九大核心系统-->
		<div class='shop_bgcolor'>
			<div class='shop_core shop_content'>
				<h4 class='shop_title'>微商城九大核心系统</h4>
				<ul class='clearfix'>
					<li>
						<img src="{{ config('app.source_url') }}home/image/shop_dpzx@2x.png" alt="">
						<p class='core_title'>店铺装修</p>
						<p class='shop_p'>模块多样自定义个性展现</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}home/image/shop_spgl@2x.png" alt="">
						<p class='core_title'>商品管理</p>
						<p class='shop_p'>一键导入海量商品，自由设计商品详情</p>
					</li>
					<li class='shop_li'>
						<img src="{{ config('app.source_url') }}home/image/shop_ddgl@2x.png" alt="">
						<p class='core_title'>订单管理</p>
						<p class='shop_p'>即时提醒订单情况，快速打单快速发货</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}home/image/shop_zcgl@2x.png" alt="">
						<p class='core_title'>资产管理</p>
						<p class='shop_p'>每日收入、账单明细一目了然</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}home/image/shop_ghgl@2x.png" alt="">
						<p class='core_title'>客户管理</p>
						<p class='shop_p'>会员、积分系统激励用户消费，</p>
						<p class='shop_p'>多渠道发展客户</p>
					</li>
					<li class='shop_li'>
						<img src="{{ config('app.source_url') }}home/image/shop_fxgl@2x.png" alt="">
						<p class='core_title'>分销管理</p>
						<p class='shop_p'>打通线上线下，拓展销售渠道</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}home/image/shop_sjgl@2x.png" alt="">
						<p class='core_title'>数据管理</p>
						<p class='shop_p'>多维度分析店铺经营，驱动店铺发展</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}home/image/shop_yygl@2x.png" alt="">
						<p class='core_title'>运营管理</p>
						<p class='shop_p'>多种营销工具灵活组合应用，扩大销量</p>
					</li>
					<li class='shop_li'>
						<img src="{{ config('app.source_url') }}home/image/shop_dqd@2x.png" alt="">
						<p class='core_title'>多渠道支持</p>
						<p class='shop_p'>一个后台，小程序、公众号多渠道支持</p>
					</li>
				</ul>
			</div>
		</div>
		<!--多种模块自由搭配-->
		<div class="shop_content shop_module">
			<h4 class='shop_title'>多种模块自由搭配</h4>
			<ul class='clearfix'>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_shousuo@2x.png" alt="">
					<p>商品搜索</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_liebiao@2x.png" alt="">
					<p>商品列表</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_fenzu@2x.png" alt="">
					<p>商品分组</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_guanggao@2x.png" alt="">
					<p>图片广告</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_mofang@2x.png" alt="">
					<p>魔方</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_biaoti@2x.png" alt="">
					<p>标题</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_wenben@2x.png" alt="">
					<p>文本导航</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_daohang@2x.png" alt="">
					<p>图片导航</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_zidingyi@2x.png" alt="">
					<p>自定义模块</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_lianjie@2x.png" alt="">
					<p>关联链接</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_shiping@2x.png" alt="">
					<p>视频</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_gif@2x.png" alt="">
					<p>GIF</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_yuyin@2x.png" alt="">
					<p>语音</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_gonggao@2x.png" alt="">
					<p>公告</p>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/shop_gengduo@2x.png" alt="">
					<p>更多</p>
				</li>
			</ul>
		</div>
		<!--多种营销工具，个性化组合促销-->
		<div class='shop_bgcolor'>
			<div class='shop_content shop_group'>
				<h4 class="shop_title">多种营销工具，个性化组合促销</h4>
				<ul class='clearfix'>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_pt@2x.png" alt="">
						<div>
							<p>多人拼团</p>
							<span class='shop_p'>邀请朋友一起拼团购买</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_xlj@2x.png" alt="">
						<div>
							<p>享立减</p>
							<span class='shop_p'>分享链接减钱的新玩法</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_jz@2x.png" alt="">
						<div>
							<p>集赞</p>
							<span class='shop_p'>邀请好友点赞享受优惠</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_dzp@2x.png" alt="">
						<div>
							<p>大转盘</p>
							<span class='shop_p'>转盘式抽奖玩法</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_ms@2x.png" alt="">
						<div>
							<p>秒杀</p>
							<span class='shop_p'>快速抢购引导客户消费</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_yhj@2x.png" alt="">
						<div>
							<p>优惠券</p>
							<span class='shop_p'>发放店铺优惠券</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_zjd@2x.png" alt="">
						<div>
							<p>砸金蛋</p>
							<span class='shop_p'>金蛋砸出赢礼品</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_ggk@2x.png" alt="">
						<div>
							<p>刮刮卡</p>
							<span class='shop_p'>刮开卡片进行抽奖</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_hyk@2x.png" alt="">
						<div>
							<p>会员卡</p>
							<span class='shop_p'>给客户发放会员卡</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_jf@2x.png" alt="">
						<div>
							<p>积分</p>
							<span class='shop_p'>积分奖励消耗制度</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_qd@2x.png" alt="">
						<div>
							<p>签到</p>
							<span class='shop_p'>每日领取积分或奖励</span>
						</div>
					</li>
					<li class='clearfix'>
						<img src="{{ config('app.source_url') }}home/image/shop_chb@2x.png" alt="">
						<div>
							<p>拆红包</p>
							<span class='shop_p'>火爆引流神器</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<!--店铺数据分析系统-->
		<div class="shop_analyze shop_content">
			<h4 class="shop_title">店铺数据分析系统</h4>
			<p class='shop_analyze_tiitle'>全维度精准分析店铺经营，让商户更好的进行用户运营</p>
			<ul class='clearfix'>
				<li class='clearfix'>
					<div class='shop_analyze_div_a'>
						<p>五分钟搭建微商城平台</p>
						<p class='shop_p'>访客频次、来源分析，跟踪浏览</p>
						<p class='shop_p'>特性，了解客户习惯，轻松搭建微商城平台</p>
					</div>
					<div class='shop_analyze_div_b shop_analyze_img_1'></div>
				</li>
				<li class='clearfix shop_li'>
					<div class='shop_analyze_div_a'>
						<p>客群分析</p>
						<p class='shop_p'>访客频次、客户画像、客户构成、区域分布</p>
						<p class='shop_p'>一目了然，跟踪浏览</p>
					</div>
					<div class='shop_analyze_div_b shop_analyze_img_2'></div>
				</li>
				<li class='clearfix'>
					<div class='shop_analyze_div_a'>
						<p>交易分析</p>
						<p class='shop_p'>付款转化率、订单涨幅情况清晰</p>
						<p class='shop_p'>明了，交易数据实时发布</p>
					</div>
					<div class='shop_analyze_div_b shop_analyze_img_3'></div>
				</li>
				<li class='clearfix shop_li'>
					<div class='shop_analyze_div_a'>
						<p>商品分析</p>
						<p class='shop_p'>商品浏览量、访客数，商品销量涨</p>
						<p class='shop_p'>幅直观明了</p>
					</div>
					<div class='shop_analyze_div_b shop_analyze_img_4'></div>
				</li>
				<li class='clearfix'>
					<div class='shop_analyze_div_a'>
						<p>营销分析</p>
						<p class='shop_p'>营销活动口碑、活动效果反馈，</p>
						<p class='shop_p'>实时查看店铺营销工具使用情况</p>
					</div>
					<div class='shop_analyze_div_b shop_analyze_img_5'></div>
				</li>
				<li class='clearfix shop_li'>
					<div class='shop_analyze_div_a'>
						<p>转化分析</p>
						<p class='shop_p'>下单支付转化，实时掌握每天商品</p>
						<p class='shop_p'>转化情况</p>
					</div>
					<div class='shop_analyze_div_b shop_analyze_img_6'></div>
				</li>
			</ul>
		</div>
		<!--微商城客户案例-->
		<div class="xcx_client">
			<h4>小程序客户案例</h4>
			@if($caseList['data'])
			<ul class='clearfix xcx_client_ul'>
				@foreach($caseList['data'] as $val)
				<li>
					<a href="/home/index/caseDetails?id={{ $val['id'] }}">
						<div class='xcx_client_div_a'>
							<img src="{{ imgUrl() }}{{ $val['logo'] }}" alt="">
						</div>
						<div class='xcx_client_div_b'>
							<div>
								<img src="{{ imgUrl() }}{{ $val['code'] }}" alt="">
							</div>
							<p>{{ $val['name'] }}</p>
						</div>
					</a>
				</li>
				@endforeach
			</ul>
			<a class='getAll' href="/home/index/3/shop">
				查看更多 →
			</a>
			@endif
		</div>
		<!--哪些企业在用微商城-->
		<div class='shop_firm shop_content'>
			<h4 class="shop_title">哪些企业在用微商城</h4>
			<ul class='clearfix'>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo01@2x.png" alt="">
					<span>1688</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo02@2x.png" alt="">
					<span>联想生活</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo03@2x.png" alt="">
					<span>海尔商城</span>
				</li>
				<li class='shop_li'>
					<img src="{{ config('app.source_url') }}home/image/logo04@2x.png" alt="">
					<span>格力电器</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo05@2x.png" alt="">
					<span>VIVO手机</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo06@2x.png" alt="">
					<span>拼多多</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo07@2x.png" alt="">
					<span>蘑菇街</span>
				</li>
				<li class='shop_li'>
					<img src="{{ config('app.source_url') }}home/image/logo08@2x.png" alt="">
					<span>京东购物</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo09@2x.png" alt="">
					<span>贝贝拼团</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo10@2x.png" alt="">
					<span>去哪儿网</span>
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/logo11@2x.png" alt="">
					<span>携程旅游</span>
				</li>
				<li class='shop_li'>
					<img src="{{ config('app.source_url') }}home/image/logo12@2x.png" alt="">
					<span>美团生活</span>
				</li>
			</ul>
		</div>
		<!--预约-->
		<div class="tri-sub">
			<h4>立即预约体验火爆的小程序</h4>
			<a href="/home/index/reserve?type=5">立即预约</a>
		</div>
    </div>
@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/microshop.js" type="text/javascript" charset="utf-8"></script>  
@endsection