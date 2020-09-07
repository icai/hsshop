@extends('shop.common.marketing')
@section('head_css')
	<link rel="shortcut icon" href="{{ config('app.source_url') }}shop/images/icon_totuan2@2x.png" />
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base1.css" media="screen">
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cardActive.css" media="screen">
@endsection
@section('main')
	<div class="container " style="min-height: 620px;">

		<div class="content">
			<div class="user-info">
				<div class="logo" style="background-image:url({{$member['headimgurl']}})"></div>
				<h4 class="js-nickname" data-nickname="{{$member['truename']}}">{{$member['truename']}}</h4>
			</div>
			<p class="phone-tip">已获得该会员卡，请填写会员资料</p>
			<form id="myform">
				<input type="hidden" name="tag" value="1" />
				<input type="hidden" name="record_id" value="{{$record_id}}" />
				<div class="detail-activate">
					<ul class="weui-cells weui-cells-form">
						<li class="weui-cell">
							<div class="weui-cell-hd">
								<label for="name" class="weui-label"><i class="require">*</i>姓名</label>
							</div>
							<div class="weui-cell-bd weui-cell-primary">
								<input id="name" type="text" name="name" class="weui-input" value="{{$member['truename']}}" placeholder="请输入姓名" />
							</div>
						</li>
						<li class="weui-cell">
							<div class="weui-cell-hd">
								<label class="weui-label"><i class="require">*</i>手机号</label>
							</div>
							<div class="weui-cell-bd weui-cell-primary">
								<input name="mobile" type="tel" class="weui-input" id="phoneNum" autocomplete="off" value="{{$member['mobile']}}" maxlength="11"  placeholder="请输入手机号" />
							</div>
						</li>
						<li class="weui-cell weui-cell-select">
							<div class="weui-cell-hd">
								<label for="gender" class="weui-label">性别</label>
							</div>
							<div class="weui-cell-bd weui-cell-primary">
								<select name="gender" id="gender" class="weui-select">
									<option value="0">请选择</option>
									<option @if($member['sex'] == 1) selected="selected" @endif value="1">男</option>
									<option @if($member['sex'] == 2) selected="selected" @endif value="2">女</option>
								</select>
							</div>
						</li>
						<li class="weui-cell">
							<div class="weui-cell-hd">
								<label for="weixin" class="weui-label">微信号</label>
							</div>
							<div class="weui-cell-bd weui-cell-primary">
								<input name="weixin" type="text" id="weixin" class="weui-input"  autocomplete="off" value="{{$member['wechat_id']}}" placeholder="请输入微信号" />
							</div>
						</li>
						<li class="weui-cell">
							<div class="weui-cell-hd">
								<label for="birthday" class="weui-label">生日</label>
							</div>
							<div class="weui-cell-bd weui-cell-primary">
								<input type="date" id="birthday" name="birthday" class="weui-input" data-value="{{$member['birthday']}}" value="{{$member['birthday']}}" />
							</div>
						</li>
						<li class="weui-cell address-fm">
							<div class="weui-cell-hd">
								<label class="c-black weui-label">所在地</label>
							</div>
							<div class="weui-cell-bd weui-cell-primary">
								<div class="js-area-layout area-layout" data-area-code="">
									<span>
			                            <select name="member_province" class="js-province address-province">
			                            	<option value=''>选择省份</option>
											@foreach($provinceList as $pro)
												<option @if($pro['id'] == $member['province_id']) selected="selected" @endif value="{{ $pro['id'] }}"> {{ $pro['title'] }}</option>
											@endforeach
			                            </select>
			                        </span>
									<span>
			                            <select name="member_city" class="js-city address-city">
			                            	<option value=''>选择城市</option>
											@if(isset($regionList[$member['province_id']]))
												@forelse($regionList[$member['province_id']] as $val)
													<option @if($val['id'] == $member['city_id']) selected="selected" @endif value="{{$val['id']}}" >{{$val['title']}}</option>
													@endforeach
													@endif
			                            </select>
			                        </span>
									<span>
			                            <select name="member_county" class="js-county address-county">
			                            	<option value=''>选择地区</option>
											@if(isset($regionList[$member['city_id']]))
												@forelse($regionList[$member['city_id']] as $val)
													<option @if($val['id'] == $member['area_id']) selected="selected" @endif value="{{$val['id']}}">{{$val['title']}}</option>
													@endforeach
													@endif
			                            </select>
			                        </span>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</form>
			<div class="btn-block">
				<button class="btn btn-green btn-block btn-activate-card js-activate-card" data-card-no="237598312904587364">立即激活会员卡</button>
			</div>
		</div>
	</div>
	<script type="text/javascript">
        var json = {!! $regions_data !!};
	</script>
@endsection
@section('page_js')
	<script type="text/javascript"> 
        var imgUrl = "{{ imgUrl() }}";
	</script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/cardActive.js" type="text/javascript" charset="utf-8"></script>
@endsection


