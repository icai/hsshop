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
	            <div class="save-a">
	            	<a href="/staff/banner/index">banner列表</a>
	            	<a href="/staff/banner/save">{{ $title }}</a>
	            </div>
            	<form id="saveform">
                    <input type="hidden" name="id" value="{{ $data['id'] or '' }}">
            		<div class="form-div">
            			<label class="fom-lab">Banner名称：</label>
            			<input class="save-int clearint" type="" name="name" id="" value="{{ $data['name'] or '' }}" />
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">Banner位置：</label>
            			<select name="position" class="save-int">
                            @foreach($posArr as $v)
            				<option value="{{ $v }}" @if(isset($data['position']) && $v == $data['position']) selected=selected @endif>{{ $v }}</option>
            				@endforeach
            			</select>
            		</div>
            		<div class="form-div flt">
            			<label class="fom-lab">Banner上传：</label>
							<div class="imgDiv flex_star">
	                            <div class="relative upImg">
	                            	<div class="imgGroup">	                            
	                            	</div>
	                            	<a id="btnUp">选择上传图片</a>
	                                <input id="img" type="hidden" name="img" class="filepath absolute" value="{{ $data['img'] or '' }}" />
	                                <span class="hint">推荐尺寸:1920×768</span>
	                            </div>
	                        </div>
	                        <div class="clear">	                        	
	                        </div>
        				<!--</label>-->     
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">链接类型：</label>
                        @if(isset($data['link_type']) && $data['link_type'] == 1)
                        <label><input type="radio" name="link_type" class="link_type" value="0" />外链</label>
                        <label><input type="radio" name="link_type" class="link_type" value="1" checked="checked"/>内链</label>
                        @else
                        <label><input type="radio" name="link_type" class="link_type" value="0" checked="checked"/>外链</label>
                        <label><input type="radio" name="link_type" class="link_type" value="1" />内链</label>
                        @endif
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">链接url：</label>
            			<input class="save-int clearint" type="" name="link_url" id="" value="{{ $data['link_url'] or '' }}" />
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
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/7.3 bannerupimg.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/7.1 bannersave.js" type="text/javascript" charset="utf-8"></script>
@endsection