@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/earnings.css">
@endsection
@section('main')
<div class="container">
    <div class="earning-header">
        <div class="wd_40 header-item">订单号</div>
        <div class="wd_30 header-item">收益</div>
        <div class="wd_30 header-item">状态</div>
    </div>
    <div class="js-earning-body">
        @forelse($income as $val)
        <div class="earning-body-item">
            <div class="wd_40 header-item">{{$val['order']['oid']?? 0}}</div>
            <div class="wd_30 header-item">+{{$val['money']}}</div>
            <div class="wd_30 header-item status-light">
                @if($val['status'] == 0)
                    待提现
                @elseif($val['status'] == 1)
                    已到账
                @elseif($val['status'] == -1)
                    已流失
                @endif
            </div>
        </div>
        @endforeach
    </div>
        
</div>
    @include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript"  src="{{config('app.source_url')}}shop/js/earnings.js"></script>
@endsection