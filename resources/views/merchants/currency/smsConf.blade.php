@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_smsConf.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')

@endsection
@section('content')

<div class="content">
	<div class="div weiXin">
		<div class="title weiXin_title">
	        <div class="title_left">
	            <b>短信通知</b>
	            
	            <p>开启之前先去注册<a target="_blank" href="http://www.yuntongxun.com/">云通讯</a>账号，注册完成后，商家在这里设置手机验证开关，开启后填写相关参数，开通短信通知功能，实现新订单短信通知功能</p>
				{{--<span>（云通讯/管理/控制台首页）</span>--}}
			</div>
	        <div class="title_right">
	            <!-- 按钮 开始 -->
	            <div class="switch_items">
	                <label class="ui-switcher ui-switcher-off" data-is-open="0"></label>
	            </div>
	            <!-- 按钮 结束 -->
	        </div>
	    </div>
	    <div class="contentDiv weiXin_content">
	    	<form id="form">
	    		<input type="hidden" name="id" class="" value="" />
		    	{{--<h4 class="fiz-bold"></h4>--}}
		        <div class="form-group">
		            <label class=" control-label">开发者账号主账号ACCOUNT SID：</label>
		            <div class="wid50">
		                <input name="account_sid" class="form-control" value="" />
		            </div>
		        </div>
		        <div class="form-group">
		            <label class=" control-label">AUTH TOKEN：</label>
		            <div class="wid50">
		                <input name="account_token" class="form-control" value=""/>
		            </div>
		        </div>
		        <div class="form-group">
		            <label class=" control-label">商家电话：</label>
		            <div class="wid50">
		                <input name="phone" class="form-control" value="" />
		            </div>
		        </div>
		        <div class="form-group">
		            <label for="inputPassword3" class=" control-label" accept="image/*">AppID (默认)：</label>
		            <div class="wid50">
		               <input name="app_id" class="form-control" value="" />
		            </div>
		        </div>
		        {{--<h4 class="fiz-bold">短信模板</h4>--}}
		        <div class="form-group" style="margin-top: 12px;">
		            <label class="control-label">短信模板ID:</label>
		            <div class="wid50">
		               <input name="code" class="form-control" value="" />
		            </div>
		        </div>
		        <div class="form-group">
		            <div class="" style="margin-left: 15%;">
		                <button type="button" class="btn btn-default">保存</button>
		            </div>
		        </div>
	        </form>
	    </div>
    </div>
</div>

@endsection
@section('page_js')
<!--特殊按钮js文件-->
<script type="text/javascript">
	var info = {!! json_encode($info)!!}
	console.log(info)
</script>
<script src="{{ config('app.source_url') }}mctsource/js/specialBtn.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_smsConf.js"></script>
@endsection