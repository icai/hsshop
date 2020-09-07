@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/set.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/ty_20180228.css">

@endsection
@section('main')
<div class="content">
	<div class="public_hint">
		<p>公告：业务员需要授权并提交以下信息，才可绑定客户，请谨慎对待！</p>
	</div>
	<div class="message_input">
		<p class="input_div">
			<span>姓名：</span>
			<input type="text" name="name" @if($registerData) disabled @endif  id="name" value="{{$registerData['name']??''}}" />
		</p>
		<p class="input_div">
			<span>手机号：</span>
			<input type="number" name="mobile" id="mobile" @if($registerData) disabled @endif  value="{{$registerData['mobile']??''}}" />
		</p>
		@if(!$registerData)
		<button type="button" id="submit">提交</button>
			@endif
		@if($registerData)
			<div style="text-align: center;margin-top: 20px">
				<img src="{{imgUrl()}}{{$registerData['qrcode']??''}}" />
			</div>
			<div style="text-align: center;margin-top: 20px">
				<span> {{$jumpUrl}}</span>
			</div>
		@endif
	</div>
	<!--弹窗-->
	<div class="dialog_div">
		<div class="dialog_board"></div>
		<div class="dialog">
			<div class="dialog_content">
				您已确认个人信息无误
			</div>
			<div class="fun_btn">
				<span class="sure">确认</span>
				<span class="cancle">取消</span>
			</div>
		</div>
	</div>
<form>
	<div class="message_input search_input" style="text-align: center">
		<p class="input_div">
			<input type="text" name="nickname" value="" placeholder="请输入昵称" />
		</p>
		<p class="input_div">
			<input type="text" name="mobile" value="" placeholder="留言手机号码" />
		</p>
		<p class="input_div">
			<input type="text" name="name" value="" placeholder="留言姓名" />
		</p>
		<p class="input_div">
			<button type="button" id="search" class="search">搜索</button>
		</p>

	</div>
</form>
<div class="list_div">
	<ul class="list list_header">
		<li>昵称</li>
		<li>拼团</li>
		<li>级别</li>
		<li>注册时间</li>
	</ul>
	@forelse($data[0]['data'] as $val)
		<ul class="list list_body">
			<li>{{$val['nickname']}}</li>
			<li>@if($val['is_open_groups']==0)否@else<a href="/shop/activity/getGroupsInfo?id={{$val['id']}}">是(点击查看)</a>@endif</li>
			<li>{{$val['level']}}</li>
			<li>{{$val['intime']}}</li>
		</ul>@endforeach
</div>
</div>
</div>
@endsection
@section('page_js')
<script>
	var wid = "{{session('wid')}}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/js/ty_20180228.js"></script>
@endsection