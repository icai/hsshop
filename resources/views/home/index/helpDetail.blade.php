@extends('home.base.head')
@section('head.css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/helpDetail.css"/>
@endsection
@section('content')
    @include('home.base.slider')
	<!--帮助中心 搜索-->
	<div class="help_top">
		<div class="help_top_content clearfix">
			<div class="help_top_left fl">
				帮助中心
			</div>
			<div class="help_top_right fr">
				<div class='clearfix'>
					<input class='right_inp fl' placeholder="请输入需要搜索的关键字" type="text">
					<botton class="right_btn fl"><span></span></botton>
				</div>
			</div>
		</div>
	</div>
	<!--帮助中心 内容导航-->
	<div class="help_nav">
		<div class="help_nav_content">
			<a href="{{ config('app.url') }}home/index/helps">帮助首页</a>
			<a class='nav_active'  href="{{ config('app.url') }}home/index/helpList">常见问题</a>
			<a href="{{ config('app.url') }}home/index/selfServe">自助服务</a>
		</div>
	</div>
	<!--帮助中心 常见问题列表-->
	<div class='help_list clearfix'>
		<div class='fl help_list_left'>
			@if(isset($typeData['nav']) && $typeData['nav'])
			<ul class='list_left_ul'>
				@foreach($typeData['nav'] as $val)
				<li>
					<div class='list_left_div'>{{ $val['name'] }} <span></span></div>
					@if(isset($val['child']) && $val['child'])
					<ul class="list_left_ul_li" @if($val['id'] == $upperTypData['parent_id']) style="display: block" @endif>
						@foreach($val['child'] as $v)
						<li @if($v['id'] == $upperTypData['id']) class='li_active' @endif><a href="{{ config('app.url') }}home/index/helpList?info_type={{ $v['id'] }}&Pid={{ $val['id'] }}">{{ $v['name'] }}</a></li>
						@endforeach
					</ul>
					@endif
				</li>
				@endforeach
			</ul>
			@endif
		</div>
		<div class='fr help_list_right'>
			<h4><a href="{{ config('app.url') }}home/index/helpList?info_type={{ $inforData['info_type'] }}&Pid={{ $upperTypData['parent_id'] }}">{{ $upperTypData['name'] }}</a> / 正文</h4>
			<div class='list_right_content'>
				<h3>{{ $inforData['title'] }}</h3>
				<div class="list_right_content_div">{{ $inforData['created_at'] }}</div>
				<div class="list_right_box_div">{!! $inforData['content'] !!}</div>
			</div>
			@if($otherHelps)
			<div class='help_list_expand'>
				<h4>拓展阅读</h4>
				<ul>
					@foreach($otherHelps as $help)
					<li><a href="/home/index/helpDetail/{{ $help['id'] }}">· {{ $help['title'] }}</a></li>
					@endforeach
				</ul>
			</div>
			@endif
		</div>
	</div>
@endsection
@section('foot.js')
	<script>
		var APP_URL = "{{ config('app.url') }}"
	</script>
	<script src="{{ config('app.source_url') }}home/js/detail.js" type="text/javascript" charset="utf-8"></script>
@endsection