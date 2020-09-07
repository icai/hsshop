@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css')
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cart_d4c65a42c9967641395785e90c4463a7.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/book_bookList.css"  media="screen">
@endsection
@section('main')
	@if($list['data'])
	<div class="app" v-cloak >
		<div class="bookList_banner">
			@if(isset($bookDatas['banner_img']) && $bookDatas['banner_img'])
			<img src="{{ $bookDatas['banner_img'] }}" alt="" />
			@else
			<img src="{{ config('app.source_url') }}shop/images/book_detail.png" alt="" />
			@endif
		</div>
		@foreach($list['data'] as $val)
		<div class="bookList_content">
			<div><span>{{ $val['created_at'] }}预约详情</span><span>@if($val['status'] == 1) 等待客服处理 @elseif($val['status'] == 2) 已确定 @else 已拒绝 @endif</span></div>
			<div>
				@if(is_array($val['form_content']))
					@foreach($val['form_content'] as $v)
					<p><label for="">{{ $v['ykey'] }}：</label><span>{{ $v['yval'] }}</span></p>
					@endforeach
				@endif
				<p><label for="">备注：</label><span>{{ $val['remark'] }}</span></p>
				<p><label for="">商家留言：</label><span>{{ $val['content'] }}</span></p>
			</div>
			<div class="bookList_ipt">
				@if($val['status'] == 1)
				<input class="b_revise" type="submit" value="修改订单" data-id="{{ $val['id'] }}" /> <input class="b_delete" type="submit" value="删除订单" data-id="{{ $val['id'] }}"/>
				@else
				<input class="b_delete" type="submit" value="删除订单" data-id="{{ $val['id'] }}"/>
				@endif
			</div>
		</div>
	</div>
	@endforeach
	@else
	<div id="app" v-cloak >
		<div class="bookList_banner">
			@if(isset($bookDatas['banner_img']) && $bookDatas['banner_img'])
			<img src="{{ $bookDatas['banner_img'] }}" alt="" />
			@else
			<img src="{{ config('app.source_url') }}shop/images/book_detail.png" alt="" />
			@endif
		</div>
		<div class="no-result widget-list-empty bookList_content" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据
		</div>
	</div>
	@endif

@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var wid = '{{ $wid }}';
    var bookId = '{{ $bookId }}';
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--当前js-->
<script src="{{ config('app.source_url') }}shop/js/book_bookList.js"></script>
@endsection
