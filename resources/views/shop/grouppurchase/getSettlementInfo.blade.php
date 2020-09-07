@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/getSettlementInfo.css"  media="screen">

@endsection

@section('main')
	<div class="waitPayOrder" id="app" v-cloak>
    <!--收货地址页面-->
    <div class="all_address" v-if="defaultAddress.length != 0 && no_logistics == 0">
    	<div class="shop_address flex_between" @click="addAddress">
	      <div class="shop_address_left flex_between">
	        <img src="{{ config('app.source_url') }}shop/images/dingwrei@2x.png" alt="">
	        <div class="address_detail">
	          <div>
	            <span v-text="defaultAddress.title"></span>
	            <span v-text="defaultAddress.phone">15070422584</span>	
	          </div>
	          <div>
	            <span>地址：</span>
	            <span v-text="defaultAddress.province.title"></span>
	            <span v-text="defaultAddress.city.title"></span>
	            <span v-text="defaultAddress.area.title"></span>&nbsp;&nbsp;
	            <span v-text="defaultAddress.address"></span>
	          </div>
	        </div>
	      </div>
	      <div class="shop_address_right">
	        <img src="{{ config('app.source_url') }}shop/images/jinru@2x.png" alt="">
	      </div>
	    </div>
    </div>
	    
    <!--添加新的收货地址-->
	    <div class="add_address" v-else-if="no_logistics == 0 && defaultAddress.length == 0">
	      <div class="flex_star header_add" @click="addAddress">
	        <img src="{{ config('app.source_url') }}shop/images/tj@2x.png" alt="">
	        <span>手动添加收货地址</span>
	      </div>
	    </div>
    <!--结算产品-->
	    <div class="wait_pay_product flex_between" @click="link_jump">
	      <div class="product_img">
	        <img :src="imgUrl + product_message.skuData.img" alt="">
	      </div>
	      <div class="product_desc">
	        <div v-text="product_message.title">
	          
	        </div>
	        <div v-if="product_message.sku_flag == 1">
	        	<span v-text="product_message.skuData.k1"></span><span v-if="product_message.skuData.k1">：</span>
	        	<span v-text="product_message.skuData.v1"></span>&nbsp;&nbsp;
	        	<span v-text="product_message.skuData.k2"></span><span v-if="product_message.skuData.k2">：</span>
	        	<span v-text="product_message.skuData.v2"></span>&nbsp;&nbsp;
	        	<span v-text="product_message.skuData.k3"></span><span v-if="product_message.skuData.k3">：</span>
	        	<span v-text="product_message.skuData.v3"></span>&nbsp;&nbsp;
	        </div>
	        <div>
	          	<span>￥</span><span v-text="price"></span>
	        </div>
	      </div>
	    </div>
    <!--购买数量-->
	    <div class="buy_num flex_between">
	      <div>购买数量</div>
	      <div>
	        <span @click="number_reduce">-</span>
	        <span v-text="number"></span>
	        <span @click="number_add">+</span>
	      </div>
	    </div>
    <!--立即付款-->
	    <div class="immediately_pay flex_between">
	      <div class="pay_price">
	        <div class="reality_price">
	          <span>实付款：&nbsp;&nbsp;<span style="color:#F72F37;">￥</span></span>
	          <span v-text="allprice"></span>
	        </div>
	        <div class="free_price">
	          	@{{freight>0?'运费：￥' + freight:'免运费'}}
	        </div>
	      </div>
	      <div class="pay_btn" @click="immediately_pay">
	      	<div>
	      		立即支付
	      	</div>
	      </div>
	    </div>
	    <!--实时信息-->
	    <div class="hint flex_star" v-if="topTipList != null">
		  <img :src="topTipList.headimgurl" alt="">
		  <span v-text="topTipList.nickname + '，' + topTipList.sec + '秒前拼单了这个商品'"></span>
		</div>
	    <!--放弃支付弹框-->
	    <div class="giveUp_price" v-if="isShowGiveUp">
	    	<div class="mask" @click="GiveUpPay"></div>
	    	<div class="payComment">
	    		<div>确定要放弃付款吗？</div>
	    		<div>
	    			你尚未完成支付 ,<br/>喜欢的商品可能会被抢购哦！
	    		</div>
	    		<div class="flex_around">
	    			<span @click="GiveUpPay">暂时放弃</span>
	    			<span @click="continuePay">继续支付</span>
	    		</div>
	    	</div>
	    </div>
	    <!--页面提示-->
	    <div class="prompting" v-text="hint" v-if="hint_show"></div>
	    <!--余额支付-->
	    <div class="sel-box" v-if="sel_show">
		    <div class='sel-mask' @click="sel_close"></div>
		    <nav class="sel-pay-wrap">
		    	<a href="javascript:;" id="yuerzf" @click="handleClick_yue($event)">储值余额支付（剩余￥<span v-text="balance? balance : '' "></span>）</a>
		    	<a href="javascript:;" id="alipay" @click="handleClick_alipay($event)" v-if="reqFrom=='aliapp'">支付宝支付</a>
				<a href="javascript:;" id="weixinzf" @click="handleClick_wec($event)" v-else>微信支付</a>
		    	<a href="javascript:;" id="baidupay" @click="handleClick_baidupay($event)" v-if="reqFrom=='baiduapp'">百度收银台支付</a>
	    	</nav>	    	
	    </div>
		<div class='sel-mask' v-if='payShow' @click='closePay'></div>
		<div v-if='payShow'>
			<nav class="sel-pay-wrap" v-if="reqFrom=='aliapp'">
				<a href="javascript:;" id="alipayYuerzf" @click='submitPay(1)'>储值余额支付（剩余￥@{{balanceMoney}}）</a>
				<a href="javascript:;" id="alipay" @click='submitPay'>支付宝支付</a>
			</nav>
			<nav class="sel-pay-wrap" v-else-if="reqFrom=='baiduapp'">
				<a href="javascript:;" id="alipayYuerzf" @click='submitPay(1)'>储值余额支付（剩余￥@{{balanceMoney}}）</a>
				<a href="javascript:;" id="alipay" @click='submitPay'>百度收银台支付</a>
			</nav>
			<nav class="sel-pay-wrap order_pay" v-else>
				<div class='order_pay_title pay_bottom'>选择支付方式</div>
				<div class="order_balance_pay pay_bottom" id="yuerzf" @click='selectPay(1)'>
					<div data-id="1">
						<div class="balance_img"></div>
						<a href="javascript:;">储值余额支付（剩余￥@{{balanceMoney}}）</a>
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
				<div class="confirm_btn" @click='submitPay'><p>确认</p></div>
			</nav>
		</div>
  </div>
  <script type="text/javascript">
  	var data1 = {!! json_encode($data)  !!};
  </script>
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var _token = $('meta[name="csrf-token"]').attr("content");
		var balance = "{{$balance}}"; //余额
		var reqFrom = "{{ $reqFrom }}";
	@if($reqFrom == 'aliapp')
	var url = location.href.split('#').toString();
	if(window.location.search){
		url += '&_pid_='+ '{{ session("mid") }}';
	}else{
		url += '?_pid_='+ '{{ session("mid") }}';
	}
	var xcx_share_url = url;
    var balance = "{{ $memberData['money']/100 }}"; //余额
	my.postMessage({share_title:'',share_desc:'',share_url:xcx_share_url,imgUrl:''});
	@endif
</script>
 @if($reqFrom == 'aliapp')
	<script type="text/javascript" src="https://appx/web-view.min.js"></script>
@endif
@if($reqFrom == 'baiduapp')
	<script type="text/javascript" src="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan.js"></script>
@endif
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/getSettlementInfo.js" ></script>
@endsection