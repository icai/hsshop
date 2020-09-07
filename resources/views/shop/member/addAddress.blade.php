@extends('shop.common.template')
@section('title', $title)
@section('head_css')
	<script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/addAddress.css"  media="screen">
@endsection
@section('main')  
	<div class="newsAddress" id="app" v-cloak>
		<!--省市区三级联动-->
		<div class="address_city address_detail" @click='showAddressModel()'>
			<div v-text="cityAddress" :style="cityColor == 1 ? 'color:#333333': ''">
				详细地址 , 如街道 , 门牌详细地址 , 如街道 , 门牌号等详细地址 , 如街道 , 门牌详细地址 , 如街道 , 门牌号等
			</div>
		</div>
		<div class="address_detail">
			<input type="text" placeholder="详细地址 , 如街道 , 门牌号等" v-model="address_detail"/>
		</div>
		<div class="name">
			<input class='inp_box' type="text" placeholder="姓名" v-model="name" />
		</div>
		<div class="phone">
			<input class='inp_box' type="text" placeholder="电话" v-model="phone" />
		</div>
		<div>
			<div class="flex_center" @click="selected_default">
				<img :src="default_img" alt="" >
				<span>设为默认地址</span>
			</div>
		</div>
		<div class="confirm_agree confirm_btn" v-show="isShow">
			<div><a href="javascript:history.go(-1)">取消</a></div>
			<div @click="confirm_agree">确认</div>
		</div>
		<div class="hint" v-text="hint" v-if="show_hint"></div>
		<div class="select_address" v-if='addressModel' @click='choseAddressModel()'>
			<div class='address'>
				<div class="address_title">
					<div>
						<span @click.stop='show_province(0)' v-if="spanShow.span_a == 0" :class='spanActive == 0 ?"active_span": ""' v-text="province_name"></span>
						<span @click.stop='show_province(1)' v-if="spanShow.span_b == 1" :class='spanActive == 1 ?"active_span": ""' v-text="city_name"></span>
						<span @click.stop='show_province(2)' v-if="spanShow.span_c == 2" :class='spanActive == 2 ?"active_span": ""' v-text="area_name"></span>
					</div>
					<div @click.stop="getAddress()">确定</div>
				</div>
				<div class='address_sel_list'>
					<div v-if="spanActive == 0">
						<ul name="province_id">
							<li v-for="(val, index) in province_data" :value="val.id" v-text="val.title" @click.stop="change_province_name(val.title,val.id)" :class="province_name == val.title ? 'red' : '' "></li>
						</ul>
					</div>
					<div v-if="spanActive == 1">
						<ul name="city_id">
							<li v-for="(val, index) in city_data"  :value="val.id" v-text="val.title" @click.stop="change_city_name(val.title,val.id)" :class="city_name == val.title ? 'red' : '' "></li>
						</ul>
					</div>
					<div v-if="spanActive == 2">
						<ul name="area_id">
							<li v-for="(val, index) in area_data"  :value="val.id" v-text="val.title"  @click.stop="change_area_name(val.title,val.id)" :class="area_name == val.title ? 'red' : '' "></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class='footer_box' v-show="isShow">
			@include('shop.common.footer')
		</div>
	</div>

@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var regionList = {!! json_encode($regionList) !!}
    var _token = $('meta[name="csrf-token"]').attr("content");
    //编辑时的数据
	var addressData = '';
	@if($addressData)
		addressData = {!! json_encode($addressData) !!}
	@endif
	console.log(addressData)
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/addAddress.js"></script>
@endsection