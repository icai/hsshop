@extends('shop.common.marketing')
@section('title', '留言板列表')   
@section('head_css')
<script src='{{ config('app.source_url') }}mobile/js/rem.js'></script>
<link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/member_researchList.css">
@endsection
@section('main')
<body>
    <div id="app">
        <input type="hidden" name="wid" value="{{$wid}}">
        <input type="hidden" name="host" value="{{config('app.url')}}">
        <div class="list" v-if="list.length>0">
            <a class="item fb" :href="targetURL+item.id+'/'+item.times" v-for="(item,idx) in list" :key="idx">
                <div class="title">
                    <p class="head">${item.title}</p>
                    <p>${item.created_at}</p>
                </div>
                <img src="{{ config('app.source_url') }}shop/images/yjt@2x.png" alt="arrow">
            </a>
        </div>
        <div v-else="list.length==0">
            <p class="noInfo">暂无留言记录</p>
        </div>
    </div>
    
<!-- 页面加载开始 -->
<div class="pageLoading">
    <img src="{{ config('app.source_url') }}shop/images/loading.gif">
</div>
<!-- 页面加载结束 -->
</body>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script>
    var data = {!! json_encode($data) !!}
</script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/member_researchList.js"></script>
@endsection