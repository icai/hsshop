@extends('shop.common.template')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/manageAccount.css">
@endsection
@section('main')
<div class="container">
    <div>
        <div class="j_item">
            @forelse($bank as $val)
            <a class="bank" href="javascript:void(0);" data-id="{{$val['id']}}">
                <div class="j_item_logo">
                    <img src="{{$val['logo']}}">
                </div>
                <div class="j_item_content">
                    <p class="bank_name"><span class="b_name">{{$val['bank_name']}}</span><span>（尾号{{$val['account']}}）</span></p>
                    <p class="bank">{{$val['name']}}</p>
                </div>
                <div class="j_item_dt">
                    <input class="check" type="checkbox" name="bank">
                    <span></span>
                </div>
            </a>
                @endforeach
        </div>
        <div class="no_data">
            请先添加银行卡
        </div>
    </div>
    <button class="btn" href="javascript:void(0);">删除</button>
</div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript"> 
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/until.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/manageAccount.js"></script>
@endsection