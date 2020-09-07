@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/safeguard_1c5f2b2c598b88d2eceffd92bf496cfe.css">
@endsection
@section('main')
    <div class="container ">
    	<!-- 拼团不支持退款申请提示 -->
    	<div class="container not-support-container hide" style="min-height: 667px;">
		    <i class="not-support-cry"></i>
		    <div class="not-support-tip">
		        <p>
		            未成团订单不支持退款申请
		        </p>
		        <p class="font-size-12 c-gray-dark">
		            开团后的24小内未达到人数要求，订单将关闭并退款
		        </p>
		    </div>
		    <div class="action-container">
		        <a href="/shop/grouppurchase/groupon" class="btn btn-block btn-green">
		            查看团详情
		        </a>
		        <a href="javascript:history.back();" class="btn btn-block btn-white">
		            返回
		        </a>
		    </div>
		</div>
		<div class="content clearfix">
			<ul class="safe-block order-info block form">
				<li class="block-item font-size-14">
					<p class="">
						<i class="">商品名称</i>
						<i class="pull-right">{{$product['title']}}</i>
					</p>
					<p class="">
						<i class="">商品金额</i>
						<i class="pull-right c-orange">￥{{$refundAmountMax}}</i>
					</p>
					<p class="">
						<i class="">订单编号</i>
						<i class="pull-right">{{$order['oid']}}</i>
					</p>
					<p class="">
						<i class="">交易时间</i>
						<i class="pull-right">{{$order['created_at']}}</i>
					</p>
				</li>
			</ul>
			<form class="js-apply-form" id="applyForm" method="post" enctype="multipart/form-data" onsubmit="return false">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
				<ul class="block form">
					<li class="block-item arrow">
						<label class="">处理方式</label>
						<select class="js-method font-size-14 line-h" name="data[type]" style="height: 40px;">
							<option value="">请选择处理方式</option>
							<option value="0">仅退款 </option>
						</select>
					</li>
					@if(isset($order['is_hexiao']) &&  $order['is_hexiao'] != 1)
					<li class="js-express-block block-item arrow">
						<label class="">货物状态</label>
						<select class="js-express font-size-14" name="data[order_status]" style="height: 40px;">
							<option value="0">未收到货</option>
							<option value="1">已收到货</option>
						</select>
					</li>
					@endif
					<li class="block-item arrow">
						<label class="">退款原因</label>
						<select class="js-reason font-size-14 line-h" name="data[reason]" style="height: 40px;">
                            <option value="">请选择退款原因</option>
                            @if(isset($order['is_hexiao']) &&  $order['is_hexiao'] != 1)
							<option value="1">配送信息错误</option>
							@endif
							<option value="2">买错商品</option>
							<option value="3">不想买了</option>
							<option value="0">其他</option>
						</select>
					</li>
					<li class="block-item">
						<label class="">退款金额</label>
                        <input type="hidden" name="data[amount]" value="{{$order['pay_price']}}"/>
						<input type="text" class="js-money font-size-14" name="data[amount]" min="0" value="{{$refundAmountMax}}" disabled="disabled" placeholder="最多可退款0.00元">
						<p class="c-gray font-size-12 js-luluckmoney-region hide">内含<span class="js-luckmoney-money" style="display: inline;"></span>元红包不予退还，实际退款金额<span class="c-red js-real-money" style="display: inline;"></span>元。</p>
					</li>
					<li class="block-item">
						<label class="">手机号码</label>
						<input type="tel" class="js-tel font-size-14" name="data[phone]" value="" placeholder="填写手机号便于商家联系您">
					</li>
					<li class="block-item">
						<label class=" vertical-top">备注信息</label>
						<textarea name="data[remark]" rows="1" class="js-message message font-size-14" placeholder="最多可填写200个字"></textarea>
					</li>

                    <div id="imgDiv"></div>

                    <li class="block-item item-picture-upload">
                    	<label class="vertical-top">图片举证</label>
                    	<div class="picture-detail">
                    		<p class="c-gray-dark font-size-12">可上传3张图片</p>
                    		<p class="js-input-container multi-uploader uploader" style="display: flex;">
                    			<span class="js-add-picture add-wrapper picture-wrapper" style="cursor: pointer">
	                    			
			                    </span>
                    		</p>
                    	</div>
                	</li>					
				</ul>
				<div class="action-container">
					<button id="submitApply" class="js-submit btn btn-block btn-green">提交</button>
				</div>

			</form>
			<form name="form1" id="form1" enctype="multipart/form-data" method="post" style="opacity: 0;">
            	<input type="file" class="add-picture" id="upload_img" name="file" style="width: 1px;height: 1px;">
			</form>
		</div>
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var wid = '{{$wid}}';
        var oid = '{{$order['id']}}';
        var pid = '{{$pid}}';
        var imgUrl = "{{ imgUrl() }}";
        console.log(wid, oid, pid)
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/order_refundApply.js"></script>
@endsection
