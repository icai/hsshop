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
            <form action="/home/category/case/list/0/{{ $type }}">
                <input type="text" name="keyword" class="search-input" placeholder="搜索店铺" value="{{ request('keyword') }}">
                <button class="search-commodity">确定</button>
            </form>
        </div>
        <div class="custom-tag-list-menu-block clear">
            <div class="category-left">
                <ul class="custom-tag-list-side-menu-left js-side-menu ">
                    <!-- active-left -->
                    <li @if($pid == 0) class="active-left" @endif>
                        <a class="js-menu-tag" href="/home/category/list?type={{ $type }}"><span>全部分类</span></a>
                    </li >
                    @if($firtList)
                    @foreach($firtList as $val)
                    <li @if($pid && $val['id'] == $pid) class="active-left" @endif>
                        <a class="js-menu-tag" href="/home/category/list?type={{ $type }}&pid={{ $val['id'] }}"><span>{{ $val['title'] or '' }}</span></a>
                    </li>
                    @endforeach
                    @endif
                </ul>
            </div>
            <div class="category-right">
                <ul class="custom-tag-list-side-menu-right">
                    <!-- active-right -->
                    @if($list)
                    @foreach($list as $key =>$val)
                    <li class="product-class"> 
                        <ul class="product-ul clear">
                            @if($pid == 0)
                            <span class="product-ul-title">{{ $firtList[$key]['title'] }}</span>
                            @endif
                            @foreach($val as $v)
                            <a href="/home/category/case/list/{{ $v['id'] }}/{{ $type }}">
                            <li class="detail-class">
                                <img class="detail-class-img" src="{{ $v['icon'] }}" alt="">
                                <span>{{ $v['title'] }}({{ $v['caseCount'] or 0 }})</span>
                            </li>
                            </a>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                    @else
                    二级分类下暂无案例
                    @endif
                </ul>
            </div>
        </div>
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection


@section('js')
    <!-- 当前页面js -->
    <script  src="{{ config('app.source_url') }}home/js/list.js"  type="text/javascript"  charset="utf-8"></script> 
    <script type="text/javascript"  charset="utf-8">
      var list = {!! json_encode($list) !!}
    </script>
@endsection
