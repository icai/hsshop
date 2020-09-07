@extends('shop.common.marketing')
@section('head_css')
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/coupon_promotecard_list_50269d6991d6f87edacb7fad2282a8c1.css">
@endsection
@section('main')
<body class=" ">
    <input type="hidden" class="wid" value="{{$wid}}">
    <div class="container " style="min-height: 667px;">
       
        <div class="content no-sidebar">
            <div class="content-body">
                <!-- 无优惠券情况下展示 -->
                <div class="promote-card-list-box">
                    <div class="promote-nav-box">
                        <div class="tabber-ios-gray tabber tabber-ios">
                            <a class="active tab" data-id="0" data-href="/shop/member/couponList/{{$wid}}/valid"><span>未使用</span></a>
                            <a class="tab" data-id="1" data-href="/shop/member/couponList/{{$wid}}/invalid"><span>已失效</span></a>
                        </div>
                    </div>
                    <ul class="promote-card-list">
                        
                    </ul>
                    <ul class="promote-card-list invalid" style="display: hide">
                        
                    </ul>
                    <div class="empty-coupon-list center">
                        <div style="margin: 115px 0 12px;">
                            <span class="font-size-16 c-black">神马，我还没有券？</span>
                        </div>
                        <div>
                            <span class="font-size-12 c-gray-dark">怎么能忍？</span>
                        </div>
                        <div style="margin-top: 50px;">
                            <a href="/shop/index/{{session('wid')}}" class="tag tag-big" style="padding:8px 49px;color:#F72F37;border-color:#F72F37;">马上去领取</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="shop-nav"></div>
        </div>
    </div>
    <div class="search-bar" style="display:none;">
        <form class="search-form" action="/v2/search" method="GET">
            <input type="search" class="search-input" placeholder="搜索商品" name="q" value="">
            <input type="hidden" name="kdt_id" value="18896693">
            <a class="js-search-cancel search-cancel" href="javascript:;">取消</a>
            <span class="search-icon"></span>
            <span class="close-icon hide"></span>
        </form>
        <div class="history-wrap center">
            <ul class="history-list search-recom-list js-history-list clearfix"></ul>
            <a class="tag tag-clear js-tag-clear c-gray-darker hide" href="javascript:;">清除历史搜索</a></div>
    </div>
<!-- 页面加载开始 -->
<div class="pageLoading">
    <img src="{{ config('app.source_url') }}shop/images/loading.gif">
</div>
<!-- 页面加载结束 -->
</body>
@include('shop.common.footer') 
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/js/index_kg1fntrz.js"></script>
@endsection