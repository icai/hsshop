@extends('home.base.head')
@section('head.css')	
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/sitemap.css"/>
@endsection
@section('content')
    @include('home.base.slider')
    <div class="main_content"> 
    	<div class="map-banner">
    		<p class="banner-p font18">网站地图</p>
    		<p class="banner-p font8">SITEMAP</p>
    	</div>   
    	<div class="z-posi">
			<img src="{{ config('app.source_url') }}home/image/maposi.png"/>
			<span class="zimap1">当前位置：首页></span>
			<span class="zimap2">网站地图</span>   
			<div class="z-xml">
				<a class="xml" href="sitemap.xml">XML地图</a>        					
			</div>		
    	</div>  		
        <div class="mapdiv">  
        	<img src="{{ config('app.source_url') }}home/image/cpzx.png"/>
        	<p class="z-tilp">会搜云</p>      	
	       	<ul class="map-ul">
	       		<li><a href="/">会搜云首页</a></li>
	       		<li><a href="/home/index/shop">行业案例</a></li>
	       		<li class="last"><a href="/home/index/about">关于会搜云</a></li>
	       		<li><a href="/">产品服务</a></li>
	       		<li class="last"><a href="/home/index/information">会搜云资讯</a></li>
	       	</ul>
        </div>
        <div class="mapdiv">
        	<img src="{{ config('app.source_url') }}home/image/help.png"/>
       		<p class="z-tilp">产品中心</p>
       		<ul class="map-ul">
	       		<li><a href="/">APP定制</a></li>
	       		<li><a href="/home/index/distribution">分销系统</a></li>
	       		<li><a href="/home/index/microMarketing">微营销总裁班</a></li>
	       		<li><a href="/home/index/microshop">微信商城</a></li>
	       		<li><a href="/home/index/applet">微信小程序</a></li>
	       	</ul>
        </div>
        <div class="mapdiv">
        	<img src="{{ config('app.source_url') }}home/image/hsy.png"/>
       		<p class="z-tilp">帮助中心</p>
       		<ul class="map-ul">
	       		<li><a href="/home/index/information/secCategory/23">账号管理</a></li>
	       		<li><a href="/home/index/information/secCategory/26">商品管理</a></li>
	       		<li><a href="/home/index/information/secCategory/31">营销管理</a></li>
	       		<li><a href="/home/index/information/secCategory/68">店铺管理</a></li>
	       		<li><a href="/home/index/information/secCategory/45">微营销技能</a></li>
	       		<li><a href="/home/index/information/secCategory/43">基础营销</a></li>
	       		<li><a href="/home/index/information/secCategory/73">付费营销</a></li>
	       		<li><a href="/home/index/information/secCategory/47">新闻科技</a></li>
	       		<li><a href="/home/index/information/secCategory/49">线下活动</a></li>
	       		<li><a href="/home/index/information/secCategory/51">产品动态</a></li>
	       		<li><a href="/home/index/information/secCategory/70">学习答疑</a></li>
	       	</ul>
        </div>
    </div>  
@endsection
@section('foot.js') 
@endsection