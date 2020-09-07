@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/safeguard_1c5f2b2c598b88d2eceffd92bf496cfe.css">
@endsection
@section('main')
    <div class="container " style="min-height: 736px;">
		<div class="content clearfix">
			<div class="content clearfix">
    		<div class="info-top-status font-size-14 text-center">
				<span class="">
                    @if($order['refund_status'] == 1)
                        等待商家处理退款申请
                    @elseif($order['refund_status'] == 2)
                        商家不同意退款申请
                    @elseif($order['refund_status'] == 5)
                        退款关闭
                    @elseif($order['refund_status'] == 3 || $order['refund_status'] == 4)
                        退款完成
                    @endif
                </span>
			</div>
        	<ul class="safe-block block block-list">

                @if($order['refund_status'] == 1)
                    <li class=" block-item">
                        <p class="">
                            <span class="">如商家同意：</span>
                            <span class="">申请将达成并退款给您</span>
                        </p>
                        <p class="">
                            <span class="">如商家拒绝：</span>
                            <span class="">你需要重新修改退款申请或申请维权</span>
                        </p>
                    </li>
                    <li class="block-item c-gray-dark">
                        若商家在
					<span class="js-time-counter" data-countdown="604791" style="display: inline;">
						<span class="c-orange"></span>
					</span>
                        内未处理，则申请达成并退款给您
                    </li>
                @elseif($order['refund_status'] == 2)
                    <li class=" block-item">
                        <p class="">
                            <span class="">拒绝理由：</span>
                            <span class="">{{$refund['remark']}}</span>
                        </p>
                    </li>
                @elseif($order['refund_status'] == 5)
                    <li class=" block-item">
                        <p class="">
                            <span class="">退款关闭：</span>
                            <span class="">买家主动撤销退款</span>
                        </p>
                        <p class="">
                            <span class="">结束时间：</span>
                            <span class="">{{$refund['updated_at']}}</span>
                        </p>
                    </li>
                    <li class="block-item c-gray-dark">
                        您已主动关闭退款申请，无法再次发起退款，如有疑问请联系商家协商处理。
                    </li>
                @elseif($order['refund_status'] == 3 || $order['refund_status'] == 4)
                    <li class=" block-item">
                        <p class="">
                            <span class="">退款金额：</span>
                            <span class="">￥{{$refund['amount']}}</span>
                        </p>
                        <p class="">
                            <span class="">退款时间：</span>
                            <span class="">{{$refund['updated_at']}}</span>
                        </p>
                    </li>
                @endif

			</ul>  
		    <div class="safe-block block-safeguard-info block block-list font-size-14">
				<div class="block-item">
					<p class=""> 
						<i class="">退款编号:</i>
						<i class="pull-right c-black">{{$refund['id']}}</i>
					</p>
					<p class=""> 
						<i class="">申请时间:</i>
						<i class="pull-right c-black">{{$refund['created_at']}}</i>
					</p>
					<p class=""> 
						<i class="">退款原因:</i>
						<i class="pull-right c-black">
                            @if ($refund['reason'] == 0)
                                其他
                            @elseif ($refund['reason'] == 1)
                                配送信息错误
                            @elseif ($refund['reason'] == 2)
                                买错商品
                            @elseif ($refund['reason'] == 3)
                                不想买了
                            @endif
                        </i>
					</p>
					<p class=""> 
						<i class="">处理方式:</i>
						<i class="pull-right c-black">
                            @if ($refund['type'] == 0)
                                仅退款
                            @endif
                        </i>
					</p>
					<p class=""> 
						<i class="">货物状态:</i>
						<i class="pull-right c-black">
                            @if ($refund['order_status'] == 0)
                                未收到货
                            @elseif ($refund['order_status'] == 1)
                                已收到货
                            @endif
                        </i>
					</p>
					<p class=""> 
						<i class="">退款金额:</i>
						<i class="pull-right c-orange">￥{{$refund['amount']}}</i>
					</p>
				</div>
				<a href="/shop/order/refundMessages/{{$wid}}/{{$order['id']}}/{{$refund['pid']}}/{{$refund['prop_id']}}" class="block-item text-center c-blue-3283FA">
					查看完整协商记录
				</a>
			</div>
    		<div class="js-action-container action-container action-container-safeguard">
			    <a href="/shop/order/detail/{{$order['id']}}" class="js-info-btn btn btn-block btn-green" data-type="back_order">订单详情</a>
				<form class="js-apply-form">
	
					<div class="action-container">
						@if($order['refund_status'] == 1)
						    <a href="/shop/order/refundDel/{{$wid}}/{{$order['id']}}/{{$refund['pid']}}/{{$refund['id']}}" class="js-info-btn btn-white js-submit btn btn-block">撤销退款申请</a>
	                    @endif
					</div>
	
				</form>
    		</div>
		</div>    

		</div>
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        console.log('{{$refundEndTimestamp}}');
        var endTimestamp = '{{$refundEndTimestamp}}';
        var startTimestamp = '{{ $now }}';
        var end = new Date(endTimestamp).getTime()/1000;
        var now = new Date(startTimestamp).getTime()/1000;
    	var intDiff = end - now;
	    function timer(intDiff){
	        window.setInterval(function(){
	        var day=0,
		        hour=0,
		        minute=0,
		        second=0;//时间默认值    
	        if(intDiff > 0){
		        day = Math.floor(intDiff / (60 * 60 * 24));
		        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
		        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
		        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
		    }else{
                //当时间耗尽，执行退款 todo:暂时没有7天自动退款功能 倒计时结束 显示请立即退款
                $("span.c-orange").html('00秒');
                return;
		    }
	        if (minute <= 9) minute = '0' + minute;
	        if (second <= 9) second = '0' + second;
	        $("span.c-orange").html('<font>'+day+'天'+hour+'时'+minute+'分'+second+'</font>秒');
	        	intDiff--;
	        }, 1000);
	    } 
	    $(function(){
	        timer(intDiff);
	    });
    </script>
@endsection
