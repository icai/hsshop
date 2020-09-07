@extends('shop.common.marketing')
	@section('title', '会员设置')
	@section('head_css')
		<!-- <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/member/hsadmin/image/icon_totuan2@2x.png" /> -->
		<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base1.css">
		<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/set.css" media="screen">
	@endsection

	@section('main')
		<form class="content">
			<div class="user-info">
				<ul class="weui-cells weui-cells-form js-user-form">
					<li class="weui-cell box_bottom_1px">
						<div class="weui-cell-hd">
							<label for="name" class="weui-label text-danger">姓名</label>
						</div>
						<div class="weui-cell-bd weui-cell-primary">
							<input id="name" type="text" name="name" class="weui-input" value="{{$member['truename']}}" placeholder="请输入姓名" />
						</div>

					</li>
					<li class="weui-cell after box_bottom_1px">
						<div class="weui-cell-hd">
							<label for="birthday" class="weui-label text-danger">生日</label>
						</div>
						<div class="weui-cell-bd weui-cell-primary text-info">
							<input type="date" id="birthday" name="birthday" class="weui-input" value="{{$member['birthday']}}" />
						</div>
					</li>
					<li class="weui-cell weui-cell-select box_bottom_1px">
						<div class="weui-cell-hd">
							<label for="gender" class="weui-label text-danger">性别</label>
						</div>
						<div class="weui-cell-bd weui-cell-primary">
							<select name="gender" id="gender" class="weui-select text-info">
								<option value="">请选择</option>
								<option @if($member['sex'] == 1) selected="selected" @endif value="1">男</option>
								<option @if($member['sex'] == 2) selected="selected" @endif  value="2">女</option>
							</select>
						</div>
					</li>
					@if($reqFrom == 'wechat')
					<li class="weui-cell box_bottom_1px">
						<div class="weui-cell-hd">
							<label for="weixin" class="weui-label text-danger">微信号</label>
						</div>
						<div class="weui-cell-bd weui-cell-primary">
							<input name="weixin" type="text" id="weixin" class="weui-input" autocomplete="off" value="{{$member['wechat_id']}}" placeholder="请输入微信号" />
						</div>
					</li>
					@endif
					{{--<li class="weui-cell">
						<div class="weui-cell-hd">
							<label class="weui-label text-danger">手机号</label>
						</div>
						<div class="weui-cell-bd weui-cell-primary">
							<input name="mobile" type="tel" disabled="disabled" class="weui-input" value="{{ $member['mobile'] or ''}}"/>
						</div>
					</li>--}}
					<li class="weui-cell address-fm">
						<div class="weui-cell-hd">
							<label class="c-black weui-label text-danger">所在地</label>
						</div>
						<div class="weui-cell-bd weui-cell-primary">
							<div class="js-area-layout area-layout" data-area-code="">
								<span>
		                            <select name="member_province" class="js-province address-province text-info">
		                            	<option value=''>选择省份</option>
		                            	@foreach($provinceList as $pro)
		                            	<option @if($pro['id'] == $member['province_id']) selected="selected" @endif value="{{ $pro['id'] }}"> {{ $pro['title'] }}</option>
		                            	@endforeach
		                            </select>
		                        </span>
								<span>
		                            <select name="member_city" class="js-city address-city text-info">
		                            	<option value=''>选择城市</option>
										@if(isset($regionList[$member['province_id']]))
											@forelse($regionList[$member['province_id']] as $val)
												<option @if($val['id'] == $member['city_id']) selected="selected" @endif value="{{$val['id']}}" >{{$val['title']}}</option>
											@endforeach
										@endif
		                            </select>
		                        </span>
								<span>
		                            <select name="member_county" class="js-county address-county text-info">
		                            	<option value=''>选择地区</option>
										@if(isset($regionList[$member['city_id']]))
											@forelse($regionList[$member['city_id']] as $val)
												<option @if($val['id'] == $member['area_id']) selected="selected" @endif value="{{$val['id']}}" >{{$val['title']}}</option>
											@endforeach
										@endif
		                            </select>
		                        </span>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="btn-block">
				<button class="btn btn-green btn-block js-save-info">保存</button>
			</div>
		</form>
	@endsection
@section('page_js')
	<script type="text/javascript">
		var json = {!! $regions_data !!};  
        var imgUrl = "{{ imgUrl() }}";
		// json = JSON.parse('[1, 5, "false"]');
		var reqFrom = "{{ $reqFrom }}";
	</script>
	<script src="{{ config('app.source_url') }}shop/js/until.js" ></script>
	<script src="{{ config('app.source_url') }}shop/js/set.js" ></script>
@endsection