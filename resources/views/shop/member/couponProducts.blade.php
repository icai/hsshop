@extends('shop.common.marketing')
@section('head_css')
<link rel="shortcut icon" href="public/hsadmin/images/icn_alert_success.png" />
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase.css" media="screen">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/goods_list.css" media="screen">
@endsection
@section('main')
<div class="container" style="width: 100%;min-height: 100%;">
    <div class="block block-list">
        <ul>
            @forelse($list as $v)
                <li class="flex_star" data-href="{{URL('/shop/product/detail/' . $wid . '/' . $v['id'])}}">
                    <img src="{{ imgUrl() }}{{$v['img']}}" width="80" height="80"/>
                    <div class="goodsNews">
                        <p class="introduce">{{$v['title']}}</p>
                        <p class="price">{{$v['price']}}</p>
                    </div>
                </li>
            @empty
                <div class="noticeA">暂无数据</div>
            @endforelse
        </ul>
    </div>
</div>
@include('shop.common.footer') 
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}shop/js/goods_list.js" type="text/javascript" charset="utf-8"></script>
@endsection