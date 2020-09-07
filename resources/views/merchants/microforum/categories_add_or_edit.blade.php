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
        <ul class="crumb_nav">
            <li>
                <a href="/merchants/microforum/categories/list">分类管理</a>
            </li>
            <li>
                <a href="javascript:;">添加分类</a>
            </li>
        </ul> 
    </div> 
</div>

@endsection
@section('content')
<div class="content">
	<div class="wrapper">
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				分类名称<em class="required">*</em>：
			</div>
			<div class="wrapper-group-cont group-inner">
				<input type="text" maxlength="3" class="form-control iblock w300" value="{{$categoryInfo->title or ''}}" id="title">
				<p class="mt10">（提示：1-3个字，最多可设置5个分类板块）</p>
			</div>
		</div>
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				排序：
			</div>
			<div class="wrapper-group-cont group-inner">
				<input type="number" class="form-control iblock w100" value="{{$categoryInfo->sort or ''}}" placeholder="" id="sort">
				<p class="mt10">（提示：数字越大越靠前）</p>
			</div>
		</div> 
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner"></div>
			<div class="wrapper-group-cont group-inner">
				<button class="btn btn-primary js-submit ml10">保存</button>
				<button class="btn btn-default ml10" onclick="history.back()">取消</button>
				<input type="hidden" id="id" value="{{$categoryInfo->id or ''}}" name="">
			</div>
		</div>
	</div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/categories_uynh7ai2.js"></script>
@endsection
