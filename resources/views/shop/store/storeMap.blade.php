@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/storeMap.css">
@endsection
@section('main')
<body onload="DefaultLocation()">
    <div class="container">
    	<div class="lookAll">查看全部</div>
        <div id="map"></div>
        <div class="storeIntro">
            <div class="store_item_address">
                <img class="store_item_img" src="">
                <div class="storeContent">
                    <span class="store_item_title">
                    </span>
                    <p class="tel"></p>
                </div>
            </div>
            <a class="phone"></a>
        </div>
    </div>
</body>
    <script type="text/javascript">
        var store = {!! $storeJson or '' !!};
    </script>
    @include('shop.common.footer')
@endsection
@section('page_js')
<!--地图接口-->
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=FLIBZ-34ELI-C6WGO-5HIAO-6QBPE-KKB2D"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript">
    var source = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/storeMap.js"></script>
@endsection
