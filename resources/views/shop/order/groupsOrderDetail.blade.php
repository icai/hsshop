@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/group_orderDetail.css?v=1.0">
@endsection
@section('main')
<div class="content" id="page" v-cloak style="width: 100%;min-height: 100%;">
	<div class="content_top flex_start_v">
		<img :src="[[host + add_field.img]]" class="icon">
		<div class="order_status_content">
			<p class="status_content1">[[add_field.title]] <span v-if='span_show' style="margin-left: 15px">[[order_m]]:[[order_s]]</span></p>
			<p class="status_content2" v-if="add_field.info">[[add_field.info]]</p>
		</div>
	</div>
	<div class="wuliu_express" v-if="show_no_express == 1 && pageData.orderDetail.address_id">
		<img src="{{ config('app.source_url') }}shop/static/images/wuliu@2x.png"/>
		无需物流
	</div>
	<a class="logistics_info flex_between_v" v-if="logistics && logisticsInfo > 0" :href="'/shop/order/expresslist/'+orderDetail.wid+'/'+orderDetail.id">
		<img v-cloak class="icon" src="{{ config('app.source_url') }}shop/static/images/wuliu@2x.png">
		<div class="logistics_msg" v-if="logisticsInfo[0].data.length > 0">
			<p>[[logisticsInfo[0].data[0].context]]</p>
			<p>[[logisticsInfo[0].data[0].time]]</p>
		</div>
		<img v-cloak class="arrow" src="{{ config('app.source_url') }}shop/static/images/arrow@2x.png">
	</a>
	<div class="address flex_between_v" v-if="pageData.orderDetail.address_id">
		<img v-cloak class="icon" src="{{ config('app.source_url') }}shop/static/images/dizhi@2x.png">
		<div class="address_info">
			<p>收货人：[[orderDetail.address_name]] &nbsp;&nbsp;&nbsp; [[orderDetail.address_phone]]</p>
			<p>地址：[[orderDetail.address_detail]]</p>
		</div>
	</div>
	<div class="goods_msg">
		<div class="list_top flex_between_v">
			<a>[[orderDetail.shop_name]]</a>
			<span class="status">[[orderDetail.statusText]]</span>
		</div>
		<div class="list_detail" v-if="group_info">
			<a class="goods_info" v-for="item in orderDetail.orderDetail" :href="'/shop/grouppurchase/detail/'+group_info.rule_id+'/'+orderDetail.wid">
				<div class="flex_between_v">
					<img :src="[[imgUrl + item.img]]"/>
					<div class="describe">
						<p class="goods_title">[[item.title]]</p>
						<p class="specification">[[item.spec]]</p>
						<span class="change_goods" style="visibility:hidden">七天退换</span>
					</div>
					<div class="num">
						<span>￥[[orderDetail.products_price - orderDetail.head_discount]]</span>
						<span style="text-decoration: line-through;color: rgb(162, 162, 162);font-size: 13px;">￥[[item.oprice]]</span>
						<span style="color: rgb(162, 162, 162);margin-top: 25px;">×[[item.num]]</span>
					</div>
				</div>
			</a>

			<div class="tuan_fun" v-if="orderDetail.statusText != '待支付' && orderDetail.statusText != '交易已取消'">
				<a class="btn" :href="'/shop/grouppurchase/groupon/'+orderDetail.groups_id+'/'+orderDetail.wid+'?group_type='+group_status">团详情</a>
				<div class="refundBtn" style="display: inline-block;" v-if="isRefundBtnShow">
					<a class="btn Bred" v-if="orderDetail.pay_price !== '0.00' && orderDetail.refundOrder === 0" :href="orderDetail.status == 1?'/shop/order/refundApplyView/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/0/'+orderDetail.orderDetail[0].product_prop_id+'?type=1':'/shop/order/refundApplyType/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/0/'+orderDetail.orderDetail[0].product_prop_id">申请退款</a>
					<a class="btn Bred" v-if="orderDetail.refundOrder === 1" :href="'/shop/order/refundDetailView/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/'+orderDetail.orderDetail[0].product_prop_id">退款处理中</a>
					<a class="btn Bred" v-if="orderDetail.refundOrder === 2" :href="'/shop/order/refundDetailView/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/'+orderDetail.orderDetail[0].product_prop_id">退款中</a>
					<a class="btn Bred" v-if="orderDetail.refundOrder === 3" :href="orderDetail.status == 1?'/shop/order/refundApplyView/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/1/'+orderDetail.orderDetail[0].product_prop_id+'?type=1':'/shop/order/refundApplyType/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/1/'+orderDetail.orderDetail[0].product_prop_id">申请退款</a>
					<a class="btn Bred" v-if="orderDetail.refundOrder === 4" :href="'/shop/order/refundDetailView/'+orderDetail.wid+'/'+orderDetail.id+'/'+orderDetail.orderDetail[0].product_id+'/'+orderDetail.orderDetail[0].product_prop_id">退款成功</a>
				</div>
			</div>
			<div class="post flex_between_v" v-if="orderDetail.statusText == '待支付'">
				<span>快递邮费：</span>
				<span>￥[[orderDetail.freight_price]]</span>
			</div>

			<div v-if="orderDetail.statusText == '待支付'">
				<div class="goods_price flex_between_v">
					<span>需支付：</span>
					<span>￥[[orderDetail.pay_price]]</span>
				</div>
				<button @click="immediately_pay">去支付</button>
			</div>
			<button v-if="orderDetail.statusText == '待收货' || orderDetail.statusText == '已中奖，待收货'" @click="sureOrder(orderDetail.refund_status,orderDetail.id,orderDetail.wid)">确认收货</button>
			<button v-if="orderDetail.statusText == '拼团中'" @click="getShare()">邀请好友拼团</button>
			<button v-if="orderDetail.statusText == '待评价' || orderDetail.statusText == '已中奖，待评价'" @click="appraise(orderDetail.orderDetail[0].id,orderDetail.wid)">立即评价</button>
		</div>
		<!-- 订单信息开始 -->
		<div class="order_num">
			<p>订单编号： [[orderDetail.oid]]
				<span :data-clipboard-text="orderDetail.oid" class="copy" style="cursor: pointer;" @click="">复制</span>
			</p>
			<p v-if="orderDetail.pay_way != 0">支付方式： [[orderDetail.pay_way == 1?"微信支付":"余额支付"]]</p>
			<p>下单时间： [[orderDetail.created_at]]</p>
			<p v-if="group_info && group_info.complete_time && group_info.complete_time!='0000-00-00 00:00:00'">成团时间： [[group_info.complete_time]]</p>
			<p v-if="orderDetail.deliver">发货时间： [[orderDetail.deliver]]</p>
			<p v-if="logisticsInfo.length > 0">快递方式:[[logisticsInfo[0].com]]</p>
			<p v-if="logisticsInfo.length > 0">运单编号:[[logisticsInfo[0].nu]]
				<span :data-clipboard-text="logisticsInfo[0].nu" class="copy copy1" style="cursor: pointer;" @click="">复制</span>
			</p>
		</div>
		<!-- 订单信息结束 -->
		<!-- 猜你喜欢开始 -->
		<div class="gruess_u_like">
			<div class="u-like-title">
				<div class="u-like-line"></div>
				<div class="u-like-icon"></div>
				<p class="u-like-tips">为您推荐</p>
				<div class="u-like-line"></div>
			</div>
			<ul class="clearfix">
				<li class="fl" v-for= "item in recommendGroups">
					<a style="border: none" :href="'/shop/grouppurchase/detail/'+item.id+'/'+item.wid">
						<img :src="imgUrl+item.img"/>
						<p class="like_goods_title">[[item.title]]</p>
						<div class="flex_between_v" style="height: 25px;padding: 0 10px;">
							<span style="color: #F72F37;font-size: 16px;"><span style="font-size: 12px;">￥</span>[[item.min]]</span>
							<div class="imgGroup">
								<img :src="ite.headimgurl" v-for=" (ite,ind) in item.groups.member" v-if="ind < 2">
							</div>
						</div>
					</a>
				</li>
			</ul>
		</div>
		<!-- 猜你喜欢结束 -->
		<!-- 订单操作开始 -->
		<div class="order_fun">
			<span class="btn" v-if="orderDetail.statusText == '待支付'" @click=cancle(orderDetail.wid,orderDetail.id)>取消订单</span>
			<span class="btn" v-if="orderDetail.statusText == '待收货'|| orderDetail.statusText == '已中奖，待收货'" @click="delay(orderDetail.id,orderDetail.wid)">延长收货</span>
			<a class="btn" v-show='!span_show' v-if="orderDetail.no_express == 0 && pageData.orderDetail.address_id" :href="'/shop/order/expresslist/'+orderDetail.wid+'/'+orderDetail.id">查看物流</a>
			<a class="btn Bred" v-if="(orderDetail.statusText != '待支付' || orderDetail.statusText != '待收货'|| orderDetail.statusText != '已中奖，待收货') && group_info" :href="'/shop/grouppurchase/detail/'+group_info.rule_id+'/'+orderDetail.wid">再次购买</a>
		</div>
		<!-- 订单操作结束 -->
	</div>
	<!-- 分享mask -->
    <div class="zhezhao" v-if="shareShow">
        <div class="share_model">
            <img src="{{ config('app.source_url') }}shop/images/share_bg3.png" />
        </div>
        <div class="close_share" @click="shareHide"></div>
    </div>
	<!-- 页面加载 -->
	<div class="pageMask" v-if="!pageShow">
	    <img class="pageLoading" src="{{ config('app.source_url') }}/shop/static/images/loading.gif">
	</div>
	<div class='sel-mask' v-if='payShow' @click='closePay'></div>
	<div v-if='payShow'>
		<nav class="sel-pay-wrap" v-if="reqFrom=='aliapp'">
			<a href="javascript:;" id="alipayYuerzf" @click.stop='goBuy(orderDetail.id, 1)'>储值余额支付（剩余￥[[balanceMoney]]）</a>
			<a href="javascript:;" id="alipay" @click.stop='goBuy(orderDetail.id, 2)'>支付宝支付</a>
		</nav>
		<nav class="sel-pay-wrap" v-else-if="reqFrom=='baiduapp'">
			<a href="javascript:;" id="alipayYuerzf" @click.stop='goBuy(orderDetail.id, 1)'>储值余额支付（剩余￥[[balanceMoney]]）</a>
			<a href="javascript:;" id="alipay" @click.stop='goBuy(orderDetail.id, 2)'>百度收银台支付</a>
		</nav>
		<nav class="sel-pay-wrap order_pay" v-else>
			<div class='order_pay_title pay_bottom'>选择支付方式</div>
			<div class="order_balance_pay pay_bottom" id="yuerzf" @click='selectPay(1)'>
				<div data-id="1">
					<div class="balance_img"></div>
					<a href="javascript:;">储值余额支付（剩余￥[[balanceMoney]]）</a>
				</div>
				<div class="order_pay_way" data-id="2">
					<div class="ap-weixuan" :class='{"hide": selectPayTypeOff == 1}'></div>
					<div class="dui" :class='{"hide": selectPayType == 2}'></div>
				</div>
			</div>
			<div class="order_balance_pay pay_bottom" id="weixinzf" @click='selectPay(2)'>
				<div data-id="2">
					<div class="balance_img weixin_img"></div>
					<a href="javascript:;">微信支付</a>
				</div>
				<div class="order_pay_way" data-id="1">
					<div class="ap-weixuan" :class='{"hide": selectPayTypeOff == 2}'></div>
					<div class="dui" :class='{"hide": selectPayType == 1}'></div>
				</div>
			</div>
			<div class="confirm_btn" @click.stop='goBuy(orderDetail.id)'><p>确认</p></div>
		</nav>
	</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script>
		var reqFrom = "{{ $reqFrom }}";
	</script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/clipboard.min.js"></script>
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	@if($reqFrom == 'aliapp')
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
	@if($reqFrom == 'baiduapp')
		<script type="text/javascript" src="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan.js"></script>
	@endif
    <script type="text/javascript">
        var _host = "{{ config('app.source_url') }}";//静态资源
		var host ="{{ config('app.url') }}";//网址域名
		var imgUrl = "{{ imgUrl() }}";//动态图片地址
		var pageData = {!!json_encode($data)!!};//页面数据
        var share_title="";//分享标题
		var share_img="";//分享图片
		var share_desc="";//分享描述
        var balance = pageData.memberData.money / 100; //余额
		var share_url="";
		// 微信分享
        function wsshare(){
            var url = location.href.split('#').toString();
            $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
                if(data.errCode == 0){
                    wx.config({
                        debug: false, 
                        appId: data.data.appId, 
                        timestamp: data.data.timestamp, 
                        nonceStr: data.data.nonceStr, 
                        signature: data.data.signature,
                        jsApiList: [
                            'checkJsApi',
                            'onMenuShareTimeline',
                            'onMenuShareAppMessage',
                            'onMenuShareQQ',
                            'chooseWXPay'
                        ] 
                    });

                }
            })

            wx.ready(function () {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: share_title, 
                    desc: share_desc, 
                    link: share_url, 
                    imgUrl: host+share_img, 
                    success: function () {
                        
                    },
                    cancel: function () {
                        
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: share_title, 
                    desc: share_desc, 
                    link: share_url, 
                    imgUrl: host+share_img, 
                    type: '', 
                    dataUrl: '', 
                    success: function () {
                        
                    },
                    cancel: function () {
                        
                    }
                });

                //分享到QQ
                wx.onMenuShareQQ({
                    title: share_title, 
                    desc: share_desc, 
                    link: share_url, 
                    imgUrl: host+share_img, 
                    success: function () {
                       
                    },
                    cancel: function () {
                       
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: share_title, 
                    desc: share_desc, 
                    link: share_url, 
                    imgUrl: host+share_img, 
                    success: function () {
                       
                    },
                    cancel: function () {
                        
                    }
                });
                wx.error(function(res){
                    
                });
            });
        }
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/group_orderDetail.js"></script>
@endsection