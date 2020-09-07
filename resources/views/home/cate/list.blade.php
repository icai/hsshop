@extends('home.mobile.default._layouts')

@section('title',$title) 
@section('css')
	<meta name="keywords" content="会搜云微商城,电商APP定制,小程序开发,微商城开发,杭州APP开发,商城APP定制开发">
	<meta name="description" content="会搜股份【股票代码：837521】荣誉出品，会搜云专注做APP定制全套
   	解决方案，将原生App + H5网页版+ 微信小程序（Hot！）一并打通！
		   用心服务于 电商大商家/中大型企业客户…">
	<meta name="360-site-verification" content="632c84a5f2cd5f61cb5d2da9e60c1db3" />
	<meta name="sogou_site_verification" content="aesuziJnOz"/>
	<meta name="baidu-site-verification" content="uyrxkhHHL2" />
	<!-- <meta name="shenma-site-verification" content="b0bd9779cb1724b8b9356af1e2faa1f0_1534130819">  -->
	<meta name="msvalidate.01" content="3ADCCB8541EF9859CA763B8421B3B43F" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css"> 
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/list.css"/>
@endsection
@section('content')
	@include('home.mobile.default.phone')
	<div class="content">
		<div class="search-case">
            <form action="">
                <input type="text"  class="search-input" placeholder="搜索店铺">
            </form>
        </div>
        <div class="custom-tag-list-menu-block clearfix">
            <div class="category-left">
                <ul class="custom-tag-list-side-menu-left js-side-menu ">
                    <!-- active-left -->
                    <li >
                        <a class="js-menu-tag" href="javascript:;"><span>全部分类(10)</span></a>
                    </li >
                    <li>
                        <a class="js-menu-tag" href="javascript:;"><span>百货食品(10)</span></a>
                    </li>
                    <li >
                        <a class="js-menu-tag" href="javascript:;"><span>服装鞋包(10)</span></a>
                    </li>
                    <li >
                        <a class="js-menu-tag" href="javascript:;"><span>家具建材(10)</span></a>
                    </li>
                    <li >
                        <a class="js-menu-tag" href="javascript:;"><span>美妆饰品(10)</span></a>
                    </li>
                </ul>
            </div>
            <div class="category-right">
                <ul class="custom-tag-list-side-menu-right">
                <!-- active-right -->
                    <li> 
                         <a class="js-menu-right " href="javascript:;"><span>超市百货(10)</span></a>
                    </li>
                    <li> 
                         <a class="js-menu-right" href="javascript:;"><span>鱼肉果蔬(10)</span></a>
                    </li>
                    <li> 
                         <a class="js-menu-right" href="javascript:;"><span>餐饮器具(10)</span></a>
                    </li>
                    <li> 
                         <a class="js-menu-right" href="javascript:;"><span>零食坚果(10)</span></a>
                    </li>
                </ul>
            </div>
        </div>
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection


@section('js')
    <script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script> 
    <!-- 当前页面js -->
    <script  src="{{ config('app.source_url') }}home/js/list.js"  type="text/javascript"  charset="utf-8"></script> 
@endsection
