@extends('merchants.default._layouts') @section('head_css')
<!--bootstrape验证插件css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_1hnfjq70.css" /> @endsection @section('slidebar') @include('merchants.currency.slidebar') @endsection @section('middle_header')
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
			<!--<li>
				<a href="{{URL('/merchants/currency/contact')}}">联系我们</a>
			</li>-->
			<li>
				<a href="{{URL('/merchants/currency/outlets')}}">门店管理</a>
			</li>
			<li class="hover">
				<a href="{{URL('/merchants/currency/afterSale')}}">退货/维权设置</a>
			</li>
			<li>
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
@endsection @section('content')
<form id="defaultForm" class="form-horizontal content" method="post" action="">
	{{ csrf_field() }}
	<!--联系电话部分-->
	<div class="form-group linkage">
		<label class="col-lg-2 control-label">收货人：</label>
		<div class="col-lg-4">
			<input type="tel" name="data[name]" value="{{$detail['name'] ?? ''}}" id="peopleName" class="form-control" placeholder="请填写收货人姓名" />
		</div>
	</div>
	<div class="form-group linkage">
		<label class="col-lg-2 control-label">联系电话：</label>
		<div class="col-lg-4">
			<input type="tel" name="data[area_code]" value="{{$detail['area_code'] ?? ''}}" id="first_number" class="form-control" style="width: 50px; text-align: center;" placeholder="区号" />&nbsp;-
			<input type="tel" name="data[phone]" value="{{$detail['phone'] ?? ''}}" id="last_number" class="form-control" placeholder="请输入电话号码（区号可为空）" />
		</div>
	</div>
	<!--联系地址部分-->
	<div class="form-group linkage">
		<label class="col-lg-2 control-label">联系地址：</label>
		<div class="col-lg-4">
			<!--三级联动块-->
			<div class="control-group" style="display: inline-block;">
				<div class="controls">
					<span>
	                	<select name="data[province_id]" class="js-province address-province">
	                    	<option value=''>选择省份</option>
							@foreach($provinceList as $pro)
								<option @if(!empty($detail['province_id']) && $detail['province_id'] == $pro['id']) selected  @endif value="{{ $pro['id'] }}"> {{ $pro['title'] }}</option>
							@endforeach
	                    </select>
	                </span>
					<span class="marl-15">
	                    <select name="data[city_id]" class="js-city address-city">
	                    	<option value=''>选择城市</option>
							@if(!empty($detail) && isset($regionList[$detail['province_id']]))
								@forelse($regionList[$detail['province_id']] as $val)
									<option @if($detail['city_id'] == $val['id']) selected  @endif value="{{ $val['id'] }}"> {{ $val['title'] }}</option>
								@endforeach
							@endif
	                    </select>
	                </span>
					<span class="marl-15">
	                    <select name="data[area_id]" class="js-county address-county">
	                    	<option value=''>选择地区</option>
							@if(!empty($detail) && isset($regionList[$detail['city_id']]))
								@forelse($regionList[$detail['city_id']] as $val)
									<option @if($detail['area_id'] == $val['id']) selected  @endif value="{{ $val['id'] }}"> {{ $val['title'] }}</option>
								@endforeach
							@endif
	                    </select>
	                </span>
				</div>
			</div>
			<br />
			<input type="text" name="data[address]" value="{{$detail['address'] ?? ''}}" id="addTxt" class="form-control" placeholder="请填写具体地址" />
		</div>
	</div>
	<hr />
	<div class="form-group linkage">
		<label class="col-lg-2 control-label">售后/维权客服电话：</label>
		<div class="col-lg-9">
			<input type="tel" name="data[service_mobile]" value="{{$detail['service_mobile'] ?? ''}}" id="afterSaleNum" class="form-control" placeholder="请填写手机号" />
			<i class="hint">手机号将用于接收买家维权咨询、维权通知提醒</i>
		</div>
	</div>
	<div class="form-group linkage">
		<label class="col-lg-2 control-label"></label>
		<div class="col-lg-8">
			<sp>
				<input type="tel" name="data[service_area_code]" value="{{$detail['service_area_code'] ?? ''}}" id="firstNumber" class="form-control" style="width: 50px; text-align: center;" placeholder="区号" />&nbsp;-
				<input type="tel" name="data[service_phone]" value="{{$detail['service_phone'] ?? ''}}" id="lastNumber" class="form-control" placeholder="座机号码（可不填）" />
				<i class="hint">优先展示座机号码</i>
			</sp>
			<label for="showIf" style="font-size: 12px; margin-top: 10px;">
                <input type="checkbox" name="data[show_in_order]" id="showIf" @if(!empty($detail['show_in_order'])) checked @endif />在订单详情中展示，买家可通过“拨打电话”直接联系我们（推荐勾选）
            </label>
		</div>
	</div>
	<hr />
	<div class="form-group">
		<label class="col-lg-2"></label>
		<div id="save" class="col-lg-8">
			<button type="submit" id="saveBtn" class="btn btn-primary offset">保存</button>
		</div>
	</div>
</form>
@endsection @section('page_js')
<script type="text/javascript">
	var json = {!! $regions_data !!};
</script>
<!--bootstrap表单验证插件js-->
<script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_1hnfjq70.js"></script>
@endsection