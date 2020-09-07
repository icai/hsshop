@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/set.css">
	<style type="text/css">
		.phone_code{width: 90px; height: 30px; position: absolute; right: 10px;top: 10px; border: 0; background: #fff; border: 1px solid #F72F37;font-size: 13px; color: #ff4f4f; border-radius: 15px; line-height: 30px; outline: none;}
		.col-ccc{color: #333;}
		.hidden{display: none;}
	</style>
@endsection
@section('main')
<div class="content">
	<form>
		<div class="user-info">
			<ul class="weui-cells weui-cells-form js-user-form">
				<li class="weui-cell remo_col col-f8 box_bottom_1px">
					<div class="weui-cell-hd">
						<label for="name" class="weui-label">手机号：</label>
					</div>
					<div class="weui-cell-bd weui-cell-primary">
						<input id="" type="text" name="phone" disabled="disabled" class="weui-input ver_phone" value="" placeholder="请输入您的手机号">
					</div>						
				</li>
				<li class="weui-cell">
					<div class="weui-cell-hd">
						<label for="birthday" class="weui-label">验证码：</label>
					</div>
					<div class="weui-cell-bd weui-cell-primary">
						<input type="text" id="birthday" name="code" class="weui-input ver_code" value="" placeholder="请输入验证码">
						<input type="button" class="phone_code phone_codefir get_codefir text-primary" value="获取验证码">
							<input type="button" class="phone_code phone_codesec get_codesec text-primary hidden" value="获取验证码">
					</div>
				</li>					
			</ul>
		</div>
		<div class="btn-block">
			<input type="button" class="btn bg-success btn-block phone-next" value="下一步">
			<input type="button" class="btn bg-success btn-block phone-up hidden" value="确认修改">
		</div>
	</form>
	@include('shop.common.footer')
</div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var wid = '{{ session('wid') }}';
        var mobile = '{{$mobile}}';
		$("input[name='phone']").val(mobile)
		var url="{{$url}}";
    </script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/until.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/bindmobile-change.js"></script>
@endsection