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
            		<input type="hidden" name="id" value="{{ $data['id']??'' }}">
            		<div class="form-div">
            			<label class="fom-lab">广告标题：</label>
            			<input class="save-int clearint" type="" name="title" id="" value="{{ $data['title'] or '' }}" />
            		</div>
            		<div class="form-div">
            			<label class="fom-lab">广告链接：</label>
            			<input class="save-int clearint" type="" name="url" id="" value="{{ $data['url'] or '' }}" />
            		</div>
            		<div class="form-div flt">
            			<label class="fom-lab">广告图片：</label>
						<div class="imgDiv flex_star">
                            <div class="relative upImg">
                            	<div class="imgGroup">
									@if(isset($data['img'])&&$data['img'])
									<div class="img_item"><img class="littleImg" src="{{imgUrl()}}{{$data['img']}}" width="400" height="400"></div>
                            		@endif
								</div>
                            	<a id="btnUp">选择上传图片</a>
                                <input id="img" type="hidden" name="img" class="filepath absolute" value="{{ $data['img']??''}}" />
                            </div>
                        </div>
                        <div class="clear"></div>    
            		</div>
					<div class="form-div">
						<label class="fom-lab">广告时长：</label>
						<input class="save-int clearint" type="" name="sec" id="" value="{{ $data['sec']??'' }}" />
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
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/selleradsave.js" type="text/javascript" charset="utf-8"></script>
@endsection