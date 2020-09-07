@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/addAlipay.css">
@endsection
@section('main')
<div class="container">
    <div class="add">
        <input type="hidden" name="logo" value="{{ config('app.source_url') }}shop/images/timg.jpg">
        <form>
            <ul class="nav">
                <li><a href="/shop/distribute/addAccount">添加银行卡</a></li>
                <li class="active"><a href="/shop/distribute/addAlipay">添加支付宝</a></li>
            </ul>
            <div class="order-related">
                <div class="block block-list list-vertical">
                    <div class="form_input">
                        <label class="label_title">账号</label>
                        <input class="text_input" type="text" name="account" placeholder="填写支付宝账号">
                    </div>
                    <div class="form_input">
                        <label class="label_title">姓名</label>
                        <input class="text_input" type="text" name="name" placeholder="账号真实姓名">
                    </div>
                </div>
            </div>
            <button class="btn" href="javascript:void(0);">保存</button>
        </form>
    </div>
</div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript"> 
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/until.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/addAlipay.js"></script>
@endsection