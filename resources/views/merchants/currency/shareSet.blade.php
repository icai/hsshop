@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
<style type="text/css">
	#logoImgDiv{
		width:100%;
		margin-bottom: 10px;
	}
	#logoChange {
	    position: relative;
	    left:-130px;
	    top:-35px;
	    color:#99c5fc;
	}
	#logoChange input {
	    position: absolute;
	    bottom: 0;
	    top: 0;
	    right: 0;
	    left: -100px;
	    opacity: 0;
	}
	button{
		position: relative;
		left:97px;
	}
	.form-control{
		display:inline-block;
		width:auto;
		margin-right:20px
	}
	.content tbody tr td:nth-of-type(2){
		color:#999
	}
	.content tbody tr:nth-of-type(2) td:nth-of-type(1){
		vertical-align:top
	}
	.content tbody tr:nth-of-type(3) td:nth-of-type(1){
		vertical-align:top
	}
</style>
@endsection

@section('slidebar')
@include('merchants.currency.slidebar')
@endsection

@section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 普通导航 开始 -->
		<ul class="common_nav">
			<li>
				<a href="{{URL('/merchants/currency/index')}}">店铺信息</a>
			</li>
			<li>
                <a href="{{URL('/merchants/currency/location')}}">商家地址库</a>
            </li>
			
			<li>
				<a href="{{URL('/merchants/currency/outlets')}}">门店管理</a>
			</li>
			
			<li class="hover">
                <a href="{{URL('/merchants/currency/share/set')}}">通用分享设置</a>
            </li> 
		</ul>
		<!-- 普通导航 结束  -->
	</div>
	<!-- 三级导航 结束 -->

	<!-- 帮助与服务 开始 -->
	<div id="help-container-open" class="help_btn">
		<i class="glyphicon glyphicon-question-sign"></i>帮助和服务
	</div>
	<!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content">
	<table class="">
		<tr>
			<td>分享标题：</td>
			<td>
				<input type="text" name="title" value="{{ $info['share_title'] or '' }}" onchange="words_title();" size="50" class="title form-control">
				不能超过<span id="textCount">18</span>个字
			</td>
		</tr>
		<tr>
			<td>分享介绍：</td>
			<td>
				<textarea cols="50" rows="10" name="desc" onchange="words_text();" class="textarea form-control">{{ $info['share_desc'] or '' }}</textarea>
				不能超过<span id="textCount">50</span>个字
			</td>
		</tr>
		<tr>
			<td>分享logo：</td>
			<td>
				<div id="logoImgDiv" style="display: inline-block;">
					@if(isset($info['share_logo']) && $info['share_logo'])
	                <img src="{{ imgUrl() }}{{ $info['share_logo'] or '' }}" width="80" height="80">
	                @else
	                <img src="" width="80" height="80">
	                @endif
	                <input type="hidden" class="logo" value="{{ $info['share_logo'] or '' }}">
	    		</div>
				<form id="uploadForm" enctype="multipart/form-data">
    		    	<a href="##" id="logoChange"><span>上传logo</span> <input type="file" name="file" id="files" accept="image/jpeg,image/gif,image/png"></a>
            	</form>
			</td>
		</tr>
		<tr>
			<td>
				<button style="width: 100px;" class="confirm_btn btn btn-primary">确定</button>
			</td>
		</tr>
	</table>

</div>
@endsection

@section('page_js')
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript">

	//限制标题字数
	function words_title(){
		if($('.title').val().length > 18){
			$('.title').val($(".title").val().substr(0,18)); 
			tipshow('标题不能超过18个字','warn');
			return false;
		} 
	}

	//限制介绍字数
	function words_text(){
		if($('.textarea').val().length > 50){
			$('.textarea').val($(".textarea").val().substr(0,50)); 
			tipshow('分享介绍不能超过50个字','warn');
			return false;
		} 
	}

	$(function(){

		//logo图片上传
		$('#files').on('change', function(){
			var reader = new FileReader();
			reader.readAsDataURL(this.files[0]);
			if(this.files[0].size > 102400 * 8){
				tipshow("图片不能超过800K","warn");
				return;
			}
			reader.onload = function(e){
				$('#logoImgDiv img').attr('src',this.result);
				$('#logoImgDiv img').attr('width',80);
				$('#logoImgDiv img').attr('height',80);
				$('#logoChange span').text('修改logo')
			}
			$.ajax({
				url: '/merchants/myfile/upfile',
				type: 'POST',
				cache: false,
				data: new FormData($('#uploadForm')[0]),
				processData: false,
				contentType: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success:function(res) {
					res = JSON.parse(res);
					logo = res.data.FileInfo['path'];
					$('#logoImgDiv input').val(logo);
				},
				error:function(){

				}
			})
		});

		// 表单提交
		$('.confirm_btn').click(function(){
			var share_title = $('input[name="title"]').val();
			var share_desc  = $('textarea[name="desc"]').val();
			var share_img = $('.logo').val();
			var commit = true;
            if(!share_img && share_title && share_desc){
                tipshow("请填写分享图片","warn");
                return false;
            }
            if(!share_title && share_img && share_desc){
                tipshow("请填写分享标题","warn");
                return false;
            }
            if(!share_desc && share_title && share_img){
                tipshow("请填写分享内容","warn");
                return false;
            }
            if(!share_title){
                tipshow("请填写分享标题","warn");
                return false;
            }
            if(!share_desc){
                tipshow("请填写分享内容","warn");
                return false;
            }
            if(!share_img){
                tipshow("请填写分享图片","warn");
                return false;
            }
			if(commit){
				$.ajax({
		            type: "POST",
		            url: "/merchants/currency/share/addShareInfo",
		            data: {title:share_title, desc:share_desc,logo:share_img},
		            headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
		            success: function(data){
	                    tipshow(data.info);
	                },
	                error:function(){

	                }

	         	});
			}
		});
	});

</script>
@endsection