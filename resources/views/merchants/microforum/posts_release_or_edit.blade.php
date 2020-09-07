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
                <a href="javascript:;">帖子管理</a>
            </li>
            <li>
                <a href="javascript:;">发布帖子</a>
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
				分类：
			</div>
			<div class="wrapper-group-cont group-inner"> 
				<select class="form-control iblock" id="discussions_id" style="width:auto;">
					<option value="">请选择</option>
            		@foreach($categoriesDatas as $categoriesData)
						<option value="{{$categoriesData->id}}" @if (isset($postsInfo) && $postsInfo['discussions_id'] == $categoriesData->id) selected=1 @endif>{{$categoriesData->title}}</option>
					@endforeach 
				</select>
				<em class="required">*</em>
			</div>
		</div>
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				标题：
			</div>
			<div class="wrapper-group-cont group-inner">
				<input type="text" class="form-control iblock w300" id="title" value="{{$postsInfo['title'] or ''}}" placeholder="">
				<em class="required">*</em>
				<p></p>
			</div>
		</div>
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				图片：
			</div>
			<div class="wrapper-group-cont group-inner ">
				<div id="img_previwe">
				@if (isset($postsInfo))
					@forelse ($postsInfo->img_paths as $v)
						<div class="img-box">
							<img data-id="{{$v->id}}" src="{{ imgUrl() }}{{$v->path}}" width="50" height="50" />
							<span class="img-close">x</span>
						</div> 
					@empty
					@endforelse
				@endif
				</div>
				<p>
					<button class="btn btn-primary" id="add_img">添加图片</button>（建议尺寸：160*160    最多上传9张，最少一张）
				</p> 
			</div>
		</div>
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner">
				内容：
			</div>
			<div class="wrapper-group-cont group-inner">
				<textarea id="ueditor" style="width:700px;height:400px">{{$postsInfo['content'] or ''}}</textarea>
			</div>
		</div> 
		<div class="wrapper-group">
			<div class="wrapper-group-title group-inner"></div>
			<div class="wrapper-group-cont group-inner">
				<button class="btn btn-primary js-submit ml10">保存</button>
				<input type="hidden" id="id" value="{{$postsInfo['id'] or ''}}" />
			</div>
		</div>
	</div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
	var img_array = []; //图片集合 用于显示图片和提交
</script>
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/release_uynh7ai2.js"></script>

@endsection
