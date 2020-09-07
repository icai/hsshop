@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/selectAccount.css">
@endsection
@section('main')
<div class="container">
    <div>
        @forelse($bank as $val)
        <div class="j_item">
            <a href="/shop/distribute/withdrawal?id={{$val['id']}}">
                <div class="j_item_logo">
                    <img src="{{$val['logo']}}">
                </div>
                <div class="j_item_content">
                    <p class="bank_name"><span class="b_name">{{$val['bank_name']}}</span><span>（尾号{{$val['account']}}）</span></p>
                    <p class="bank">{{$val['name']}}</p>
                </div>
                <div class="j_item_dt j_item_dt_2">
                    <input class="check" type="radio" name="bank">
                    <span></span>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="add_account">
        <a href="/shop/distribute/addAccount">
            <i class="add_icon">+</i>
            <span>添加账户</span>
        </a>
    </div>
    <div class="manage">
        <a href="/shop/distribute/manageAccount">管理账户</a>
    </div>
</div>
    @include('shop.common.footer')
@endsection
@section('page_js')
@endsection