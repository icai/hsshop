@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<meta name="description" content="会搜股份荣誉出品，会搜云专注做微营销总裁班培训，提供微营销总裁班哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
	<meta name="keywords" content="微营销总裁班">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/zcb.css">  
@endsection

@section('content')
<div class="content">	
	<!--分页一-->
	<div class="zc-fir">
		<a href="/home/index/reserve?type=4">
			<p>立即预约</p>
		</a>
	</div>
	<!--分页2-->
	<div class="zc-wen">
		<div class="zc-wen-img">
			<img src="{{ config('app.source_url') }}mobile/images/zc-w.png" alt="" />
		</div>
		<div class="zc-item">
			<p>1</p>
			<h3>流量</h3>
			<h4>流量稀缺，用户流失快</h4>
		</div>
		<div class="zc-item">
			<p>2</p>
			<h3>营销</h3>
			<h4>营销推广难、成本不可控</h4>
		</div>
		<div class="zc-item">
			<p>3</p>
			<h3>转型</h3>
			<h4>不知道如何转型移动互联网</h4>
		</div>
		<div class="zc-item">
			<p>4</p>
			<h3>利润</h3>
			<h4>竞争激烈、利润越来越低</h4>
		</div>
		<div class="zc-item">
			<p>5</p>
			<h3>团队</h3>
			<h4>团队效率低、管理无头绪</h4>
		</div>
	</div>
	<!--分页2-->
	<div class="zc-study">
		<h2>通过微营销课程你将学到</h2>
		<h3><span>1</span>移动互联网运营战略布局和架构</h3>
		<p>趋势分析、认识运营、推广逻辑、<br />文案训练、落地实操</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img01.png" alt="" />
		<h3><span>2</span>APP的商业价值及盈利模式解析</h3>
		<p>“0”成本推广的8大秘诀、为APP引流<br />的渠道及策略</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img02.png" alt="" />
		<h3><span>3</span>公众平台的价值及运营技能</h3>
		<p>传统企业用公众平台获取客户流量<br />的20大秘诀</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img03.png" alt="" />
		<h3><span>4</span>微信小程序营销指南及落地实操</h3>
		<p>小程序8大商业模式解析及落地<br />推广的50大秘籍</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img04.png" alt="" />
		<h3><span>5</span>个人微信实操策略</h3>
		<p>个人微信+小程序+公众平台+社群<br />如何互联互推？</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img05.png" alt="" />
		<h3><span>6</span>社群营销解决方案</h3>
		<p>如何经营好一个群？如何设计社群产品？<br />如何设计营销活动？</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img06.png" alt="" />
		<h3><span>7</span>高绩效团队打造方法</h3>
		<p>传统企业如何创新商业模式？如何打造高绩效团队？<br />如何快速接入“互联网+”？</p>
		<img src="{{ config('app.source_url') }}mobile/images/zc-img07.png" alt="" />
	</div>
	<!--分页3-->
	<div class="zc-yuyue">
		<a href="/home/index/reserve?type=4">
			<p>立即预约微商城开发</p><span><img src="{{ config('app.source_url') }}mobile/images/zc-arr.png" alt="" /></span>
		</a>
	</div>
	<!--底部-->
	<a class="order-footer" href="/home/index/reserve?type=4">我要预约</a>
</div>
	
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection

@section('js')

@endsection