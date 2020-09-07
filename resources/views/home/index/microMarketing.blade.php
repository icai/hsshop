@extends('home.base.head')
@section('head.css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/weiyingxiao.css"/>
@endsection
@section('content')
    @include('home.base.slider')
    <div class="main_content">
        <!--分页1-->
        <div class='zc_bg'>
			<div class="xiao-fir">
				{{--<img src="{{ config('app.source_url') }}home/image/inbannerd.jpg" alt="会搜云"/>--}}
				<a class="order-btn" href="/home/index/reserve?type=4">立即预约</a>
			</div>
		</div>
        <!--分页2-->
        <div class="zc-qus">
        	<div class="zc-qus-img">
        		<img src="{{ config('app.source_url') }}home/image/zc-w.png" alt="" />
        	</div>
        	<div class="qus-1 qus">
        		<p>1</p>
        		<h3>流量</h3>
        		<h4>流量稀缺，用户流失快</h4>
        	</div>
        	<div class="qus-2 qus">
        		<p>2</p>
        		<h3>营销</h3>
        		<h4>营销推广难、成本不可控</h4>
        	</div>
        	<div class="qus-3 qus">
        		<p>3</p>
        		<h3>转型</h3>
        		<h4>不知道如何转型移动互联网</h4>
        	</div>
        	<div class="qus-4 qus qus-l">
        		<p>4</p>
        		<h3>团队</h3>
        		<h4>竞争激烈、利润越来越低</h4>
        	</div>
        	<div class="qus-5 qus qus-l">
        		<p>5</p>
        		<h3>流量</h3>
        		<h4>团队效率低、管理无头绪</h4>
        	</div>
        </div>
        <!--分页3-->
        <div class="zc-study">
        	<div class="zc-title">
        		<p>通过微营销课程你将学到</p>
        	</div>
        	<div class="zc-item-wrap">
	        	<div class="zc-item">
	        		<img src="{{ config('app.source_url') }}home/image/zc-img01.png" alt="" />
	        		<div class="study-title">
	        			<h3><span>1</span>移动互联网运营战略布局和架构</h3>
	        			<p>趋势分析、认识运营、推广逻辑、文案训练、落地实操</p>
	        		</div>
	        	</div>
	        	<div class="zc-item2">
	        		<div class="study-title2">
	        			<h3><span>2</span>APP的商业价值及盈利模式解析</h3>
	        			<p>“0”成本推广的8大秘诀、为APP引流的渠道及策略</p>
	        		</div>
	        		<img src="{{ config('app.source_url') }}home/image/zc-img02.png" alt="" />
	        	</div>
	        	<div class="zc-item">
	        		<img src="{{ config('app.source_url') }}home/image/zc-img03.png" alt="" />
	        		<div class="study-title">
	        			<h3><span>3</span>公众平台的价值及运营技能</h3>
	        			<p>传统企业用公众平台获取客户流量的20大秘诀</p>
	        		</div>
	        	</div>
	        	<div class="zc-item2">
	        		<div class="study-title2">
	        			<h3><span>4</span>微信小程序营销指南及落地实操</h3>
	        			<p>小程序8大商业模式解析及落地推广的50大秘籍</p>
	        		</div>
	        		<img src="{{ config('app.source_url') }}home/image/zc-img04.png" alt="" />       		
	        	</div>
	        	<div class="zc-item">
	        		<img src="{{ config('app.source_url') }}home/image/zc-img05.png" alt="" />
	        		<div class="study-title">
	        			<h3><span>5</span>个人微信实操策略</h3>
	        			<p>个人微信+小程序+公众平台+社群如何互联互推？</p>
	        		</div>
	        	</div>
	        	<div class="zc-item2">
	        		<div class="study-title2">
	        			<h3><span>6</span>社群营销解决方案</h3>
	        			<p>如何经营好一个群？如何设计社群产品？如何设计营销活动？</p>
	        		</div>
	        		<img src="{{ config('app.source_url') }}home/image/zc-img06.png" alt="" />
	        	</div>
	        	<div class="zc-item">
	        		<img src="{{ config('app.source_url') }}home/image/zc-img07.png" alt="" />
	        		<div class="study-title">
	        			<h3><span>7</span>高绩效团队打造方法</h3>
	        			<p>传统企业如何创新商业模式？如何打造高绩效团队？<br />如何快速接入“互联网+”？</p>
	        		</div>
	        	</div>
	        </div>
        </div>
        <!--分页4-->
        <div class="zc-yy">
        	<p>预约移动互联网实战总裁班，名额有限</p>
        	<a href="/home/index/reserve?type=4">
        		<button>立即预约</button>
        	</a>
        </div>
    </div>
@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/weiyingxiao.js" type="text/javascript" charset="utf-8"></script> 
@endsection