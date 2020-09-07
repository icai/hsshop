@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/addAccount.css">
@endsection
@section('main')
<div class="container">
    <div class="add">
        <form>
            <ul class="nav">
                <li class="active"><a href="/shop/distribute/addAccount">添加银行卡</a></li>
                <li><a href="/shop/distribute/addAlipay">添加支付宝</a></li>
            </ul>
            <div class="order-related">
                <div class="block block-list list-vertical">
                    <a class="block-item clearfix item1" href="javascript:void(0);">
                        <p class="font-size-14 left">选择银行</p>
                        <p class="font-size-14 right"></p>
                    </a>
                    <div class="form_input">
                        <label class="label_title">卡号</label>
                        <input class="text_input" type="text" name="account" placeholder="填写卡号">
                    </div>
                    <div class="form_input">
                        <label class="label_title">姓名</label>
                        <input class="text_input" type="text" name="name" placeholder="开户人姓名">
                    </div>
                </div>
            </div>
            <button class="btn" href="javascript:void(0);">保存</button>
        </form>
    </div>
    <div class="bank_list">
        <div class="j_item">
            @forelse($bank as $val)
            <a href="javascript:void(0);">
                <div class="j_item_logo">
                    <img src="{{$val['logo']}}">
                </div>
                <div class="j_item_content">
                    <p class="bank_name">{{$val['name']}}</p>
                </div>
                <div class="j_item_dt">
                    <input class="check" type="radio" name="bank">
                    <span></span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript"> 
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/until.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/addAccount.js"></script>
@endsection