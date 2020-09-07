<!--退款详情包括协商记录-->
@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base1.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/safeguard_1c5f2b2c598b88d2eceffd92bf496cfe.css">
@endsection
@section('main')
    <div class="container " style="min-height: 736px;">
		<div class="content clearfix">
			<div class="block-list block">
                <div class="block-item">
	            	<p class="">退款编号
	                	<span class="pull-right">{{$refund['id']}}</span>
	            	</p>
	            	<p class="">订单编号
	                	<span class="pull-right">{{$order['oid']}}</span>
	            	</p>
        		</div>
                <div class="block-item record-name-card name-card name-card-3col">
                    <div class="thumb">
                        <img src="{{ imgUrl($product['img']) }}" alt="{{$product['title']}}">
                    </div>
                    <div class="detail">
                        <h3 class="font-size-12 ellipsis">{{$product['title']}}</h3>
                        <p class="font-size-12">{{$product['spec']}}</p>
                        <p class="font-size-12 line-height-18">
                                <span class="c-orange">
                                    @if ($order['status'] == 0)
                                        代付款
                                    @elseif ($order['status'] == 1)
                                        待发货
                                    @elseif ($order['status'] == 2)
                                        待收货
                                    @endif
                                </span>
                        </p>
                    </div>
                    <div class="right-col">
                        <p class="line-height-18">
                            <span class="pull-right c-black">￥{{$product['price']}}</span>
                        </p>
                        <p>
                            <span class="pull-right c-gray-darker">x{{$product['num']}}</span>
                        </p>
                    </div>
                </div>
            </div>

            @foreach($messages as $v)
	        <div class="talk talk-right">
	            <p class="time">{{$v['created_at']}}</p>
	            <div class="talk-content">
	                <div class="">
	                    <span class="">
                            @if ($v['is_seller'] == 1)
                                商家
                            @else
                                买家
                            @endif
                        </span>
	                </div>
	                <hr>
	                <div class="">
                        @if ($v['status'] == 1)
                            <p>拒绝了本次退款申请</p>
                            <p>拒绝原因：{{$v['content']}}</p>
                        @elseif ($v['status'] == 2)
                            <p>同意退款给买家，本次维权结束</p>
                            <p>退款金额：{{$v['content']}}</p>
                        @elseif ($v['status'] == 3)
                            <p>买家撤销本次退款，本次维权结束</p>
                        @else
                            <p>留言：{{$v['content']}}</p>
                        @endif
                        <div class="message-img">
                            @if ($v['imgs'])
                                @foreach(explode(',', $v['imgs']) as $img)
                                    <img src="{{imgUrl($img)}}" width="40" height="40"/>
                                @endforeach
                            @endif
                        </div>
                    </div>
	                <span class="empty-trigon"></span>
	            </div>
	        </div>
            @endforeach

	        <div class="talk talk-right" style="margin-bottom: 60px;">
	            <p class="time">{{$refund['created_at']}}</p>
	            <div class="talk-content">
	                <div class="">
	                    <span class="">买家</span>
	                </div>
	                <hr>
	                <div class="">
	                    <p>发起了退款申请,等待商家处理</p>
	                    <p>
                            退款原因：
                            @if ($refund['reason'] == 0)
                                其他
                            @elseif ($refund['reason'] == 1)
                                配送信息错误
                            @elseif ($refund['reason'] == 2)
                                买错商品
                            @elseif ($refund['reason'] == 3)
                                不想买了
                            @endif
                        </p>
	                    <p>
                            处理方式：
                            @if ($refund['type'] == 0)
                                仅退款
                            @endif
                        </p>
	                    <p>
                            货物状态：
                            @if ($refund['order_status'] == 0)
                                未收到货
                            @elseif ($refund['order_status'] == 1)
                                已收到货
                            @endif
                        </p>
	                    <p>退款金额：{{$refund['amount']}}</p>
	                    <p>退款说明：{{$refund['remark']}}</p>
	                    <p>联系电话：{{$refund['phone']}}</p>
	                </div>
	                <span class="empty-trigon"></span>
	            </div>
	        </div>
	    	<div class="bottom-fix bottom-log-action clearfix">
                @if($refund['status'] == 1)
	                <a class="btn btn-block btn-green font-size-16" href="/shop/order/refundAddMessage/{{$refund['id']}}/{{$wid}}/{{$order['id']}}/{{$product['product_id']}}">我要留言</a>
                @endif
	      </div>  
		</div>
	</div>
@endsection
