@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_ty180119.css" />
<link href="{{ config('app.source_url') }}mctsource/static/css/cropper.min.css" rel="stylesheet">
<style>
	.flex{
		display: flex;
		display: -webkit-flex;
		display: -moz-flex;
		display: -ms-flex;
		display: -o-flex;
	}
	.cropper-box{
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 99999;
		background-color: rgba(0,0,0,.6);
		display: none;
	}
	.cropper{
		width: 900px;
		height: 524px;
		background-color: #ffffff;
		border-radius: 2px;
		position: absolute;
		top: 50%;
		left: 50%;
		margin: -267px 0 0 -450px;
	}
	.cropper-header{
		padding: 20px 20px 10px 20px;
		align-items: center;
		justify-content: space-between;
	}
	.cropper-header-title{
		line-height: 24px;
		font-size: 18px;
		color: #303133
	}
	.close-cropper{
		background: url("https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/guanbi-x.png") no-repeat;
		background-size: 100% 100%;
		width: 12px;
		height: 12px;
		cursor: pointer;
	}
	.cropper-body{
		padding: 0 20px;
		justify-content: space-between;
	}
	.cropper-title{
		margin: 16px 0;
		font-size: 18px;
		color: #606266;
	}
	.tailor{
		width: 320px;
		height: 320px;
		background: #f9fbf9;
		border: 1px solid #d0ecd0;
		text-align: center;
	}
	.preview{
		width: 450px;
		height: 320px;
		background-color: #999;
		justify-content: center;
		align-items: center;
	}
	.img-preview{
		overflow: hidden;
		width: 100%;
		height: 100%;
	}
	.cropper-footer{
		padding: 30px 20px 20px;
		justify-content: space-between;
	}
	.cropper-select{
		width: 86px;
		height: 38px;
		line-height: 38px;
		text-align: center;
		background: #fff;
		border: 1px solid #1aad19;
		border-radius: 4px;
		position: relative;
		overflow: hidden;
		cursor: pointer;
		color: #1aad19;
	}
	.select-only{
		position: absolute;
		top: 0;
		left: 0;
		font-size: 0;
		opacity: 0;
		cursor: pointer;
		width: 100%;
		height: 100%;
	}
	.footer-button{
		padding: 12px 20px;
		cursor: pointer;
		display: inline-block;
		border-radius: 4px;
		font-size: 14px;
		line-height: 1;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		-ms-box-sizing: border-box;
		box-sizing: border-box;
	}
	.close-button{
		color: #666;
		border: 1px solid #dcdfe6;
		margin-right: 10px;
	}
	.close-button:hover{
		color: #1aad19;
		border-color: #bae6ba;
		background-color: #eff8ef;
	}
	.submit-button{
		background: #1aad19 ;
		color: #fff;
	}
	.submit-button:hover{
		background-color: #48bd47;
	}
	.cropper-hide{
		display: none;
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
            <li class="hover">
                <a href="{{URL('/merchants/currency/commonSetting')}}">通用设置</a>
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
    <h5>小程序设置</h5>
    <br />
    <br />
	<form id="uploadForm" class="form-horizontal" enctype="multipart/form-data">
		<!--二维码图片-->
	  	<div class="form-group">
	    	<label for="Qcode" class="col-sm-2 control-label">个人主页展示：</label>
	    	<div class="col-sm-4" style="margin-top: 7px;">
			<img class="codeImgShow" id='showStoreImg' src="{{ imgUrl($weixinInfo['wechat_qrcode']) }}" height="100" width="100"/>
			    <a href="##" id="QcodeChange">
			    	@if(isset($weixinInfo['wechat_qrcode']) && $weixinInfo['wechat_qrcode'])
			    	<span id='uploadImg'>修改图片</span>
			    	@else
			    	<span id='uploadImg' class='QcodeChange-span'>+添加图片 </span>
			    	<img class="codeImgShow" src="" height="100" width="100"/>
			    	<input type="file" name="file" id="files" accept="image/jpeg,image/gif,image/png">
			    	@endif
			    	<input type="hidden" name="codeUrl" id="codeUrl" value="{{ $weixinInfo['wechat_qrcode'] or '' }}" />
			    </a>
      			<span id="hintText" class="help-block">图片建议尺寸：116*116px，图片大小不超过3M</span>
	    	</div>
	  	</div>
	  	
	  	<!--名称-->
	  	<div class="form-group">
	    	<label for="name" class="col-sm-2 control-label">名称：</label>
	    	<div class="col-sm-3">
	      		<input type="text" class="form-control xcx-setting" id="name" name="name" placeholder="" value="{{ $weixinInfo['qrcode_name'] or '' }}">
      			<span id="hintText" class="help-block xcx-help-block">不超过6个字</span>
	    	</div>
	  	</div>
	  	<!--提交按钮-->
	  	<div class="form-group">
	    	<div class="col-sm-offset-2 col-sm-10">
	      		<button type="button" class="btn btn-primary submit">提交</button>
	    	</div>
	  	</div>
	</form>
</div>


<div class='cropper-box' id='cropper'>
	<div class='cropper'>
		<div class='cropper-header flex'>
			<span class='cropper-header-title'>添加图片</span>
			<span class='close-cropper close-dalong'></span>
		</div>
		<div class='cropper-body flex'>
			<div class='cropper-tailor'>
				<p class='cropper-title'>裁剪操作框</p>
				<div class='tailor cropper-hide' style="" id='tailor_a'>
					<img src="" id="photo">
				</div>
				<div class='tailor' style="" id='tailor_b'></div>
			</div>
			<div class='cropper-preview'>
				<p class='cropper-title'>裁剪预览框</p>
				<div class='preview flex cropper-hide' id='preview_a'>
					<div class='img-preview'></div>
				</div>
				<div class='preview flex' id='preview_b'></div>
			</div>
		</div>
		<div class='cropper-footer flex'>
			<div class='cropper-select'>
				<span>选择图片</span>
				<input type="file" accept="image/png, image/jpeg, image/gif, image/jpg" id="input" class="select-only">
			</div>
			<div class='cropper-button'>
				<div class='close-button footer-button close-dalong'>取消</div>
				<div class='submit-button footer-button' id='tailor'>确认</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('page_js')
<!--layer文件引入-->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<!--上传图片js-->
<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 当前页面js -->
<script type="text/javascript">
    var imgUrl = "{{ config('app.source_url') }}" + 'mctsource/';
    var wechat_qrcode = "{{ $weixinInfo['wechat_qrcode'] or ''}}"
</script>
<script src="{{ config('app.source_url') }}static/js/jquery.js"></script>
<script src="{{ config('app.source_url') }}mctsource/static/js/cropper.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/cropper_img.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/currency_ty180119.js"></script>
@endsection

