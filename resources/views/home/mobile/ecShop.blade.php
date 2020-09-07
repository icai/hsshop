@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<meta name="description" content="会搜股份荣誉出品，会搜云专注做微信商城全套解决方案，提供微信商城哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
	<meta name="keywords" content="微信商城">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/EcShop.css">
@endsection

@section('content')
    <div class="content">
        <section>
            <div class="app_header">
				<a href="/home/index/reserve?type=5">
					立即预约
				</a>
            </div>
			<div class='es_trait'>
				<h4 class='es_title'>会搜微商城四大亮点</h4>
				<ul>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_001@2x.png" alt="">
						<div>
							<p>五分钟搭建微商城平台</p>
							<p class='es_p_txt'>快速授权会搜云系统，轻松搭建微商城平台</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_002@2x.png" alt="">
						<div>
							<p>强大的自定义系统</p>
							<p class='es_p_txt'>多种模块自由搭配，满足不同商家个性化需求</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_003@2x.png" alt="">
						<div>
							<p>主流营销工具系统</p>
							<p class='es_p_txt'>多种营销工具灵活组合应用，积累老客户，引流新客户，实现店铺高活跃、高客单、高复购</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_004@2x.png" alt="">
						<div>
							<p>裂变分销体系</p>
							<p class='es_p_txt'>符合规定的3级分销，打通线上线下，分销变现；顾客购买，推客就能获得佣金</p>
						</div>
					</li>
				</ul>
			</div>
			<div class='es_core'>
				<h4 class='es_title'>微商城九大核心系统</h4>
				<ul>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_dpzx@2x.png" alt="">
						<p>店铺装修</p>
						<div>
							<p class='es_p_txt'>模块多样</p>
							<p class='es_p_txt'>自定义个性展现</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_spgl@2x.png" alt="">
						<p>商品管理</p>
						<div>
							<p class='es_p_txt'>一键导入海量商品</p>
							<p class='es_p_txt'>自由设计商品详情</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_ddgl@2x.png" alt="">
						<p>订单管理</p>
						<div>
							<p class='es_p_txt'>即时提醒订单情况</p>
							<p class='es_p_txt'>快速打单快速发货</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_zcgl@2x.png" alt="">
						<p>资产管理</p>
						<div>
							<p class='es_p_txt'>每日收入、账单明细</p>
							<p class='es_p_txt'>一目了然</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_ghgl@2x.png" alt="">
						<p>客户管理</p>
						<div>
							<p class='es_p_txt'>会员、积分系统激励用户</p>
							<p class='es_p_txt'>消费，多渠道发展客户</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_fxgl@2x.png" alt="">
						<p>分销管理</p>
						<div>
							<p class='es_p_txt'>打通线上线下，拓展</p>
							<p class='es_p_txt'>销售渠道</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_sjgl@2x.png" alt="">
						<p>数据管理</p>
						<div>
							<p class='es_p_txt'>多维度分析店铺经</p>
							<p class='es_p_txt'>营，驱动店铺发展</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_yygl@2x.png" alt="">
						<p>运营管理</p>
						<div>
							<p class='es_p_txt'>多种营销工具灵活</p>
							<p class='es_p_txt'>组合应用，扩大销量</p>
						</div>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/shop_dqd@2x.png" alt="">
						<p>多渠道支持</p>
						<div>
							<p class='es_p_txt'>一个后台，小程序、</p>
							<p class='es_p_txt'>公众号多渠道支持</p>
						</div>
					</li>
				</ul>
			</div>
			<div class='es_module'>
				<h4 class='es_title'>多种模块自由搭配</h4>
				<div class='swiper-container'>
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_shousuo@2x.png" alt="">
									<p>商品搜索</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_liebiao@2x.png" alt="">
									<p>商品列表</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_fenzu@2x.png" alt="">
									<p>商品分组</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_guanggao@2x.png" alt="">
									<p>图片广告</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_biaoti@2x.png" alt="">
									<p>魔方</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_shousuo@2x.png" alt="">
									<p>标题</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_wenben@2x.png" alt="">
									<p>文本导航</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_daohang@2x.png" alt="">
									<p>图片导航</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_zidingyi@2x.png" alt="">
									<p>自定义模块</p>
								</li>
							</ul>
						</div>
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_lianjie@2x.png" alt="">
									<p>关联链接</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_shiping@2x.png" alt="">
									<p>视频</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_gif@2x.png" alt="">
									<p>GIF</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_yuyin@2x.png" alt="">
									<p>语音</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_gonggao@2x.png" alt="">
									<p>公告</p>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_gengduo@2x.png" alt="">
									<p>更多</p>
								</li>
							</ul>
						</div>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<div class='es_tool'>
				<h4>多种营销工具，个性化组合促销</h4>
				<div class='swiper-container'>
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_pt@2x.png" alt="">
									<p>多人拼团</p>
									<span>邀请朋友一起拼团购买</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_xlj@2x.png" alt="">
									<p>享立减</p>
									<span>分享链接减钱的新玩法</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_jz@2x.png" alt="">
									<p>集赞</p>
									<span>邀请好友点赞享受优惠</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_dzp@2x.png" alt="">
									<p>大转盘</p>
									<span>转盘式抽奖玩法</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_ms@2x.png" alt="">
									<p>秒杀</p>
									<span>快速抢购引导客户消费</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_yhj@2x.png" alt="">
									<p>优惠券</p>
									<span>发放店铺优惠券</span>
								</li>
							</ul>
						</div>
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_zjd@2x.png" alt="">
									<p>砸金蛋</p>
									<span>金蛋砸出赢礼品</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_ggk@2x.png" alt="">
									<p>刮刮卡</p>
									<span>刮开卡片进行抽奖</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_hyk@2x.png" alt="">
									<p>会员卡</p>
									<span>给客户发放会员卡</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_jf@2x.png" alt="">
									<p>积分</p>
									<span>积分奖励消耗制度</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_qd@2x.png" alt="">
									<p>签到</p>
									<span>每日领取积分或奖励</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/shop_chb@2x.png" alt="">
									<p>拆红包</p>
									<span>火爆引流神器</span>
								</li>
							</ul>
						</div>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<div class='es_data'>
				<h4 class='es_title'>店铺数据分析系统</h4>
				<p>全维度精准分析店铺经营，让商户更好的进行用户运营</p>
				<div class='swiper-container'>
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<ul>
								<li>
									<div>
										<p>客流分析</p>
										<p class="es_p_txt">访客频次、来源分析，跟踪浏览</p>
										<p class="es_p_txt">特性，了解客户习惯</p>
									</div>
									<div class="es_data_img_1"></div>
								</li>
								<li>
									<div>
										<p>客群分析</p>
										<p class="es_p_txt">客户画像、客户构成、区域分布</p>
										<p class="es_p_txt">一目了然</p>
									</div>
									<div class="es_data_img_2"></div>
								</li>
								<li>
									<div>
										<p>交易分析</p>
										<p class="es_p_txt">付款转化率、订单涨幅情况清晰</p>
										<p class="es_p_txt">明了，交易数据实时发布</p>
									</div>
									<div class="es_data_img_3"></div>
								</li>
							</ul>
						</div>
						<div class="swiper-slide">
							<ul>
								<li>
									<div>
										<p>商品分析</p>
										<p class="es_p_txt">商品浏览量、访客数，商品销量涨</p>
										<p class="es_p_txt">幅直观明了</p>
									</div>
									<div class="es_data_img_4"></div>
								</li>
								<li>
									<div>
										<p>营销分析</p>
										<p class="es_p_txt">营销活动口碑、活动效果反馈，</p>
										<p class="es_p_txt">实时查看店铺营销工具使用情况</p>
									</div>
									<div class="es_data_img_5"></div>
								</li>
								<li>
									<div>
										<p>转化分析</p>
										<p class="es_p_txt">下单支付转化，实时掌握每天商品</p>
										<p class="es_p_txt">转化情况</p>
									</div>
									<div class="es_data_img_6"></div>
								</li>
							</ul>
						</div>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<div class="es_clien">
				<h4 class='es_title'>微商城客户案例</h4>
				<ul>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_1@2x.png" alt="">
						<p>MUSE妙思服饰</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_2@2x.png" alt="">
						<p>筑品卫浴洁具商城</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_3@2x.png" alt="">
						<p>佰荷堂</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_4@2x.png" alt="">
						<p>大美前线</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_5@2x.png" alt="">
						<p>UBEST芭蕾</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_6@2x.png" alt="">
						<p>天虹电脑</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_7@2x.png" alt="">
						<p>有家家居商城</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_8@2x.png" alt="">
						<p>老表橙园</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/es_clien_9@2x.png" alt="">
						<p>膏药旗舰商城</p>
					</li>
				</ul>
			</div>
			<div class='es_firm'>
				<h4 class='es_title'>哪些企业在用微商城</h4>
				<div class='swiper-container'>
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo01@2x.png" alt="">
									<span>1688</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo02@2x.png" alt="">
									<span>联想生活</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo03@2x.png" alt="">
									<span>海尔商城</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo04@2x.png" alt="">
									<span>格力电器</span>
								</li>
							</ul>
						</div>
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo05@2x.png" alt="">
									<span>VIVO手机</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo06@2x.png" alt="">
									<span>拼多多</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo07@2x.png" alt="">
									<span>蘑菇街</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo08@2x.png" alt="">
									<span>京东购物</span>
								</li>
							</ul>
						</div>
						<div class="swiper-slide">
							<ul>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo09@2x.png" alt="">
									<span>贝贝拼团</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo10@2x.png" alt="">
									<span>去哪儿网</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo11@2x.png" alt="">
									<span>携程旅游</span>
								</li>
								<li>
									<img src="{{ config('app.source_url') }}mobile/images/logo12@2x.png" alt="">
									<span>美团生活</span>
								</li>
							</ul>
						</div>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<div class='small_appoin'>
				<a href="/home/index/reserve?type=5">
					<span>立即预约微商城开发</span>
					<p></p>
				</a>
			</div>
			<a class="order-footer" href="/home/index/reserve?type=5">我要预约</a>
        </section>
    </div>

@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection
@section('js')
	<script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script>

	<script>
        var mySwiper = new Swiper ('.swiper-container', {

            // 如果需要分页器
            pagination: '.swiper-pagination',

        })
	</script>
@endsection