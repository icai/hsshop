@extends('merchants.default._layouts')
@section('head_css')
	<!-- 当前页面css -->
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_9ps47mzo.css" />
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/location.css" />
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/editAddress.css" />
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
				<li class="hover">
					<a href="{{URL('/merchants/currency/location')}}">商家地址库</a>
				</li>
			
            <li>
                <a href="{{URL('/merchants/currency/outlets')}}">门店管理</a>
            </li>
            <li>
                <a href="{{URL('/merchants/currency/afterSale')}}">退货/维权设置</a>
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
		<div class="app">
			<div class="app-inner clearfix">
				<div class="app-init-container">
					<div class="app__content"><div data-reactroot="" class="location-add">
							<h3>编辑地址</h3>
							<form class="zent-form zent-form--horizontal location-add-form">
								<div class="zent-form__control-group ">
									<label class="zent-form__control-label">联系人：</label>
									<div class="zent-form__controls">
										<div class="zent-input-wrapper">
											<input type="text" onFocus="replaceSpace(this)" class="zent-input" name="contact_name" placeholder="请填写联系人姓名" value="@if(!empty($address)){{$address['name']}}@endif">
										</div>
									</div>
								</div>
								<div class="zent-form__control-group">
									<label class="zent-form__control-label">联系方式：</label>
									<div class="zent-form__controls">
										<div tabindex="0" class="zent-select areacode ">
											<div class="zent-select-text">中国 +86</div>
											<div tabindex="0" class="zent-select-popup">
												<div class="zent-select-search">
													<input type="text" placeholder="" class="zent-select-filter" value="@if(!empty($address)){{$address['name']}}@endif">
												</div>
											</div>
										</div>
										<div class="zent-input-wrapper phone-num">
											<input type="text" class="zent-input zent-phone" placeholder="请填写手机号" value="@if(!empty($address)){{$address['mobile']}}@endif">
										</div>
									</div>
								</div>
								<div class="zent-form__control-group ">
									<label class="zent-form__control-label">联系地址：</label>
									<div class="js-area-layout area-layout" data-area-code="">
										<span>
				                            <select name="member_province" class="js-province address-province">
				                            	<option value=''>选择省份</option>
												@foreach($provinceList as $pro)
													<option @if(!empty($address) && $address['province_id'] == $pro['id']) selected  @endif value="{{ $pro['id'] }}"> {{ $pro['title'] }}</option>
												@endforeach
				                            </select>
				                        </span>
										<span class="marl-15">
				                            <select name="member_city" class="js-city address-city">
				                            	<option value=''>选择城市</option>
												@if(!empty($address) && isset($regionList[$address['province_id']]))
													@forelse($regionList[$address['province_id']] as $val)
														<option @if($address['city_id'] == $val['id']) selected  @endif value="{{ $val['id'] }}"> {{ $val['title'] }}</option>
														@endforeach
														@endif
				                            </select>
				                        </span>
										<span class="marl-15">
				                            <select name="member_county" class="js-county address-county">
				                            	<option value=''>选择地区</option>
												@if(!empty($address) && isset($regionList[$address['city_id']]))
													@forelse($regionList[$address['city_id']] as $val)
														<option @if($address['area_id'] == $val['id']) selected  @endif value="{{ $val['id'] }}"> {{ $val['title'] }}</option>
														@endforeach
														@endif
				                            </select>
				                        </span>
									</div>
								</div>
								<div class="zent-form__control-group ">
									<label class="zent-form__control-label">详细地址：</label>
									<div class="zent-form__controls">
										<div class="zent-input-wrapper">
											<input type="text" onFocus="replaceSpace(this)" class="zent-input" name="address" placeholder="请填写详细地址，如街道名称，门牌号等信息" value="@if(!empty($address)){{$address['address']}}@endif">
										</div>
									</div>
								</div>
								<div class="zent-form__control-group ">
									<label class="zent-form__control-label">邮编：</label>
									<div class="zent-form__controls">
										<div class="zent-input-wrapper">
											<input type="text" class="zent-input" name="zip_code" placeholder="请填写邮政编码" value="@if(!empty($address)){{$address['zip_code']}}@endif">
										</div>
									</div>
								</div>
								<div class="zent-form__control-group  zent-form__type-wrap show-type">
									<label class="zent-form__control-label">地址类型：</label>
									<div class="zent-form__controls" style="padding-top: 1px;">
										@if(!empty($address) && $address['type'] == 0 && $address['is_default'] == 1 )
											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="tuihuo-moren" type="checkbox" onclick="tuihuo_moren()" checked="checked">
			                					</span>
												<span>设为默认退货地址</span>
											</label>

											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="fahuo-moren" type="checkbox" onclick="fahuo_moren()">
			                					</span>
												<span>设为默认发货地址</span>
											</label>
										@elseif(!empty($address) && $address['type'] == 3 && $address['is_send_default'] == 1 && $address['is_default'] == 1)
											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="tuihuo-moren" type="checkbox" onclick="tuihuo_moren()" checked="checked"> 
			                					</span>
												<span>设为默认退货地址</span>
											</label>

											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="fahuo-moren" type="checkbox" onclick="fahuo_moren()" checked="checked">
			                					</span>
												<span>设为默认发货地址</span>
											</label>
										@elseif(!empty($address) && $address['type'] == 2 && $address['is_send_default'] == 1 && $address['is_default'] == 0)
											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="tuihuo-moren" type="checkbox" onclick="tuihuo_moren()"> 
			                					</span>
												<span>设为默认退货地址</span>
											</label>

											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="fahuo-moren" type="checkbox" onclick="fahuo_moren()" checked="checked">
			                					</span>
												<span>设为默认发货地址</span>
											</label>
										@else
											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="tuihuo-moren" type="checkbox" onclick="tuihuo_moren()"> 
			                					</span>
												<span>设为默认退货地址</span>
											</label>

											<label class="return-location show zent-checkbox-wrap zent-checkbox-checked zent-checkbox-disabled">
			                					<span class="zent-checkbox">
			                						<input class="fahuo-moren" type="checkbox" onclick="fahuo_moren()">
			                					</span>
												<span>设为默认发货地址</span>
											</label>

										@endif
									</div>
								</div>
								<div class="zent-form__form-actions">
									<button class="ui-btn ui-btn-primary" type="button">保存</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="notify-bar js-notify animated hinge hide"></div>
			</div>
		</div>
	</div>
	@if(!empty($address))
		<input id="address_id" type="hidden" value="{{$address['id']}}">
	@else
		<input id="address_id"  type="hidden" value="">
	@endif
	<script type="text/javascript">
		var json = {!! $regions_data !!};
		//去除input空格
		function replaceSpace(obj){
			obj.value = obj.value.replace(/\s/gi,'')
		}

		var _type = "{{ $address['type'] or '0' }}";
		var _default = "{{ $address['is_default'] or '0' }}";
		var _send_default = "{{ $address['is_send_default'] or '0'}}";
	</script>
@endsection
@section('page_js')
	<!--layer文件引入-->
	<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
	<script src="{{ config('app.source_url') }}mctsource/js/currency_9ps47mzo.js"></script>
@endsection

