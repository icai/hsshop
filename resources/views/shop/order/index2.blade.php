@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_list_2.css">
    <style type="text/css">
    	.goods_list .card_mi{
            display: inline-block;
            background: #F58E32;
            color: #fff;
            padding: 1px 2px;
            line-height: 18px;
            font-size: 11px !important;
        }
    </style>
@endsection
@section('main')
	<div class="orderList" v-cloak>
		<!--导航栏-->
		<div class="tabNav flex_between_v">
			<span :class="nav_index==index?'select':''" v-for="(item,index) in nav_bar" :key="item.name" :data-type="item.type" @click="onItemClick(item.type, index)" v-text="item.name"></span>
		</div>
		<!--商品列表-->
		<ul class="goods_list">
			<li v-for="(item,index) in list_data">
				<!--商品列表头部-->
				<div class="list_top flex_between_v">
					<p >
						<span v-text="item.weixin.shop_name"></span>
						<span class="card_mi" v-if="item.type==12">虚拟卡密</span>
					</p>
					@if($reqFrom == 'aliapp')
					<span class="status" v-text="item.statusName" v-if="item.statusName!='待评价'"></span>
					@else
					<span class="status" v-text="item.statusName"></span>
					@endif
				</div>
				<!--商品详情-->
				<div  class="list_detail">
					<div class="goods_info flex_between_v" @click="order_detail(item.groups_id, item.id, item.wid)">
						<img :src="imgUrl+''+item.orderDetail[0].img" width="100" />
						<div class="describe">
							<p class="goods_title" v-text="item.orderDetail[0].title" style=''></p>
							<p class="specification" v-text="item.orderDetail[0].spec"></p>
							<span class="ziti-tips" v-if="item.is_hexiao">自提</span>
						</div>
						<div class="num">
							<span>￥<span v-text="item.orderDetail[0].price"></span></span><br />
							<span style="color: #A2A2A2;">×<span v-text="item.orderDetail[0].num"></span></span>
						</div>
					</div>
					<div v-if="item.orderDetail.length > 1" class="look_more" @click="order_detail(item.groups_id, item.id, item.wid)">查看全部<span v-text="item.orderDetail.length"></span>件商品</div>
					<div class="goods_price">
						实付：￥<span v-text="item.pay_price"></span>（@{{item.freight_price > 0?"运费:￥"+item.freight_price:"免运费"}}）
					</div>
				</div>
				<!--商品列表功能按钮-->
				<div class="list_fun">
					<div v-if="item.btn_cancle" class="btn" @click="cancle(item.wid, item.id, index)">取消订单</div>
					<div v-if="item.btn_pay" class="btn Bred" @click="pay(item.groups_id, item.id)">付款</div>
					<div v-if="item.btn_buyAgain" class="btn" @click="buyagain(item.groups_id, item.orderDetail[0].product_id, item.wid, item.rule_id)">再次购买</div>
					<div v-if="item.btn_invite" class="btn Bred" @click="invite(item.groups_id, item.wid, item.groups_status)">邀请好友拼团</div>
					<div v-if="item.btn_delay" class="btn" @click="delay(item.id, item.wid)">延长收货</div>
					<div v-if="item.btn_logistics" class="btn" @click="logistics(item.id, item.wid, item.no_express, item.groups_id)">查看物流</div>
					<div v-if="item.btn_confirm" class="btn Bred" @click="confirm(item.refund_status, item.id, item.wid, index)">确认收货</div>
					@if($reqFrom != 'aliapp')
					<div v-if="item.btn_appraise" class="btn Bred" @click="appraise(item.id, item.wid)">立即评价</div>
					@endif
					<div v-if="item.btn_more" class="btn" @click="more(item.groups_id, item.id)">更多</div>
				</div>				
			</li>
		</ul>
		<!--没有订单数据的情况-->
		<div v-if="no_data" class="noGoods">
			<div class="imgDiv">
				<img src="{{ config('app.source_url') }}shop/images/no-order.png"/>
			</div>
			<p class="no-order-tips">您还没有相关的订单</p>
			<div class="no-order-link">
				<a href="{{ config('app.url') }}shop/index/{{session('wid')}}">去首页看看</a>
			</div>
		</div>
		<!--加载更多提示-->
		<div v-else class="loadMore" v-text="moreHint"></div>
		<!--toast提示框-->
		<div v-if="toastShow" class="toast" v-html="toastText"></div>
		<!--confirm提示框-->
		<div v-if="confirmShow" class="confirDiv">
			<div class="confirmBoard" @click="confirmShow = false"></div>
			<div class="confirmContent">
				<p class="confirmTitle" v-text="confirmTitle"></p>
				<p class="confirmBtns flex_around_v">
					<span @click="confirmShow = false">取消</span>
					<span @click="confirmSure">确定</span>
				</p>
			</div>
		</div>
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var wid = {{session('wid')}};
        var imgUrl = "{{ imgUrl() }}";
        var _token = $('meta[name="csrf-token"]').attr('content');
        var reqFrom = "{{$reqFrom}}";
        var takeAwayConfig = "{{$takeAwayConfig}}";
    </script>
    
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/order_index2.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <script type="text/javascript">
    </script>
@endsection
