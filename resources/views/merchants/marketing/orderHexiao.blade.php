@extends('merchants.default._layouts') 
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/orderHexiao.css" /> 
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection 
@section('slidebar') 
@include('merchants.marketing.slidebar') 
@endsection 
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
            </li>
            <li>
                <a href="javascript:;">验证工具</a>
            </li>
        </ul>
    </div>
</div>
@endsection 
@section('content')
<div class="content">
   <div class="content-page">
    <form method='get' action=''>
        <div class="header">
            <div class="header-left">到店自提</div>
            <input type="text" name="code" maxlength="30" value="{{ request('code') }}" class="search-input pull-left" placeholder="请输入或搜索优惠核销码（11位数字）">
            <button class="search-btn J_search-btn" ></button>
        </div>
    </form>
    @if($detail)
    <div class="show-box">
        <div class="show-box ">
            @foreach($detail['orderDetail'] as $product)
            <a href="/shop/preview/{{ session('wid') }}/{{ $product['product_id'] }}">
		        <div class="hexiao-info clearFix">
		            <div class="img-box">
		                <img src="{{ imgUrl() }}{{ $product['img'] }}" alt="">
		            </div>
		            <div class="hexiao-desc">
		                <p>{{ $product['title'] }}</p>
		                <p>¥ {{ $product['price'] }} * {{ $product['num'] }} 件</p>
		                <div class="is_weiquan">
			                @if($product['status_string'])
			                <p style="color:blue;" class="is_tuikuan">{{ $product['status_string'] }}</p>
			                @endif
		                </div>
		            </div>
		        </div>
            </a>
            @endforeach
            <div class="hexiao-desc-list">
                <p>
                    <strong>买家留言：</strong>
                    {{ $detail['buy_remark'] }}
                </p>
                <p>
                    <strong>提货地址：</strong>
                    {{ $detail['ziti']['orderZiti']['province_title'] or '' }}{{ $detail['ziti']['orderZiti']['city_title'] or '' }}{{ $detail['ziti']['orderZiti']['area_title'] or '' }}{{ $detail['ziti']['orderZiti']['address'] or '' }}
                </p>
                <p>
                    <strong>提货时间：</strong>
                    {{ $detail['ziti']['ziti_datetime'] or '' }}
                </p>
                <p>
                    <strong>提货人：</strong>
                    {{ $detail['ziti']['ziti_contact'] or '' }}  {{ $detail['ziti']['ziti_phone'] or '' }}
                </p>
            </div>
            <div class="btn-box">
                @if($detail['status'] == 2)
                <button type="button" class="verify-button btn-disabled" ><span>已核销</span></button>
                @elseif($detail['status'] == 1)
                <button type="button" class="J_verify-button verify-button" data-oid="{{ $detail['id'] }}"><span>核销</span></button>
                <p class="hexiao-tips">卡券验证后不可撤回</p>
                @endif
            </div>
        </div>
    </div>
    @elseif(empty($detail) && request('code'))
    <div class="empty show-box">没有该核销商品，请重新输入!</div>
    @endif
   </div>
</div>
@endsection 
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/orderHexiao.js"></script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
@endsection