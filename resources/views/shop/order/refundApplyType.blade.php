@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refundApplyType.css">
@endsection
@section('main')
	<div class="apply_afterSales_type">
		<div class="contents">
            <div class="refund_list mg_btm_10" @click="returnGoods">
                <div class="refund_img">
                    <img src="{{ config('app.source_url') }}shop/images/tuihuotuikuan11.png">
                </div>
                <div class="refund_content">
                    <h3 class="refund_title font-size-16 text-danger">我要退货退款</h3>
                    <p class="refund_desc font-size-12 text-cancel">已收到货，需要退还已收到的货物</p>
                </div> 
            </div>

            <div class="refund_list mg_btm_10" @click="refunds">
                <div class="refund_img">
                    <img src="{{ config('app.source_url') }}shop/images/tuikuan11.png">
                </div>
                <div class="refund_content">
                    <h3 class="refund_title font-size-16 text-danger">我要退款（无需退货）</h3>
                    <p class="refund_desc font-size-12 text-cancel">未收到货，或与商家协商之后申请</p>
                </div> 
            </div>
		</div>
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script type="text/javascript">
        var wid = {{session('wid')}};
        var oid = '{{$oid}}';
        var pid = '{{$pid}}';
        var isEdit = '{{$isEdit}}';
        var imgUrl = "{{ imgUrl() }}";
        var _token = $('meta[name="csrf-token"]').attr('content');
		var propID = '{{$propID}}';
    </script>
    
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/refundApplyType.js"></script>
@endsection