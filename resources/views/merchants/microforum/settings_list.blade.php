@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/settings_uynh7ai2.css" />
@endsection
@section('slidebar')
@include('merchants.microforum.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">社区设置</div>
        <!-- 二级导航三级标题 结束 -->
    </div> 
</div>

@endsection
@section('content')
<div class="content"> 
	<div class="wrapper">
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				社区名称：
			</div>
			<div class="wrapper-group-cont group-inner">
				<input type="text" class="form-control iblock w300" id="title" value="{{$forumData->title or ''}}">
				<em class="required">*</em>
			</div>
		</div>
		<!-- <div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				微社区简介：
			</div>
			<div class="wrapper-group-cont group-inner">
				<input type="text" class="form-control iblock w300" id="introduction" placeholder="找到有共同话题的朋友" id=""  value="{{$forumData->introduction or ''}}">
				<em class="required">*</em>
				<p></p>
			</div>
		</div> -->
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				微社区头像：
			</div>
			<div class="wrapper-group-cont group-inner flex">
				<div id="head_img_box">
				@if(!is_null($forumData) && !empty($forumData->img_path))
					<img src="{{ imgUrl() }}{{$forumData->img_path}}" id="head_img" /> 
				@endif
				</div>
				<div>
					<label for="head_file" class="btn btn-default">选择文件</label><em class="required">*</em>
					<input type="file" id="head_file" id="file" class="form-control iblock w200" style="display: none;" accept="image/jpeg,image/jpg,image/png" />
					<input type="hidden" id="imgid" value="{{$forumData->imgid or ''}}" /> 
					<br />
					<p class="mt5">支持jpg，png格式图片，文件小于1M（尺寸1:1）</p>
				</div>

			</div>
		</div>
		@if(!is_null($forumData))
			<div class="wrapper-group">
				<div class="wrapper-group-title group-inner">
					微社区地址：
				</div>
				<div class="wrapper-group-cont group-inner" style="line-height: 33px;">
					{{ URL("/shop/microforum/forum/index/" . session('wid')) }}
				</div>
			</div>
			<div class="wrapper-group">
				<div class="wrapper-group-title group-inner">
					微社区二维码：
				</div>
				<div class="wrapper-group-cont group-inner">
					{!! QrCode::size(150)->generate(URL("/shop/microforum/forum/index/" . session('wid'))); !!}
				</div>
			</div>
		@endif
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner"></div>
			<div class="wrapper-group-cont group-inner">
				<button class="btn btn-primary js-submit ml10">保存</button>
			</div>
		</div> 
	</div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/settings_uynh7ai2.js"></script>
@endsection
