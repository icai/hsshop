@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refundApplyView.css">
@endsection
@section('main')
	<div class="apply_afterSales" v-cloak>
		<div class="contents">
			<div class="order_msg">
				<p class="flex_between_v"><span>商品名称：</span><span style="overflow: hidden;text-overflow:ellipsis;white-space: nowrap;display:inline-block;width: 75%;text-align: right;" v-text="prouct_info.title"></span></p>
				<p class="flex_between_v"><span>商品金额：</span><span style="color: #E5333A;">￥<i v-text="refundAmountMax"></i></span></p>
				<p class="flex_between_v"><span>订单编号：</span><span v-text="order_info.trade_id"></span></p>
				<p class="flex_between_v"><span>交易时间：</span><span v-text="order_info.created_at"></span></p>
			</div>
			<div id="group">
		      	<p v-if="type==1" class="flex_between_v">
		      		<span>退款类型</span>
		      		<span>我要退款（无需退货）</span>
		      	</p>
		      	<p v-else class="flex_between_v">
		      		<span>退款类型</span>
		      		<span>我要退货退款</span>
		      	</p>
		      	<p class="flex_between_v">
		      		<span>退款原因</span>
		      		<span class="line_row">
		      			<select v-model="refundReason">
		      				<option disabled="disabled" value="-1">请选择退款原因</option>
		      				<option v-for="item in reasons" :value="item.val" v-text="item.name">未按承诺时间发货</option>
		      			</select>
		      		</span>
		      	</p>
		      	<p v-if="type==2" class="flex_between_v">
		      		<span>收货状态</span>
		      		<span class="line_row">
		      			<select v-model="goodsState">
		      				<option disabled="disabled" value="-1">点击选择货物状态</option>
		      				<option v-for="item in states" :value="item.val" v-text="item.name"></option>
		      			</select>
		      		</span>
		      	</p>
		      	<p class="flex_between_v">
		      		<span>退款金额</span>
		      		<span><input type="number" v-model="money" :placeholder="refundMoney" /></span>
		      	</p>
		      	<p>
		      		<span>退款说明</span>
		      		<span>(必填)</span>
		      	</p>
		      	<p id="text-area">
		      		<textarea maxlength="170" rows="5" cols="" v-model="explain" placeholder="请填写您的详细退款说明（最多170字）" @input="fontNum"></textarea>
		      		<span>还可输入<i v-text="font_num"></i>字</span>
		      	</p>
		      	<p class="flex_between_v">
		      		<span>联系电话</span>
		      		<span><input type="number" v-model="phoneNum" placeholder="请输入联系电话"/></span>
		      	</p>
		    </div>
		    <div id="group_2">	
		    	<p>
		    		<span>上传凭证（最多3张）</span>
		    	</p>
		    	<div class="upload_images flex_start_v">
		    		<div class="imgs_item" v-for="(item,index) in images">
		    			<img :src="imgUrl + item"/>
		    			<span class="delImg" @click="delImgIndex(index)">×</span>
		    		</div>
	                <div v-if="images.length<3" class="uploaderDiv">
	                    <input id="btnUp" @change="uploadImg" type="file" name="" class="absolute" value="" />
	                    <img src="{{ config('app.source_url') }}shop/images/xj.png" width="80" height="80"/>
	                </div>
	            </div>
		    	
		    </div>
		</div>
		<div v-if="btnTxt" class="submitDiv" style='padding: 10px 10px;'>
			<button @click="submit" v-text="btnTxt"></button>
		</div>
		<!--toast提示框-->
		<div v-if="toastShow" class="toast" v-html="toastText"></div>
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script type="text/javascript">
        var wid = {{session('wid')}};
        var oid = '{{$oid}}';
        var pid = '{{$pid}}';
        var isEdit = '{{$isEdit}}';
		var propID = '{{$propID}}';
        var imgUrl = "{{ imgUrl() }}";
        var _token = $('meta[name="csrf-token"]').attr('content');
    </script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/jquery-2.1.4.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/refundApplyView.js"></script>
@endsection