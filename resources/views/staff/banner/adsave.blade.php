@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/7.1 bannersave.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
        	<div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>{{ $title }}</span>
            </div>
            <div class="main_content">      
            	<form id="saveform">
            		<input type="hidden" name="id" value="{{ $data['id'] or '' }}">
            		<div class="form-div">
            			<label class="fom-lab">广告标题：</label>
            			<input class="save-int clearint" type="" name="title" id="" value="{{ $data['title'] or '' }}" />
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">广告链接：</label>
            			<input class="save-int clearint" type="" name="url" id="" value="{{ $data['url'] or '' }}" />
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">广告位置：</label>
            			<select name="position" class="save-int">
                            @foreach($posArr as $v)
            				<option value="{{ $v }}" @if(isset($data['position']) && $v == $data['position']) selected=selected @endif>{{ $v }}</option>
            				@endforeach
            			</select>
            		</div>
            		<div class="form-div flt">
            			<label class="fom-lab">广告图片：</label>
						<div class="imgDiv flex_star">
                            <div class="relative upImg">
                            	<div class="imgGroup">	                            
                            	</div>
                            	<a id="btnUp">选择上传图片</a>
                                <input id="img" type="hidden" name="img" class="filepath absolute" value="{{ $data['img'] or '' }}" />
                            </div>
                        </div>
                        <div class="clear"></div>    
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">广告类型：</label>
            			<select name="type" class="save-int">
            				<option value="0" @if(isset($data['type']) && $data['type'] == 0) selected @endif>普通广告</option>
            				<option value="1" @if(isset($data['type']) && $data['type'] == 1) selected @endif>精选广告</option>
            			</select>
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">排序：</label>
            			<input class="clearint" type="number" name="sort" id="" value="{{ $data['sort'] or '' }}" />
            		</div>
            		<div class="form-div form-subm">
	            		<input class="btn btn-primary saveup" type="button" value="确认提交"/>
	            		<input class="btn btn-primary clear-form" type="button" value="重置表单"/>            			
            		</div>
            	</form>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
	<script type="text/javascript">
		var _host = "{{ config('app.source_url') }}staff";
        var imgUrl ="{{ imgUrl() }}";
	</script>
    <script src="{{ config('app.source_url') }}static/js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/adsave.js" type="text/javascript" charset="utf-8"></script>
@endsection